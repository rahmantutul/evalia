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
            ['id' => 'hassan', 'group_name' => 'Default Group', 'description' => 'General purpose group', 'created_at' => now()->toDateTimeString()],
            ['id' => 'group-2', 'group_name' => 'Premium Group', 'description' => 'High priority group', 'created_at' => now()->toDateTimeString()],
            ['id' => 'group-3', 'group_name' => 'Enterprise Group', 'description' => 'Large scale operations', 'created_at' => now()->toDateTimeString()]
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
            'id' => $groupId,
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
            'id' => $groupId,
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