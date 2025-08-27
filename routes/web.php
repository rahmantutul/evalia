<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\Admin\{
    AuthController as AdminAuthController,
    AdminController,
    FeatureController,
    PlanController,
    SettingsController
};
use App\Http\Controllers\AgentController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\GroupDataController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Artisan;
// Clear website cache 
Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('clear-compiled');
    Artisan::call('optimize:clear');
    return redirect()->back()->with('success', 'Cache cleared successfully!');
})->name('cache.clear');

// Login route 
Route::get('/', function () {
    return redirect()->route('login'); 
});

// Google authentication route 
Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Auth::routes();

// =================User routes ======================
Route::group(['middleware' => 'auth'], function () {
    Route::get('/user-dashboard', [HomeController::class, 'index'])->name('user.home');
    Route::get('/user-profile', [HomeController::class, 'profile'])->name('user.profile');
    Route::put('/user-profile/update', [HomeController::class, 'updateProfile'])->name('user.profile.update');
    Route::get('/user/subscription', [HomeController::class, 'subscription'])->name('user.subscription');

    // Group Routs start 
    Route::get('/user/group_data/list', [GroupDataController::class, 'groupList'])->name('user.group_data.list');
    Route::get('/user/group_data/create', [GroupDataController::class, 'groupCreate'])->name('user.group_data.create');
    Route::post('/user/group_data/store', [GroupDataController::class, 'groupStore'])->name('user.group_data.store');
    Route::delete('/user/group_data/delete/{id}', [GroupDataController::class, 'groupDelete'])->name('user.group_data.delete');

    // Company Routs start 
    Route::get('/user/company/list', [CompanyController::class, 'companyList'])->name('user.company.list');
    Route::get('/user/company/create', [CompanyController::class, 'companyCreate'])->name('user.company.create');
    Route::get('/user/company/view/{id}', [CompanyController::class, 'companyDetails'])->name('user.company.view');
    Route::get('/user/company/edit/{id}', [CompanyController::class, 'companyEdit'])->name('user.company.edit');
    Route::post('/user/company/store', [CompanyController::class, 'companyStore'])->name('user.company.store');
    Route::put('/user/company/update/{company_id}', [CompanyController::class, 'companyUpdate'])->name('user.company.update');
    Route::get('/user/company/delete/{id}', [CompanyController::class, 'companyDelete'])->name('user.company.delete');


    // Task routes start
    Route::post('/user/task/store', [TaskController::class, 'taskStore'])->name('user.task.store');
    Route::get('/user/task/details/{workId}', [TaskController::class, 'taskDetails'])->name('user.task.details');
    Route::get('/user/task/delete/{workId}', [TaskController::class, 'deleteTask'])->name('user.task.delete');
    Route::get('/user/task/list/{companyId}', [TaskController::class, 'TaskList'])->name('user.task.list');

    // Support route start
    Route::get('/user/support', [HomeController::class, 'support'])->name('user.support');

    // Agent routes start
    Route::post('/user/agent/store', [AgentController::class, 'agentStore'])->name('user.agent.store');
    Route::get('/user/agent/details', [AgentController::class, 'agentDetails'])->name('user.agent.details');
    Route::get('/user/agent/delete', [AgentController::class, 'agentTask'])->name('user.agent.delete');
    Route::get('/user/agent/list', [AgentController::class, 'agentList'])->name('user.agent.list');
    Route::get('/user/agent/create', [AgentController::class, 'agentCreate'])->name('user.agent.create');
    Route::get('/user/agent/edit', [AgentController::class, 'agentEdit'])->name('user.agent.edit');

    // Knowledge Base routes start
    Route::post('/user/knowledgeBase/store', [KnowledgeBaseController::class, 'knowledgeBaseStore'])->name('user.knowledgeBase.store');
    Route::get('/user/knowledgeBase/details', [KnowledgeBaseController::class, 'knowledgeBaseDetails'])->name('user.knowledgeBase.details');
    Route::get('/user/knowledgeBase/delete', [KnowledgeBaseController::class, 'knowledgeBaseTask'])->name('user.knowledgeBase.delete');
    Route::get('/user/knowledgeBase/list', [KnowledgeBaseController::class, 'knowledgeBaseList'])->name('user.knowledgeBase.list');
    Route::get('/user/knowledgeBase/create', [KnowledgeBaseController::class, 'knowledgeBaseCreate'])->name('user.knowledgeBase.create');
    Route::get('/user/knowledgeBase/edit', [KnowledgeBaseController::class, 'knowledgeBaseEdit'])->name('user.knowledgeBase.edit');
});

































// =================Admin routes ======================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login']);
    });
    
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
});

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {

    Route::get('dashboard', [AdminController::class, 'index'])->name('dashboard');

    Route::resource('admins', AdminController::class)->except(['show']);

    Route::get('plans', [PlanController::class, 'index'])->name('plans.index');
    Route::get('plans/create', [PlanController::class, 'create'])->name('plans.create');
    Route::post('plans', [PlanController::class, 'store'])->name('plans.store');
    Route::get('plans/{plan}', [PlanController::class, 'show'])->name('plans.show');
    Route::get('plans/{plan}/edit', [PlanController::class, 'edit'])->name('plans.edit');
    Route::put('plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
    Route::delete('plans/{plan}', [PlanController::class, 'destroy'])->name('plans.destroy');

    Route::prefix('plans/{plan}')->group(function () {
        Route::get('features', [PlanController::class, 'editFeatures'])->name('plans.features.edit');
        Route::put('features', [PlanController::class, 'updateFeatures'])->name('plans.features.update');
    });

    Route::get('features', [FeatureController::class, 'index'])->name('features.index');
    Route::get('features/create', [FeatureController::class, 'create'])->name('features.create');
    Route::post('features', [FeatureController::class, 'store'])->name('features.store');
    Route::get('features/{feature}', [FeatureController::class, 'show'])->name('features.show');
    Route::get('features/{feature}/edit', [FeatureController::class, 'edit'])->name('features.edit');
    Route::put('features/{feature}', [FeatureController::class, 'update'])->name('features.update');
    Route::delete('features/{feature}', [FeatureController::class, 'destroy'])->name('features.destroy');

    Route::get('subs/preview', [PlanController::class, 'preview'])->name('plans.preview');
    Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
});
