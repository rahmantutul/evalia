<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
class KnowledgeBaseController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth.api');
    }
    private function getToken()
    {
        return session('user_access_token');
    }

    private function getAuthHeaders()
    {
        $token = $this->getToken();
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ];
    }

public function knowledgeBaseStore(Request $request)
{
    $validated = $request->validate([
        'company_id'    => 'required',
        'file'          => 'required|file|mimes:txt,pdf,doc,docx,csv,xlsx,xls|max:10240',
        'topics'        => 'nullable|string',
        'description'   => 'required|string',
        'run_embedding' => 'boolean',
    ]);

    $cleanTagifyInput = function ($input) {
        if (empty($input)) return [];
        
        if (is_array($input)) {
            return array_map(function ($item) {
                return is_array($item) ? ($item['value'] ?? $item) : $item;
            }, $input);
        }
        
        $decoded = json_decode($input, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            if (isset($decoded[0]) && is_array($decoded[0])) {
                return array_map(function ($item) {
                    return $item['value'] ?? $item;
                }, $decoded);
            }
            if (isset($decoded['value'])) {
                return [$decoded['value']];
            }
            return $decoded;
        }
        
        return array_filter(array_map('trim', explode(',', $input)));
    };

    try {
        $file = $request->file('file');
        $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // Prepare the data payload
        $dataPayload = [
            'topics' => $cleanTagifyInput($validated['topics'] ?? ''),
            'description' => $validated['description'],
            'run_embedding' => $validated['run_embedding'] ?? false,
            'source_title' => $fileName
        ];

        // Use Guzzle for multipart form data
        $client = new \GuzzleHttp\Client();
        
        $multipartData = [
            [
                'name' => 'company_id',
                'contents' => (string)$validated['company_id']
            ],
            [
                'name' => 'file',
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName()
            ],
            [
                'name' => 'data',
                'contents' => json_encode($dataPayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            ]
        ];

        $response = $client->post('http://35.153.178.201:8080/knowledgebase_notebook', [
            'multipart' => $multipartData,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->getToken(),
            ],
            'timeout' => 120,
        ]);

        $statusCode = $response->getStatusCode();
        $responseBody = $response->getBody()->getContents();
        $responseData = json_decode($responseBody, true);

        if ($statusCode >= 200 && $statusCode < 300) {
            $filePath = $file->store('knowledge_base_files', 'public');
            
            return redirect()
                ->route('user.knowledgeBase.list')
                ->with('success', 'Knowledge base created successfully.');
        } else {
            $errorMessage = $responseData['detail'] ?? $responseData['message'] ?? 'API request failed';
            
            return back()
                ->with('error', $errorMessage)
                ->withInput();
        }

    } catch (\GuzzleHttp\Exception\RequestException $e) {
        $errorMessage = 'API request failed';
        if ($e->hasResponse()) {
            $response = $e->getResponse();
            $responseBody = $response->getBody()->getContents();
            $responseData = json_decode($responseBody, true);
            $errorMessage = $responseData['detail'] ?? $responseData['message'] ?? $errorMessage;
        }
        
        return back()
            ->with('error', $errorMessage)
            ->withInput();
            
    } catch (\Exception $e) {
        return back()
            ->with('error', 'Request failed: ' . $e->getMessage())
            ->withInput();
    }
}

    public function knowledgeBaseDelete()
    {
        return redirect()->route('user.knowledgeBase.list')->with('success', 'Article deleted successfully.');
    }

    public function knowledgeBaseList(Request $request)
    {
        $response = Http::get('http://35.153.178.201:8080/get_all');

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
        $response = Http::withHeaders($this->getAuthHeaders())
            ->get('http://35.153.178.201:8080/list_of_companies');

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