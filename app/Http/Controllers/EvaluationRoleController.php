<?php

namespace App\Http\Controllers;

use App\Models\AgentEvaluationRole;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.api');
        $this->middleware('permission:evaluationroles.view')->only(['index']);
        $this->middleware('permission:evaluationroles.create')->only(['create', 'store']);
        $this->middleware('permission:evaluationroles.edit')->only(['edit', 'update']);
        $this->middleware('permission:evaluationroles.delete')->only(['destroy']);
    }

    public function index()
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        if ($companyId) {
            $company = Company::find($companyId);
            $roles = AgentEvaluationRole::where('company_id', $companyId)->get();
        } else {
            $company = (object)['company_name' => 'All Companies', 'id' => null];
            $roles = AgentEvaluationRole::all();
        }

        return view('user.evaluation_roles.index', compact('roles', 'company'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $company = $companyId ? Company::find($companyId) : (object)['company_name' => 'Evalia HQ', 'id' => null];
        return view('user.evaluation_roles.create', compact('company'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        AgentEvaluationRole::create([
            'name' => $request->name,
            'company_id' => $companyId,
            'eval_kb' => $request->has('eval_kb'),
            'eval_policies' => $request->has('eval_policies'),
            'eval_risks' => $request->has('eval_risks'),
            'eval_extractions' => $request->has('eval_extractions'),
            'eval_professionalism' => $request->has('eval_professionalism'),
            'eval_assessment' => $request->has('eval_assessment'),
            'eval_cooperation' => $request->has('eval_cooperation'),
            'eval_linguistic' => $request->has('eval_linguistic'),
        ]);

        return redirect()->route('user.evaluation_roles.index')
            ->with('success', 'Evaluation role created successfully.');
    }

    public function edit($id)
    {
        $role = AgentEvaluationRole::findOrFail($id);
        $company = $role->company;
        return view('user.evaluation_roles.edit', compact('role', 'company'));
    }

    public function update(Request $request, $id)
    {
        $role = AgentEvaluationRole::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $role->update([
            'name' => $request->name,
            'eval_kb' => $request->has('eval_kb'),
            'eval_policies' => $request->has('eval_policies'),
            'eval_risks' => $request->has('eval_risks'),
            'eval_extractions' => $request->has('eval_extractions'),
            'eval_professionalism' => $request->has('eval_professionalism'),
            'eval_assessment' => $request->has('eval_assessment'),
            'eval_cooperation' => $request->has('eval_cooperation'),
            'eval_linguistic' => $request->has('eval_linguistic'),
        ]);

        return redirect()->route('user.evaluation_roles.index')
            ->with('success', 'Evaluation role updated successfully.');
    }

    public function destroy($id)
    {
        $role = AgentEvaluationRole::findOrFail($id);
        $role->delete();

        return redirect()->route('user.evaluation_roles.index')
            ->with('success', 'Evaluation role deleted successfully.');
    }
}
