<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.api');
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->isAgent()) {
            return redirect()->route('agent.dashboard');
        }

        if ($user->isSupervisor()) {
            return redirect()->route('supervisor.dashboard');
        }

        // Get common task stats for the dashboard
        $allTasks = app(CompanyController::class)->getAllTasks();
        
        $totalCompletedTasks = count(array_filter($allTasks, function($t) { return $t['status'] === 'completed'; }));
        $totalActiveTasks = count(array_filter($allTasks, function($t) { return $t['status'] === 'processing'; }));
        $totalPendingAnalysis = count(array_filter($allTasks, function($t) { return $t['status'] === 'pending'; }));
        
        $totalScore = array_sum(array_column($allTasks, 'score'));
        $avgQaScore = count($allTasks) > 0 ? round($totalScore / count($allTasks), 1) : 0;
        
        return view('user.dashboard', compact(
            'totalCompletedTasks',
            'totalActiveTasks',
            'totalPendingAnalysis',
            'avgQaScore'
        ));
    }

    public function setActiveProduct(Request $request)
    {
        $productId = $request->input('product_id');
        
        // 🔹 Clear previous product-specific session data
        session()->forget([
            'product_1_data',
            'product_2_data',
            'product_3_data',
        ]);
        
        // 🔹 Set new active product
        session(['active_product' => $productId]);
        
        // 🔹 Return success with redirect URL if needed
        return response()->json([
            'success' => true,
            'redirect_url' => route('user.home')
        ]);
    }

    public function profile()
    {
        $user = session('user');
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = session('user');
        
        $userData = [
            'id' => $user['id'] ?? '1',
            'full_name' => $request->input('name', $user['full_name'] ?? 'أحمد حسان'),
            'email' => $request->input('email', $user['email'] ?? 'ahmed.hassan@ssc.gov.jo'),
            'phone' => $request->input('phone', $user['phone'] ?? '+962 79 123 4567'),
            'company_name' => $request->input('company', $user['company_name'] ?? 'الضمان الاجتماعي - الأردن'),
            'role' => $user['role'] ?? ['name' => 'Admin'],
        ];

        session(['user' => $userData]);

        return back()->with('success', 'Profile updated successfully (Mock)!');
    }

    public function support()
    {
        return view('user.support');
    }

    public function subscription()
    {
        return view('user.subscription');
    }

    public function bots()
    {
        return view('user.bots');
    }

    public function bot_create()
    {
        return view('user.bot_create');
    }
    
    public function bot_store()
    {
        return view('user.maintenance');
    }

    public function overview()
    {
        return view('user.overview');
    }

    public function inbox()
    {
        return view('user.inbox');
    }

    public function performanceBadges()
    {
        return view('user.performance_badges');
    }
}
