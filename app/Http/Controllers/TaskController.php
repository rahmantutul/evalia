<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TaskController extends Controller
{
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

            if (!isset($data['speakers_transcriptions']) || !isset($data['call_duration'])) {
                return redirect()->back()->with('error', 'Incomplete analysis data received.');
            }

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

}
