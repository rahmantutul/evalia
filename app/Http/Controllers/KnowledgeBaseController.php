<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KnowledgeBase;
use App\Models\Company;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Services\KnowledgeService;
use App\Services\OpenAIService;

class KnowledgeBaseController extends Controller
{
    protected $openai;

    public function __construct(OpenAIService $openai)
    {
        $this->openai = $openai;
        // $this->middleware('auth.api'); 
    }

    /**
     * Test the KB filtering logic without calling GPT.
     */
    public function knowledgeBaseSearch(Request $request, KnowledgeService $service)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'query'      => 'required|string|min:2|max:1000',
        ]);

        $companyId = (int) $request->input('company_id');
        $queryText = (string) $request->input('query');

        $meta = $service->getRelevantContextWithMeta($companyId, $queryText);

        return response()->json([
            'success'        => true,
            'query'          => $queryText,
            'keywords_found' => $meta['keywords_found'],
            'matched'        => $meta['matched'],
            'result'         => $meta['context'],
        ]);
    }

    // -------------------------------------------------------
    //  CRUD
    // -------------------------------------------------------

    public function knowledgeBaseList(Request $request)
    {
        $query = KnowledgeBase::with('company')->orderBy('created_at', 'desc');

        if ($request->filled('company_id') && $request->input('company_id') !== 'all') {
            $query->where('company_id', $request->integer('company_id'));
        }

        $knowledgeBase = $query->paginate(10);
        $companies     = Company::orderBy('company_name')->get();

        return view('user.knowledgeBase.list', compact('knowledgeBase', 'companies'));
    }

    public function knowledgeBaseCreate()
    {
        $companies = Company::orderBy('company_name')->get();
        return view('user.knowledgeBase.create', compact('companies'));
    }

    public function knowledgeBaseStore(Request $request, KnowledgeService $service)
    {
        $validated = $request->validate([
            'company_id'  => 'required|exists:companies,id',
            'title'       => 'required|string|max:255',
            'content'     => 'required|string|max:5000', // The "limited description"
            'keywords'    => 'nullable|string|max:1000',
        ]);

        try {
            KnowledgeBase::create([
                'company_id'  => $validated['company_id'],
                'title'       => $validated['title'],
                'content'     => $validated['content'],
                'keywords'    => $validated['keywords'] ?? null,
                'is_active'   => true,
            ]);

            // Invalidate KB cache
            $service->invalidateCache((int) $validated['company_id']);

            return redirect()->route('user.knowledgeBase.list')->with('success', 'Knowledge base entry created successfully.');

        } catch (Exception $e) {
            Log::error('KB Store Error', ['message' => $e->getMessage()]);
            return back()->with('error', 'Error creating knowledge base entry: ' . $e->getMessage());
        }
    }

    public function knowledgeBaseEdit($id)
    {
        $kb        = KnowledgeBase::findOrFail($id);
        $companies = Company::orderBy('company_name')->get();
        return view('user.knowledgeBase.edit', compact('kb', 'companies'));
    }

    public function knowledgeBaseUpdate(Request $request, $id, KnowledgeService $service)
    {
        $validated = $request->validate([
            'company_id'  => 'required|exists:companies,id',
            'title'       => 'required|string|max:255',
            'content'     => 'required|string|max:5000',
            'keywords'    => 'nullable|string|max:1000',
        ]);

        $kb = KnowledgeBase::findOrFail($id);

        try {
            $kb->update([
                'company_id'  => $validated['company_id'],
                'title'       => $validated['title'],
                'content'     => $validated['content'],
                'keywords'    => $validated['keywords'] ?? null,
                'is_active'   => $request->boolean('is_active'),
            ]);

            // Invalidate KB cache
            $service->invalidateCache((int) $validated['company_id']);

            return redirect()->route('user.knowledgeBase.list')->with('success', 'Knowledge base entry updated successfully.');

        } catch (Exception $e) {
            Log::error('KB Update Error', ['message' => $e->getMessage()]);
            return back()->with('error', 'Error updating knowledge base entry: ' . $e->getMessage());
        }
    }

    public function knowledgeBaseDelete($id, KnowledgeService $service)
    {
        $kb = KnowledgeBase::findOrFail($id);
        $companyId = $kb->company_id;

        $kb->delete();

        $service->invalidateCache($companyId);

        return redirect()->route('user.knowledgeBase.list')->with('success', 'Knowledge base entry deleted successfully.');
    }

    public function knowledgeBaseDetails($id)
    {
        $data = KnowledgeBase::with('company')->findOrFail($id);
        return view('user.knowledgeBase.details', compact('data'));
    }

    /**
     * Display Simulator View
     */
    public function kbSimulator()
    {
        $companies = Company::orderBy('company_name')->get();
        return view('user.knowledgeBase.simulator', compact('companies'));
    }

    /**
     * Run the KB Identification Logic (Stage 1)
     * Splits the conversation into Q/A pairs and matches each pair independently.
     * Unique matched KBs across all pairs are collected and returned.
     */
    public function kbSimulatorRun(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'query_text' => 'required|string',
        ]);

        $companyId = $request->company_id;
        $queryText = $request->query_text;

        // 1. Get KB entries for the company
        $kbEntries = KnowledgeBase::where('company_id', $companyId)
            ->where('is_active', true)
            ->select(['id', 'title', 'keywords', 'content'])
            ->get();

        if ($kbEntries->isEmpty()) {
            return back()->with('error', 'No active Knowledge Base entries found for this company.');
        }

        $kbMapping = [];
        foreach ($kbEntries as $entry) {
            $kbMapping[] = [
                'id'       => $entry->id,
                'name'     => $entry->title,
                'keywords' => array_map('trim', explode(',', $entry->keywords ?? ''))
            ];
        }

        // 2. Split conversation into Q/A pairs
        $pairs = $this->splitIntoPairs($queryText);

        Log::debug('[Simulator] Conversation split into pairs.', ['pair_count' => count($pairs)]);

        // 3. For each pair, identify which KB(s) match
        $collectedKbIds = [];   // ordered unique KB IDs across all pairs
        $pairResults    = [];   // per-pair breakdown for the view

        foreach ($pairs as $pairText) {
            $matchedIndices = $this->openai->identifyMatchedKnowledgeBase($pairText, $kbMapping);

            $pairKbIds = [];
            foreach ($matchedIndices as $index) {
                if (isset($kbMapping[$index])) {
                    $id = $kbMapping[$index]['id'];
                    $pairKbIds[] = $id;
                    if (!in_array($id, $collectedKbIds)) {
                        $collectedKbIds[] = $id;
                    }
                }
            }

            $pairResults[] = [
                'pair_text'      => $pairText,
                'matched_kb_ids' => $pairKbIds,
            ];
        }

        // 4. Build the final unique KB list preserving order of first match
        $results = [];
        foreach ($collectedKbIds as $id) {
            $kb = $kbEntries->firstWhere('id', $id);
            if ($kb) {
                $results[] = $kb;
            }
        }

        return view('user.knowledgeBase.simulator', [
            'companies'        => Company::orderBy('company_name')->get(),
            'results'          => $results,
            'query_text'       => $queryText,
            'selected_company' => $companyId,
            'kb_mapping_sent'  => $kbMapping,
            'pair_results'     => $pairResults,   // per-pair breakdown
        ]);
    }

    /**
     * Split a conversation transcript into individual Q/A pairs.
     *
     * Strategy:
     *  1. Split on blank lines — each block is one pair.
     *  2. If the whole text is one block, try splitting on speaker-change lines
     *     (e.g. "Customer:", "Agent:", "Q:", "A:").
     *  3. Fall back to treating the entire text as a single pair.
     */
    private function splitIntoPairs(string $text): array
    {
        // Method 1: blank-line delimited blocks
        $blocks = preg_split('/\n[\s]*\n/', trim($text));
        $blocks = array_values(array_filter(array_map('trim', $blocks)));

        if (count($blocks) >= 2) {
            return $blocks;
        }

        // Method 2: detect speaker-tagged lines and group each Customer+Agent exchange
        $speakerPattern = '/^(?:Customer|Agent|User|Assistant|Q|A|عميل|وكيل|Speaker\s*\d+)\s*:/im';
        $lines = explode("\n", trim($text));

        $pairs  = [];
        $buffer = [];
        $seenCustomer = false;

        foreach ($lines as $line) {
            $isCustomerLine = preg_match('/^(?:Customer|User|Q|عميل)\s*:/i', trim($line));
            $isAgentLine    = preg_match('/^(?:Agent|Assistant|A|وكيل)\s*:/i', trim($line));

            // Start of a new customer turn → flush previous pair
            if ($isCustomerLine && $seenCustomer && !empty($buffer)) {
                $pairs[]  = implode("\n", $buffer);
                $buffer   = [];
                $seenCustomer = false;
            }

            if ($isCustomerLine) {
                $seenCustomer = true;
            }

            $buffer[] = $line;

            // After an agent reply, close the pair
            if ($isAgentLine && $seenCustomer) {
                $pairs[]  = implode("\n", $buffer);
                $buffer   = [];
                $seenCustomer = false;
            }
        }

        // Any remaining lines
        if (!empty($buffer)) {
            $pairs[] = implode("\n", $buffer);
        }

        $pairs = array_values(array_filter(array_map('trim', $pairs)));

        return !empty($pairs) ? $pairs : [trim($text)];
    }
}