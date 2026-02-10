<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;

class GroupDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.api');
    }

    public function groupList(Request $request)
    {
        $groups = [
            [
                'group_id' => 'hassan', 
                'group_name' => 'Support Team', 
                'description' => 'General customer support keywords', 
                'created_at' => now()->toDateTimeString(),
                'keyword_sets' => [
                    ['name' => 'Greeting', 'keywords' => ['hello', 'hi', 'welcome', 'good morning']],
                    ['name' => 'Closing', 'keywords' => ['bye', 'thank you', 'have a nice day']]
                ]
            ],
            [
                'group_id' => 'group-2', 
                'group_name' => 'Sales Department', 
                'description' => 'Sales and lead generation keywords', 
                'created_at' => now()->toDateTimeString(),
                'keyword_sets' => [
                    ['name' => 'Pricing', 'keywords' => ['cost', 'price', 'discount', 'quote']],
                    ['name' => 'Follow up', 'keywords' => ['call back', 'next steps', 'schedule']]
                ]
            ],
            [
                'group_id' => 'group-3', 
                'group_name' => 'Tech Infrastructure', 
                'description' => 'Technical and backend monitoring', 
                'created_at' => now()->toDateTimeString(),
                'keyword_sets' => [
                    ['name' => 'Server Errors', 'keywords' => ['500 error', 'timeout', 'latency', 'database down']],
                    ['name' => 'Maintenance', 'keywords' => ['upgrade', 'patch', 'reboot']]
                ]
            ]
        ];
        
        $paginatedGroups = new LengthAwarePaginator(
            $groups,
            count($groups),
            10,
            1,
            ['path' => Paginator::resolveCurrentPath()]
        );
        
        return view('user.group.group_list', compact('paginatedGroups'));
    }

    public function groupCreate()
    {
        return view('user.group.group_create');
    }

    public function groupDetails($groupId)
    {
        $group = [
            'group_id' => $groupId,
            'group_name' => 'Default Group',
            'description' => 'General purpose group',
            'filler_words' => ['err', 'umm'],
            'main_topics' => ['support'],
            'call_types' => ['inbound'],
            'company_policies' => ['Policy 1']
        ];

        return view('user.group.group_edit', compact('group'));
    }

    public function groupDelete($groupId)
    {
        return redirect()->back()->with('success', 'Group deleted successfully (Mock).');
    }

    public function groupStore(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Group created successfully (Mock)',
            'data' => [],
        ]);
    }

    public function groupEdit($groupId)
    {
        $group = [
            'group_id' => $groupId,
            'group_name' => 'Default Group',
            'description' => 'General purpose group',
            'filler_words' => ['err', 'umm'],
            'main_topics' => ['support'],
            'call_types' => ['inbound'],
            'company_policies' => ['Policy 1']
        ];

        return view('user.group.group_edit', compact('group'));
    }

    public function groupUpdate(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Group updated successfully (Mock)',
            'data' => [],
        ]);
    }
}