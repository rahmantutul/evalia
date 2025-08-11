<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class SettingsController extends Controller
{
    // SettingsController.php
public function edit()
{
    $settings = SystemSetting::first();
        return view('admin.system_setting', compact('settings'));
    }

    public function update(Request $request)
    {

        $settings = SystemSetting::first();
        
        if (!$settings) {
            return back()->with('error', 'System settings record not found');
        }

        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_tagline' => 'nullable|string|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'favicon' => 'nullable|image|mimes:png|max:512',
            'offer_text' => 'nullable',
            'pricing_title' => 'nullable',
            'pricing_subtitle' => 'nullable',
            'dashboard_text' => 'nullable',
            'dashboard_subtext' => 'nullable',
        ]);
        
        try {
            // Handle company logo upload
            if ($request->hasFile('company_logo')) {
                // Delete old logo if exists
                if ($settings->company_logo) {
                    Storage::delete($settings->company_logo);
                }
                $data['company_logo'] = $request->file('company_logo')->store('settings', 'public');
            }
            
            // Handle favicon upload
            if ($request->hasFile('favicon')) {
                // Delete old favicon if exists
                if ($settings->favicon) {
                    Storage::delete($settings->favicon);
                }
                $data['favicon'] = $request->file('favicon')->store('settings', 'public');
            }
            
            $updated = $settings->update($data);
            
            if (!$updated) {
                throw new \Exception('Failed to update settings');
            }
            
            return back()->with('success', 'Settings updated successfully');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating settings: ' . $e->getMessage())
                        ->withInput();
        }
    }
}
