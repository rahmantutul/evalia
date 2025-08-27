<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function agentStore(Request $request)
    {
        return response()->json(['message' => 'Agent stored successfully']);
    }

    public function agentDetails()
    {
        return view('user.agent.agent_details');
    }

    public function agentTask()
    {
        return redirect()->back()->with('success', 'Agent deleted successfully');
    }

    public function agentList()
    {
        return view('user.agent.agent_list');
    }

    public function agentCreate()
    {
        return view('user.agent.agent_create');
    }
    public function agentEdit()
    {
        return view('user.agent.agent_create');
    }
}
