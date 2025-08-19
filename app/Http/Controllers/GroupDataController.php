<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
class GroupDataController extends Controller
{
    public function groupList()
    {
        $response = Http::get('http://52.22.157.186:8080/list_groups');

        $groups = [];
        if ($response->successful()) {
            $groups = $response->json()['data'] ?? [];
        }
        return view('user.group.group_list', compact('groups'));
    }

    public function companyCreate()
    {
        return view('user.company_create');
    }

    public function groupDetails($id)
    {
        $response = Http::get('http://65.108.142.207:8080/get_company_details', [
            'company_id' => $id
        ]);
        $company = $response->successful() ? ($response->json()['data'] ?? []) : [];

        $response_audio = Http::get("http://65.108.142.207:8080/task_list", [
            'company_id' => $id
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

        return view('user.company_details', [
            'company' => $company,
            'taskList' => $paginatedTasks,
        ]);
    }


    public function groupDelete($id)
    {
        try {
            $response = Http::delete("http://65.108.142.207:8080/delete_group?group_id={$id}");

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Group deleted successfully.');
            } else {
                $errorMessage = $response->json()['detail'][0]['msg'] ?? $response->body();
                return redirect()->back()->with('error', 'Failed to delete group: ' . $errorMessage);
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Request failed: ' . $e->getMessage());
        }
    }
}
