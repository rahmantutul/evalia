<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class KnowledgeBaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.api');
    }

    public function knowledgeBaseStore(Request $request)
    {
        return redirect()
            ->route('user.knowledgeBase.list')
            ->with('success', 'Knowledge base created successfully (Mock).');
    }

    public function knowledgeBaseDelete()
    {
        return redirect()->route('user.knowledgeBase.list')->with('success', 'Article deleted successfully (Mock).');
    }

    public function knowledgeBaseList(Request $request)
    {
        $knowledgeBase = [];
        for ($i = 1; $i <= 15; $i++) {
            $knowledgeBase[] = [
                'id' => (string)$i,
                'notebook_id' => "nb-$i",
                'notebook_name' => "Notebook Alpha $i",
                'company_id' => "hassan",
                'content_file_name' => "document_$i.pdf",
                'source_title' => "Document Alpha $i",
                'description' => "This is a dummy description for knowledge base item $i. It contains important information about the company procedures.",
                'topics' => ['Policy', 'Procedure', 'Guideline'],
                'created_at' => now()->subDays($i)->toDateTimeString(),
                'company_name' => 'Evalia Demo Corp'
            ];
        }
        
        $collection = collect($knowledgeBase);
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $currentItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedItems = new LengthAwarePaginator(
            $currentItems,
            $collection->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('user.knowledgeBase.list', ['knowledgeBase' => $paginatedItems]);
    }

    public function knowledgeBaseCreate()
    {
        $companies = [
            ['id' => 'hassan', 'name' => 'Evalia Demo Corp'],
            ['id' => 'company-2', 'name' => 'Future Systems']
        ];

        return view('user.knowledgeBase.create', compact('companies'));
    }

    public function knowledgeBaseEdit()
    {
        return view('user.knowledgeBase.edit');
    }
}