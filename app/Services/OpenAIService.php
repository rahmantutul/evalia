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

    public function identifyMatchedKnowledgeBase(string $transcription, array $kbMapping): array
    {
        if (empty($kbMapping)) {
            return [];
        }
        $kbList = '';
        foreach ($kbMapping as $index => $kb) {
            $keywords = implode(', ', array_filter($kb['keywords'] ?? []));
            if (empty($keywords)) continue; 
            $kbList .= "ID:{$index} [{$keywords}]\n";
        }

        if (empty($kbList)) {
            return []; 
        }

        $systemPrompt = <<<SYSTEM
You are a KB matcher. Match a conversation snippet to Knowledge Base IDs by keywords only.

Rules:
- Return ONLY the IDs whose keywords closely match topics discussed in the text.
- IGNORE: greetings, farewells, "thank you", hold phrases, and small-talk.
- If nothing matches, return [].

Output: raw JSON integer array only. Example: [0, 2]
SYSTEM;

        $userPrompt = <<<USER
KB LIST (ID [keywords]):
{$kbList}
TEXT:
{$transcription}

Return matching IDs as JSON array.
USER;

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post($this->baseUrl, [
                    'model'       => $this->model,
                    'messages'    => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user',   'content' => $userPrompt],
                    ],
                    'temperature' => 0,
                    'max_tokens'  => 50,
                ]);

            if ($response->failed()) {
                Log::error('[Stage 1] API failed.', ['status' => $response->status()]);
                return [];
            }

            $content = $response->json()['choices'][0]['message']['content'] ?? '[]';
            $content  = preg_replace('/```json\s*|```/', '', trim($content));

            $result = json_decode($content, true);
            return is_array($result) ? array_values(array_filter($result, 'is_int')) : [];

        } catch (Exception $e) {
            Log::error('[Stage 1] Exception: ' . $e->getMessage());
            return [];
        }
    }


    public function evaluateTranscription(string $transcription, string $kbContext, array $companyPolicies = [], string $companyContext = '', array $companyRisks = [], array $extractions = []): array
    {
        if (empty($this->apiKey)) {
            throw new Exception('Evaluation service is currently unavailable (API key missing).');
        }

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

        $riskSection = '';
        if (!empty($companyRisks)) {
            $riskSection = "\n=== POTENTIAL RISKS TO MONITOR ===\n";
            $riskSection .= "Check the call transcript against the following risk items. If any occur, flag them in 'risk_assessment'.\n\n";
            foreach ($companyRisks as $i => $risk) {
                $clean = preg_replace('/^[\d\x{0660}-\x{0669}]+[\s.\-\)]+/u', '', trim((string) $risk));
                $riskSection .= ($i + 1) . ". " . $clean . "\n";
            }
        }

        $extractionSection = '';
        if (!empty($extractions)) {
            $extractionSection = "\n=== CUSTOM DATA EXTRACTION ===\n";
            $extractionSection .= "Extract the following specific data points from the transcript. For each point, return the value in the specified type.\n\n";
            foreach ($extractions as $i => $ext) {
                $extractionSection .= ($i + 1) . ". Label: \"{$ext['description']}\" | Expected Type: {$ext['type']}\n";
            }
        }

        $systemPrompt = $this->buildEvaluationSystemPrompt();
        $userPrompt   = $this->buildEvaluationUserPrompt($transcription, $kbContext, $policySection, $companyContext, $riskSection, $extractionSection);

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
            $decoded = json_decode($content, true);

            if (!is_array($decoded)) {
                throw new Exception('OpenAI returned invalid JSON.');
            }

            return $decoded;

        } catch (Exception $e) {
            throw $e;
        }
    }

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

=== NOTEBOOK CONTEXT STRUCTURE (read carefully) ===
The Knowledge Base section in the prompt is structured as follows:

  === NOTEBOOK: <Title> ===
  <KB content>
  Reference Pairs:
    Pair N: <the specific Customer/Agent exchange from the transcript>

Each NOTEBOOK entry comes with "Reference Pairs" — these are the EXACT transcript exchanges
that were pre-matched to that KB. This means:
- When evaluating Pair N against a KB, ONLY use the KB entry listed under that pair's notebook.
- Different pairs may reference DIFFERENT notebooks — do NOT apply all notebooks to all pairs.
- If a pair has no reference notebook, skip it for notebook_analysis.

