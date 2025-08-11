<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Feature;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::orderBy('sort_order')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        $features = Feature::orderBy('sort_order')->get();
        return view('admin.plans.create', compact('features'));
    }

    public function store(Request $request)
    {
        // Validate plan data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'required|integer|min:1',
        ]);

        // Create the plan
        $plan = Plan::create($validated);

        // Attach features with their values
        if ($request->has('features')) {
            $features = [];
            foreach ($request->input('features') as $featureId => $value) {
                $features[$featureId] = ['value' => $value];
            }
            $plan->features()->attach($features);
        }

        return redirect()->route('admin.plans.index')->with('success', 'Plan created successfully');
    }

    public function show(Plan $plan)
    {
        return view('admin.plans.show', compact('plan'));
    }

    public function edit(Plan $plan)
    {
        $features = Feature::orderBy('sort_order')->get();
        return view('admin.plans.edit', compact('plan', 'features'));
    }
    public function update(Request $request, Plan $plan)
    {
        // Validate plan data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'required|integer|min:1',
        ]);

        // Update the plan
        $plan->update($validated);

        // Sync features with their values
        if ($request->has('features')) {
            $features = [];
            foreach ($request->input('features') as $featureId => $value) {
                $features[$featureId] = ['value' => $value];
            }
            $plan->features()->sync($features);
        }

        return redirect()->route('admin.plans.index')->with('success', 'Plan updated successfully');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('admin.plans.index')->with('success', 'Plan deleted successfully');
    }

    public function editFeatures(Plan $plan)
    {
        $features = Feature::orderBy('sort_order')->get();
        return view('admin.plans.features', compact('plan', 'features'));
    }

    public function updateFeatures(Request $request, Plan $plan)
    {
        $features = Feature::all();
        $syncData = [];
        
        foreach ($features as $feature) {
            if ($request->has('feature_'.$feature->id)) {
                $value = $request->input('feature_'.$feature->id);
                
                // Validate based on feature type
                switch ($feature->type) {
                    case 'integer':
                        $value = (int)$value;
                        break;
                    case 'boolean':
                        $value = (bool)$value;
                        break;
                    case 'string':
                        $value = (string)$value;
                        break;
                }
                
                $syncData[$feature->id] = ['value' => $value];
            }
        }
        
        $plan->features()->sync($syncData);
        
        return redirect()->route('admin.plans.index')->with('success', 'Plan features updated successfully');
    }
    public function preview (){
        $plans = Plan::with('features')
                ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
        
        $features = Feature::orderBy('sort_order')->get();
        
        return view('admin.plans.preview_plans', compact('plans', 'features'));
    }
    public function system_setting (){
        $plans = Plan::with('features')
                ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
        
        $features = Feature::orderBy('sort_order')->get();
        
        return view('admin.system_setting', compact('plans', 'features'));
    }
}