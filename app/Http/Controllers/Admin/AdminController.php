<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function index()
    {
        $dataList = Admin::where('roleId', '!=', 3)->get(); // Assuming 3 is for regular users
        return view('admin.pages.admin-list', compact('dataList'));
    }

    public function create()
    {
        return view('admin.pages.create-edit');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roleId' => 'required|in:1,2', // 1=Admin, 2=Sub Admin
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('password', 'image');
        $data['password'] = Hash::make($request->password);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('admin_images', 'public');
        }

        Admin::create($data);

        return redirect()->route('admin.admins.index')->with('success', 'Admin created successfully!');
    }

    public function edit(Admin $admin)
    {
        return view('admin.pages.create-edit', compact('admin'));
    }

    public function update(Request $request, Admin $admin)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($admin->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'roleId' => 'required|in:1,2',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('password', 'image');

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($admin->image) {
                Storage::disk('public')->delete($admin->image);
            }
            $data['image'] = $request->file('image')->store('admin_images', 'public');
        }

        $admin->update($data);

        return redirect()->route('admin.admins.index')->with('success', 'Admin updated successfully!');
    }

    public function destroy(Admin $admin)
    {
        // Prevent self-deletion
        if ($admin->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account!');
        }

        // Delete image if exists
        if ($admin->image) {
            Storage::disk('public')->delete($admin->image);
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')->with('success', 'Admin deleted successfully!');
    }
}