=== NOTEBOOK ANALYSIS RULES (critical) ===
- Only include entries where the agent gives a specific factual answer that CAN be checked against the Knowledge Base.
- DO NOT include: greetings, closings, "thank you", "one moment please", or generic procedural phrases.
- DO NOT include simple yes/no answers that lack factual substance.
- For each included entry: quote the customer's question and the agent's exact answer.
- Use the "Reference Pairs" mapping above to determine WHICH KB to check each exchange against.
- Evaluate as: ✅ Correct (matches KB), ❌ Incorrect (contradicts KB), ⚠️ Partially Correct (incomplete match).
- Set "notebook_name" to the title of the NOTEBOOK that is the reference for that exchange.
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

=== RISK ASSESSMENT RULES (MANDATORY) ===
Check the transcript against the "POTENTIAL RISKS TO MONITOR" list.
- For EVERY risk identified in the transcript, add an entry to the `risk_assessment` array.
- If NO risks from the list are detected, return `risk_flag: "No"` and an empty `risk_assessment` array [].
- If ANY risk is detected, set `risk_flag: "Yes"`.
- Each entry must include:
  - 'risk_title': The risk from the provided list.
  - 'detected': true / false.
  - 'evidence': exact transcript quote.
  - 'severity': 1-10.
  - 'impact': brief explanation.

=== CUSTOM DATA EXTRACTION RULES ===
If "CUSTOM DATA EXTRACTION" is provided:
- Extract the requested values precisely.
- If a value is not mentioned or cannot be determined, return null.
- Ensure the data type matches (e.g., if type is integer, return a number, not a string).
- Return an array of objects called `extracted_data`, where each object has:
  - 'label': The description provided in the request.
  - 'value': The extracted value (or null).
  - 'type': The requested type.
  - 'evidence': A short quote from the transcript confirming the extracted value.

=== OUTPUT ===
Return ONLY valid JSON matching this exact schema. Do not add any explanation outside the JSON:

{
    "agent_id": "The speaker tag for the AGENT (e.g. 'Speaker 1')",
    "customer_id": "The speaker tag for the CUSTOMER (e.g. 'Speaker 0')",
    "risk_flag": "Yes/No",
    "risk_reason": "Summary of all detected risks or 'No risk detected'",
    "risk_assessment": [
        {
            "risk_title": "Risk name from monitor list",
            "detected": true,
            "evidence": "quote",
            "severity": 8,
            "impact": "explanation"
        }
    ],
    "extracted_data": [
        {
            "label": "Description of data",
            "value": "Extracted value",
            "type": "string",
            "evidence": "Transcript quote"
        }
    ],
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

    private function buildEvaluationUserPrompt(string $transcription, string $kbContext, string $policySection, string $companyContext = '', string $riskSection = '', string $extractionSection = ''): string
    {
        $contextBlock = $companyContext ? "=== COMPANY CONTEXT ===\n{$companyContext}\n" : '';

        $noPolicyNote = empty(trim($policySection))
            ? "\n[NOTE: No company policies were provided. Return an empty array [] for policy_compliance. Do NOT invent generic policies.]\n"
            : '';

        return <<<USER
{$contextBlock}
{$policySection}{$noPolicyNote}
{$riskSection}
{$extractionSection}
=== KNOWLEDGE BASE CONTENT (for verification) ===
{$kbContext}

=== CALL TRANSCRIPT ===
{$transcription}

Evaluate this call thoroughly and objectively. Base every score on direct transcript evidence.
Return ONLY the JSON object described in the system instructions — no extra text.
USER;
    }
    public function identifyClientSegments(string $transcription): array
    {
        $systemPrompt = <<<SYSTEM
Identify the client’s timestamps only, group them into sequential from–to pairs representing continuous segments, calculate the duration for each segment (to - from), and return the results strictly in valid JSON format including from, to, and duration for each segment.
SYSTEM;

        $userPrompt = <<<USER
TRANSCRIPTION WITH TIMESTAMPS:
{$transcription}

Identify client segments and return JSON.
USER;

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post($this->baseUrl, [
                    'model'       => 'gpt-4o', // Using gpt-4o specifically for this task
                    'messages'    => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user',   'content' => $userPrompt],
                    ],
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 0,
                ]);

            if ($response->failed()) {
                Log::error('GPT Segment Identification failed.', ['status' => $response->status()]);
                return [];
            }

            $content = $response->json()['choices'][0]['message']['content'] ?? '{"segments": []}';
            $decoded = json_decode($content, true);
            
            return $decoded['segments'] ?? (is_array($decoded) ? $decoded : []);

        } catch (Exception $e) {
            Log::error('GPT Segment Identification Exception: ' . $e->getMessage());
            return [];
        }
    }
}
