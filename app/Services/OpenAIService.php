<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class OpenAIService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('services.openai.key') ?? env('OPENAI_API_KEY', '');
        $this->model   = config('services.openai.model') ?? env('OPENAI_MODEL', 'gpt-4o-mini');
        $this->baseUrl = 'https://api.openai.com/v1/chat/completions';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STAGE 1 — Identify relevant Knowledge Base entries
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Identify which Knowledge Base entries are genuinely relevant to the call.
     * Returns an array of integer indices from $kbMapping.
     */
    public function identifyMatchedKnowledgeBase(string $transcription, array $kbMapping): array
    {
        if (empty($kbMapping)) {
            return [];
        }

        $kbList = '';
        foreach ($kbMapping as $index => $kb) {
            $keywords = implode(', ', array_filter($kb['keywords'] ?? []));
            $kbList .= "ID: {$index} | Title: {$kb['name']} | Keywords: {$keywords}\n";
        }

        $systemPrompt = <<<SYSTEM
You are a precise call-quality assistant. Your ONLY task is to identify which Knowledge Base (KB) entries contain information that is **directly relevant** to the factual content of this phone call transcript.

Rules:
- Include a KB entry ONLY if an agent statement or customer question directly relates to its specific content.
- SKIP pure greetings, sign-off phrases, and generic social exchanges.
- SKIP KB entries about general etiquette unless there is a clear policy violation in the transcript.
- Return a JSON array of integer IDs. If nothing matches, return an empty array [].

Output format: raw JSON array only, e.g. [0, 2]
SYSTEM;

        $userPrompt = <<<USER
=== AVAILABLE KNOWLEDGE BASES ===
{$kbList}

=== CALL TRANSCRIPT ===
{$transcription}

Return the JSON array of relevant KB IDs.
USER;

        Log::debug('[OpenAI Stage 1] KB Identification prompt sent.', ['kb_count' => count($kbMapping)]);

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post($this->baseUrl, [
                    'model'       => $this->model,
                    'messages'    => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user',   'content' => $userPrompt],
                    ],
                    'temperature' => 0,   // Deterministic — no hallucination in selection
                    'max_tokens'  => 200, // IDs only, short output
                ]);

            if ($response->failed()) {
                Log::error('[OpenAI Stage 1] API call failed.', ['status' => $response->status(), 'body' => $response->body()]);
                return [];
            }

            $content = $response->json()['choices'][0]['message']['content'] ?? '[]';
            Log::debug('[OpenAI Stage 1] Response.', ['content' => $content]);

            // Strip markdown code fences if present
            $content = preg_replace('/```json\s*|```/', '', trim($content));

            $result = json_decode($content, true);
            return is_array($result) ? array_values(array_filter($result, 'is_int')) : [];

        } catch (Exception $e) {
            Log::error('[OpenAI Stage 1] Exception: ' . $e->getMessage());
            return [];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STAGE 2 — Deep evaluation of the full transcription
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Evaluate a call transcription against KB content and company policies.
     * Returns a structured associative array parsed from GPT's JSON response.
     */
    public function evaluateTranscription(string $transcription, string $kbContext, array $companyPolicies = [], string $companyContext = ''): array
    {
        if (empty($this->apiKey)) {
            Log::error('[OpenAI Stage 2] API key is missing.');
            throw new Exception('Evaluation service is currently unavailable (API key missing).');
        }

        // Build policy section — strip any existing numbering prefix to avoid "1. 1. Policy" duplication
        $policySection = '';
        if (!empty($companyPolicies)) {
            $policySection = "\n=== COMPANY POLICIES TO EVALUATE ===\n";
            $policySection .= "IMPORTANT: You MUST evaluate ALL " . count($companyPolicies) . " policies listed below.\n";
            $policySection .= "Produce exactly " . count($companyPolicies) . " entries in policy_compliance — one per policy, in order.\n\n";
            foreach ($companyPolicies as $i => $policy) {
                // Strip leading numbering like "1. ", "١. ", "(1) ", "1) " etc.
                $clean = preg_replace('/^[\d\x{0660}-\x{0669}]+[\s.\-\)]+/u', '', trim((string) $policy));
                $policySection .= ($i + 1) . ". " . $clean . "\n";
            }
        }

        $systemPrompt = $this->buildEvaluationSystemPrompt();
        $userPrompt   = $this->buildEvaluationUserPrompt($transcription, $kbContext, $policySection, $companyContext);

        Log::debug('[OpenAI Stage 2] Sending evaluation request.', [
            'model'             => $this->model,
            'transcription_len' => strlen($transcription),
            'kb_context_len'    => strlen($kbContext),
        ]);

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(180)
                ->post($this->baseUrl, [
                    'model'           => $this->model,
                    'messages'        => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user',   'content' => $userPrompt],
                    ],
                    'response_format' => ['type' => 'json_object'],
                    'temperature'     => 0.2,  // Low — more consistent, factual scoring
                    'max_tokens'      => 4096,
                ]);

            if ($response->failed()) {
                $body = $response->body();
                Log::error('[OpenAI Stage 2] API call failed.', ['status' => $response->status(), 'body' => $body]);
                throw new Exception('OpenAI evaluation failed: ' . $response->reason());
            }

            $content = $response->json()['choices'][0]['message']['content'] ?? '{}';
            Log::debug('[OpenAI Stage 2] Raw response content received.', ['length' => strlen($content)]);

            $decoded = json_decode($content, true);

            if (!is_array($decoded)) {
                Log::error('[OpenAI Stage 2] Failed to decode JSON response.', ['content' => $content]);
                throw new Exception('OpenAI returned invalid JSON.');
            }

            return $decoded;

        } catch (Exception $e) {
            Log::error('[OpenAI Stage 2] Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function buildEvaluationSystemPrompt(): string
    {
        return <<<'SYSTEM'
You are a senior call-center quality assurance (QA) analyst with 15+ years of experience evaluating agent performance.
Your job is to produce a comprehensive, objective, evidence-based evaluation of the call transcript provided.

=== GENERAL RULES ===
1. Base EVERY score on direct evidence from the transcript. Quote exact phrases as evidence.
2. Scores must be integers 0–10 unless stated otherwise.
3. Be strict but fair. A score of 10/10 means exceptional — not just adequate.
4. Identify the correct speaker tags for "agent" and "customer" from the transcript (e.g. "Speaker 0", "speaker_1").
5. The AGENT is the one answering questions, handling the call, and representing the company.
6. The CUSTOMER is the one calling with a request or inquiry.

=== NOTEBOOK ANALYSIS RULES (critical) ===
- Only include entries where the agent gives a specific factual answer that CAN be checked against the Knowledge Base.
- DO NOT include: greetings, closings, "thank you", "one moment please", or generic procedural phrases.
- DO NOT include simple yes/no answers that lack factual substance.
- For each included entry: quote the customer's question and the agent's exact answer.
- Evaluate as: ✅ Correct (matches KB), ❌ Incorrect (contradicts KB), ⚠️ Partially Correct (incomplete match).
- If no KB content was provided or no factual exchange exists, return an empty array [].

=== SCORING METHODOLOGY ===
- Think step by step: read the transcript, find evidence, then assign the score.
- Score 9–10: Outstanding performance or full compliance.
- Score 7–8: Good performance with minor gaps.
- Score 5–6: Acceptable but with clear deficiencies.
- Score 3–4: Below expectations with significant gaps.
- Score 1–2: Poor performance, multiple failures.
- Score 0: Complete absence of the attribute.

=== POLICY COMPLIANCE RULES (MANDATORY) ===
⚠️  You MUST produce EXACTLY one entry in policy_compliance for EVERY policy listed in the prompt.
    Count the policies. Your array length MUST match. Never skip, merge, or add policies.

- "Meets policy"            → Agent demonstrably followed this rule (quote the evidence).
- "Does not meet policy"   → Agent demonstrably violated this rule (quote the evidence).
- "Cannot determine"       → No evidence in transcript to judge this rule either way.
- "Not applicable"         → This call type made this policy completely irrelevant (use sparingly).
- 'title'      → Short version of the policy text (≤ 60 characters), in its original language.
- 'requirement'→ Full restatement of what the policy demands.
- 'action'     → What the agent actually did, quoting transcript phrases where possible.
- 'reference'  → "Policy 1", "Policy 2" … matching the list order.
- 'section'    → Category: Communication | Privacy | Identity Verification | Response Time | Escalation | Closing | Other.
- 'confidence' → Float 0.0–1.0 representing your certainty.
- Policies may be in Arabic — evaluate in Arabic context, keep titles in the original language.

=== OUTPUT ===
Return ONLY valid JSON matching this exact schema. Do not add any explanation outside the JSON:

{
    "agent_id": "The speaker tag for the AGENT (e.g. 'Speaker 1')",
    "customer_id": "The speaker tag for the CUSTOMER (e.g. 'Speaker 0')",
    "score": 85,
    "risk_flag": "No",
    "risk_reason": "Briefly explain if risk is present, otherwise 'No risk detected'",
    "summary": "3–5 sentence factual summary: what was the call about, what happened, what was resolved or left open",
    "call_outcome": "One-line outcome, e.g. 'Issue resolved — customer confirmed satisfaction', 'Appointment scheduled for follow-up', 'Complaint escalated to supervisor'",

    "agent_professionalism": {
        "total_score": { "percentage": 85, "score": 42, "max_score": 50 },
        "speech_characteristics": {
            "volume":       { "loudness_class": "Normal", "optimal_loudness_percentage": 90 },
            "speed":        130,
            "pauses":       3,
            "tone_analysis": { "friendly": 85, "confident": 80, "empathetic": 75 }
        },
        "customer_satisfaction":          { "score": 8, "evidence": "exact quote", "reasoning": "why this score", "determination": "Satisfied" },
        "professionalism":                { "score": 9, "evidence": "exact quote", "reasoning": "why this score", "determination": "Professional" },
        "tone_consistency":               { "score": 8, "evidence": "exact quote", "reasoning": "why this score", "determination": "Consistent" },
        "polite_language_usage":          { "score": 9, "evidence": "exact quote", "reasoning": "why this score", "determination": "Polite" },
        "configured_standards_compliance":{ "score": 8, "evidence": "exact quote", "reasoning": "why this score", "determination": "Compliant" },
        "linguistic_analysis":            { "formal_language_percentage": 80 }
    },

    "agent_assessment": {
        "total_score": { "percentage": 80 },
        "communication":     { "score": 8, "evidence": "exact quote", "reasoning": "why this score", "determination": "Good" },
        "problem_solving":   { "score": 7, "evidence": "exact quote", "reasoning": "why this score", "determination": "Adequate" },
        "technical_knowledge":{ "score": 9, "evidence": "exact quote", "reasoning": "why this score", "determination": "Strong" },
        "efficiency":        { "score": 8, "evidence": "exact quote", "reasoning": "why this score", "determination": "Efficient" }
    },

    "agent_cooperation": {
        "total_score": { "percentage": 88 },
        "agent_proactive_assistance": { "score": 9, "evidence": "exact quote", "reasoning": "why this score", "determination": "Proactive" },
        "agent_responsiveness":       { "score": 9, "evidence": "exact quote", "reasoning": "why this score", "determination": "Responsive" },
        "agent_empathy":              { "score": 8, "evidence": "exact quote", "reasoning": "why this score", "determination": "Empathetic" },
        "effectiveness":              { "score": 9, "evidence": "exact quote", "reasoning": "why this score", "determination": "Effective" }
    },

    "notebook_analysis": [
        {
            "question":                    "Customer's exact inquiry",
            "answer":                      "Agent's exact response",
            "evaluation":                  "✅ Correct",
            "KBtext":                      "Relevant text from the Knowledge Base",
            "notebook_name":               "KB entry title",
            "confidence_level":            0.95,
            "matching_topics":             ["topic1", "topic2"],
            "matching_transcript_sections":["verbatim relevant quote from transcript"]
        }
    ],

    "policy_compliance": [
        {
            "title":       "Policy Name",
            "requirement": "What the policy requires",
            "action":      "What the agent actually did",
            "evaluation":  "Meets policy",
            "reference":   "Policy code or identifier",
            "confidence":  0.9,
            "topics":      ["topic"],
            "section":     "Category"
        }
    ]
}
SYSTEM;
    }

    private function buildEvaluationUserPrompt(string $transcription, string $kbContext, string $policySection, string $companyContext = ''): string
    {
        $contextBlock = $companyContext ? "=== COMPANY CONTEXT ===\n{$companyContext}\n" : '';

        $noPolicyNote = empty(trim($policySection))
            ? "\n[NOTE: No company policies were provided. Return an empty array [] for policy_compliance. Do NOT invent generic policies.]\n"
            : '';

        return <<<USER
{$contextBlock}
{$policySection}{$noPolicyNote}
=== KNOWLEDGE BASE CONTENT (for verification) ===
{$kbContext}

=== CALL TRANSCRIPT ===
{$transcription}

Evaluate this call thoroughly and objectively. Base every score on direct transcript evidence.
Return ONLY the JSON object described in the system instructions — no extra text.
USER;
    }
}
