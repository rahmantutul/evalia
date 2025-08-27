<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
class KnowledgeBaseController extends Controller
{
    public function knowledgeBaseStore(Request $request)
    {
        return redirect()->route('user.knowledgeBase.list')->with('success', 'Article created successfully.');
    }

    public function knowledgeBaseDetails()
    {
        return view('user.knowledgeBase.details');
    }

    public function knowledgeBaseTask()
    {
        return redirect()->route('user.knowledgeBase.list')->with('success', 'Article deleted successfully.');
    }

    public function knowledgeBaseList(Request $request)
    {
        $response = Http::get('http://65.108.142.207:8080/get_all');

        $knowledgeBase = [];
        if ($response->successful()) {
            $knowledgeBase = $response->json()['data'] ?? [];
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
         $response = Http::get('http://65.108.142.207:8080/list_of_companies');

        $companies = [];
        if ($response->successful()) {
            $companies = $response->json()['data'] ?? [];
        }
        return view('user.knowledgeBase.create',compact('companies'));
    }

    public function knowledgeBaseEdit()
    {
        return view('user.knowledgeBase.edit');
    }
}
