<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
class TaskController extends Controller
{
    public function TaskList($companyId){
         $response_audio = Http::get("http://65.108.142.207:8080/task_list", [
            'company_id' => $companyId
        ]);

        $taskList = $response_audio->successful() ? ($response_audio->json()['tasks'] ?? []) : [];

        $page = Paginator::resolveCurrentPage(); 
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $pagedTasks = array_slice($taskList, $offset, $perPage);

        $paginatedTasks = new LengthAwarePaginator(
            $pagedTasks,
            count($taskList),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath()] 
        );
        return view('user.task.task_list', [
            'company_id' => $companyId,
            'taskList' => $paginatedTasks,
        ]);
    }



    public function taskDetails($workId)
    {

        $url = 'http://65.108.142.207:8080/analysis_result?work_id=' . urlencode($workId);

        $response = Http::timeout(30)
            ->retry(3, 100)
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->get($url);

        if ($response->successful()) {
            $result = $response->json();
            
            if (!isset($result['data'])) {
                return redirect()->back()->with('error', 'Invalid response structure from API.');
            }

            $data = $result['data'];
            // dd($data); // Debugging line to inspect the data structure
            return view('user.task.task_details', [
                'data' => $data,
                'workId' => $workId,
                'status' => $result['status'] ?? 'completed' // Pass status with default
            ]);

        } else {
            return redirect()->back()->with('error', 'Failed to fetch analysis result. API returned status: ' . $response->status());
        }
    }
   public function deleteTask($workId)
    {
        $url = 'http://65.108.142.207:8080/delete_task?work_id=' . urlencode($workId);

        $response = Http::timeout(30)
            ->retry(3, 100)
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->delete($url);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Task deleted successfully.');
        }

         return redirect()->back()->with('error', 'Failed to delete Task');
    }
    public function taskStore(Request $request)
    {
        $request->validate([
            'company_id' => 'required|string',
            'agent_audio' => 'required|file|mimes:mp3,wav|max:51200',
            'customer_audio' => 'required|file|mimes:mp3,wav|max:51200',
            'combined_audio' => 'nullable|file|mimes:mp3,wav|max:102400',
        ]);

        try {
            $client = new \GuzzleHttp\Client();
            
            $multipart = [
                [
                    'name' => 'company_id',
                    'contents' => $request->input('company_id')
                ],
                [
                    'name' => 'agent_audio',
                    'contents' => fopen($request->file('agent_audio')->path(), 'r'),
                    'filename' => $request->file('agent_audio')->getClientOriginalName()
                ],
                [
                    'name' => 'customer_audio',
                    'contents' => fopen($request->file('customer_audio')->path(), 'r'),
                    'filename' => $request->file('customer_audio')->getClientOriginalName()
                ]
            ];

            if ($request->hasFile('combined_audio')) {
                $multipart[] = [
                    'name' => 'combined_audio',
                    'contents' => fopen($request->file('combined_audio')->path(), 'r'),
                    'filename' => $request->file('combined_audio')->getClientOriginalName()
                ];
            }

            $response = $client->post('http://65.108.142.207:8080/background_processing_v2', [
                'multipart' => $multipart,
                'headers' => [
                    'Accept' => 'application/json',
                ]
            ]);

            $responseBody = json_decode($response->getBody(), true);

            sleep(3); 

            return redirect()->back()->with('success', 'Audio analysis completed successfully!');

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $errorMessage = 'Failed to process audio';
            
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $errorBody = json_decode($response->getBody(), true);
                
                if ($statusCode === 422) {
                    $errorMessage = 'Validation error: ' . collect($errorBody['detail'])->pluck('msg')->implode(', ');
                } else {
                    $errorMessage = 'API Error: ' . ($errorBody['message'] ?? 'Unknown error');
                }
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
