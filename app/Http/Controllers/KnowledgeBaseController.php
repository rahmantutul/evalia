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

        // 2. Call OpenAI (Stage 1: Identification)
        $matchedIndices = $this->openai->identifyMatchedKnowledgeBase($queryText, $kbMapping);

        $results = [];
        if (!empty($matchedIndices)) {
            foreach ($matchedIndices as $index) {
                if (isset($kbMapping[$index])) {
                    $id = $kbMapping[$index]['id'];
                    $kb = $kbEntries->firstWhere('id', $id);
                    if ($kb) {
                        $results[] = $kb;
                    }
                }
            }
        }

        return view('user.knowledgeBase.simulator', [
            'companies' => Company::orderBy('company_name')->get(),
            'results' => $results,
            'query_text' => $queryText,
            'selected_company' => $companyId,
            'kb_mapping_sent' => $kbMapping
        ]);
    }
}