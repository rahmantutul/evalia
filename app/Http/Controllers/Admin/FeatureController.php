<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    public function index()
    {
        $features = Feature::orderBy('sort_order')->get();
        return view('admin.features.index', compact('features'));
    }

    public function create()
    {
        $types = ['integer', 'boolean', 'string'];
        return view('admin.features.create', compact('types'));
    }

    public function store(Request $request)
    {
       
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key' => 'required',
            'type' => 'required|in:integer,boolean,string',
            'sort_order' => 'required|integer|min:1',
        ]);
        Feature::create($validated);

        return redirect()->route('admin.features.index')->with('success', 'Feature created successfully');
    }

    public function show(Feature $feature)
    {
        return view('admin.features.show', compact('feature'));
    }

    public function edit(Feature $feature)
    {
        $types = ['integer', 'boolean', 'string'];
        return view('admin.features.edit', compact('feature', 'types'));
    }

    public function update(Request $request, Feature $feature)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:features,key,'.$feature->id,
            'type' => 'required|in:integer,boolean,string',
            'sort_order' => 'required|integer|min:1',
        ]);

        $feature->update($validated);

        return redirect()->route('admin.features.index')->with('success', 'Feature updated successfully');
    }

    public function destroy(Feature $feature)
    {
        $feature->delete();
        return redirect()->route('admin.features.index')->with('success', 'Feature deleted successfully');
    }
}