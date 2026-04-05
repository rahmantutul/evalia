<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SalesManagerController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TelephonyAccountController;
use App\Http\Controllers\VoicePintController;
use App\Http\Controllers\HamsaController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Criteria Routes
// Route::prefix('criteria')->name('criteria.')->group(function () {
//     Route::get('/', [SalesManagerController::class, 'criteriaIndex'])->name('index');
//     Route::get('/create', [SalesManagerController::class, 'criteriaCreate'])->name('create');
//     Route::post('/', [SalesManagerController::class, 'criteriaStore'])->name('store');
//     Route::get('/{id}/edit', [SalesManagerController::class, 'criteriaEdit'])->name('edit');
//     Route::put('/{id}', [SalesManagerController::class, 'criteriaUpdate'])->name('update');
//     Route::delete('/{id}', [SalesManagerController::class, 'criteriaDestroy'])->name('destroy');
// });

// // Sales Person Routes
// Route::prefix('sales-person')->name('sales_person.')->group(function () {
//     Route::get('/', [SalesManagerController::class, 'salesPersonIndex'])->name('index');
//     Route::get('/create', [SalesManagerController::class, 'salesPersonCreate'])->name('create');
//     Route::post('/', [SalesManagerController::class, 'salesPersonStore'])->name('store');
//     Route::get('/{id}', [SalesManagerController::class, 'salesPersonShow'])->name('show');
//     Route::get('/{id}/edit', [SalesManagerController::class, 'salesPersonEdit'])->name('edit');
//     Route::put('/{id}', [SalesManagerController::class, 'salesPersonUpdate'])->name('update');
//     Route::delete('/{id}', [SalesManagerController::class, 'salesPersonDestroy'])->name('destroy');
//     Route::get('/{id}/clients', [SalesManagerController::class, 'salesPersonClients'])->name('clients');
// });

// // Client Routes
// Route::prefix('client')->name('client.')->group(function () {
//     Route::get('/', [SalesManagerController::class, 'clientIndex'])->name('index');
//     Route::get('/create', [SalesManagerController::class, 'clientCreate'])->name('create');
//     Route::post('/', [SalesManagerController::class, 'clientStore'])->name('store');
//     Route::get('/{id}', [SalesManagerController::class, 'clientShow'])->name('show');
//     Route::get('/{id}/edit', [SalesManagerController::class, 'clientEdit'])->name('edit');
//     Route::put('/{id}', [SalesManagerController::class, 'clientUpdate'])->name('update');
//     Route::delete('/{id}', [SalesManagerController::class, 'clientDestroy'])->name('destroy');
// });



Route::get('/', function () {
    return redirect()->route(session()->has('user') ? 'user.home' : 'login');
});



Route::post('/set-active-product', [HomeController::class, 'setActiveProduct'])->name('setActiveProduct');



Route::group(['middleware' => 'auth.api'], function () {
    Route::prefix('hamsa')->name('hamsa.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [HamsaController::class, 'dashboard'])->name('dashboard');
        
        // Transcription
        Route::get('/transcribe', [HamsaController::class, 'transcribe'])->name('transcribe');
        Route::post('/transcribe', [HamsaController::class, 'transcribeSubmit'])->name('transcribe.submit');
        Route::get('/transcribe/{jobId}', [HamsaController::class, 'getTranscriptionJob'])->name('transcribe.job');
        
        // Text-to-Speech
        Route::get('/tts', [HamsaController::class, 'tts'])->name('tts');
        Route::post('/tts', [HamsaController::class, 'ttsSubmit'])->name('tts.submit');
        Route::get('/tts/{jobId}', [HamsaController::class, 'getTtsJob'])->name('tts.job');
        Route::get('/hamsa/tts/status', [HamsaController::class, 'checkTtsStatus'])->name('hamsa.tts.status');
        // Translation
        Route::get('/translate', [HamsaController::class, 'translate'])->name('translate');
        Route::post('/translate', [HamsaController::class, 'translateSubmit'])->name('translate.submit');
        
        // Speech-to-Speech
        Route::get('/sts', [HamsaController::class, 'sts'])->name('sts');
        Route::post('/sts', [HamsaController::class, 'stsSubmit'])->name('sts.submit');
        Route::get('/sts/{jobId}', [HamsaController::class, 'getStsJob'])->name('sts.job');
        
        // AI Content Generation
        Route::get('/ai/generate', [HamsaController::class, 'aiGenerate'])->name('ai.generate');
        Route::post('/ai/generate', [HamsaController::class, 'aiGenerateSubmit'])->name('ai.generate.submit');
        
        // Voice Agents
        Route::get('/voice-agents', [HamsaController::class, 'voiceAgents'])->name('voice-agents');
        Route::post('/voice-agents', [HamsaController::class, 'createVoiceAgent'])->name('voice-agents.create');
        Route::get('/voice-agents/{agentId}', [HamsaController::class, 'getVoiceAgent'])->name('voice-agents.show');
        Route::get('/clone-voice-agents', [HamsaController::class, 'cloneVoiceAgent'])->name('voice-agents.clone');
        
        // Conversations
        Route::get('/conversations', [HamsaController::class, 'conversations'])->name('conversations');
        Route::post('/conversations/start', [HamsaController::class, 'startConversation'])->name('conversations.start');
        Route::get('/conversations/{conversationId}', [HamsaController::class, 'getConversation'])->name('conversations.show');
        
        // Jobs
        Route::get('/jobs', [HamsaController::class, 'jobs'])->name('jobs');
        Route::get('/jobs/{jobId}', [HamsaController::class, 'getJob'])->name('jobs.show');
        
        // Usage Statistics
        Route::get('/usage', [HamsaController::class, 'usage'])->name('usage');
        
        // Project Settings
        Route::get('/project', [HamsaController::class, 'project'])->name('project');
    });



    Route::get('/user-dashboard', [HomeController::class, 'index'])->name('user.home');
    Route::get('/task-detailshamsa/{id}', [TaskController::class, 'checkTaskStatus'])->name('task.detailshamsa');
    Route::get('/agent-dashboard', function () {
        return view('agent.dashboard');
    })->name('agent.dashboard');
    Route::get('/agent-coaching', function () {
        return view('agent.coaching');
    })->name('agent.coaching');
    Route::get('/supervisor-dashboard', function () {
        return view('supervisor.dashboard');
    })->name('supervisor.dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{id}/activate', [UserController::class, 'activate'])->name('users.activate');
    Route::get('/users/{id}/change-password', [UserController::class, 'showChangePasswordForm'])->name('users.change-password.form');
    Route::post('/users/{id}/change-password', [UserController::class, 'changePassword'])->name('users.change-password');
    
    // Role management routes
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/{id}', [RoleController::class, 'show'])->name('roles.show');
        Route::get('/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/{id}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
        
        // API endpoints for AJAX calls
        Route::get('/api/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
    });

    Route::get('/user-profile', [HomeController::class, 'profile'])->name('user.profile');
    Route::put('/user-profile/update', [HomeController::class, 'updateProfile'])->name('user.profile.update');
    Route::get('/user/subscription', [HomeController::class, 'subscription'])->name('user.subscription');
    Route::get('/performance-badges', [HomeController::class, 'performanceBadges'])->name('user.performance_badges');

    // Group Routs start
    Route::get('/user/group_data/list', [GroupDataController::class, 'groupList'])->name('user.group_data.list');
    Route::get('/user/group_data/create', [GroupDataController::class, 'groupCreate'])->name('user.group_data.create');
    Route::get('/user/group_data/details/{id}', [GroupDataController::class, 'groupEdit'])->name('user.group_data.details');
    Route::post('/user/group_data/store', [GroupDataController::class, 'groupStore'])->name('user.group_data.store');
    Route::post('/user/group_data/update', [GroupDataController::class, 'groupUpdate'])->name('user.group_data.update');
    Route::any('/user/group_data/delete/{id}', [GroupDataController::class, 'groupDelete'])->name('user.group_data.delete');

    // Company Routs start 
    Route::get('/user/company/list', [CompanyController::class, 'companyList'])->name('user.company.list');
    Route::get('/user/company/create', [CompanyController::class, 'companyCreate'])->name('user.company.create');
    Route::get('/user/company/view/{id}', [CompanyController::class, 'companyDetails'])->name('user.company.view');
    Route::get('/user/company/edit/{id}', [CompanyController::class, 'companyEdit'])->name('user.company.edit');
    Route::post('/user/company/store', [CompanyController::class, 'companyStore'])->name('user.company.store');
    Route::put('/user/company/update/{id}', [CompanyController::class, 'companyUpdate'])->name('user.company.update');
    Route::get('/user/company/delete/{id}', [CompanyController::class, 'companyDelete'])->name('user.company.delete');


    // Task routes start
    Route::post('/user/task/store', [TaskController::class, 'taskStore'])->name('user.task.store');
    Route::get('/user/task/details/{workId}', [TaskController::class, 'taskDetails'])->name('user.task.details');
    Route::get('/user/task/delete/{workId}', [TaskController::class, 'deleteTask'])->name('user.task.delete');
    Route::get('/user/task/list/{companyId}', [TaskController::class, 'TaskList'])->name('user.task.list');
    Route::get('/user/task/check-status/{taskId}', [TaskController::class, 'checkTaskStatus'])->name('user.task.checkStatus');
    Route::get('/user/company/evaluate/{id}', [TaskController::class, 'reEvaluateTask'])->name('user.company.evaluate');
    Route::get('/user/extractions/agent-wise', [TaskController::class, 'agentWiseExtractions'])->name('user.extractions.agent-wise');

    // ─── Hamsa Job Lookup (JSON API) ───────────────────────────────
    // Pull raw Hamsa job by Hamsa job ID   → /api/hamsa/job/{jobId}
    Route::get('/api/hamsa/job/{jobId}', [TaskController::class, 'fetchHamsaJob'])->name('api.hamsa.job');
    // Pull Hamsa job by local Task DB ID   → /api/task/{taskId}/hamsa-job
    Route::get('/api/task/{taskId}/hamsa-job', [TaskController::class, 'fetchTaskHamsaJob'])->name('api.task.hamsaJob');
    // Support route start
    Route::get('/user/support', [HomeController::class, 'support'])->name('user.support');

    // Agent routes start
    // Route::post('/user/agent/store', [AgentController::class, 'agentStore'])->name('user.agent.store');
    Route::get('/user/agent/details', [AgentController::class, 'agentDetails'])->name('user.agent.details');
    // Route::get('/user/agent/delete', [AgentController::class, 'agentTask'])->name('user.agent.delete');
    // Route::get('/user/agent/list', [AgentController::class, 'agentList'])->name('user.agent.list');
    // Route::get('/user/agent/create', [AgentController::class, 'agentCreate'])->name('user.agent.create');
    // Route::get('/user/agent/edit', [AgentController::class, 'agentEdit'])->name('user.agent.edit');

    Route::get('/agents/dashboard', [AgentController::class, 'dashboard'])->name('user.agents.dashboard');
    Route::get('/agents', [AgentController::class, 'index'])->name('user.agents.index');
    Route::get('/agents/{agentId}', [AgentController::class, 'show'])->name('user.agents.show');
    Route::get('/agents/{agentId}/performance', [AgentController::class, 'performanceHistory'])->name('user.agents.performance');
    Route::get('/agents/{agentId}/performance-data', [AgentController::class, 'getPerformanceData'])->name('agents.performance.data');

    // Knowledge Base routes start
    Route::get('/user/knowledgeBase/list', [KnowledgeBaseController::class, 'knowledgeBaseList'])->name('user.knowledgeBase.list');
    Route::post('/user/knowledgeBase/search-test', [KnowledgeBaseController::class, 'knowledgeBaseSearch'])->name('user.knowledgeBase.searchTest');
    Route::get('/user/knowledgeBase/create', [KnowledgeBaseController::class, 'knowledgeBaseCreate'])->name('user.knowledgeBase.create');
    Route::post('/user/knowledgeBase/store', [KnowledgeBaseController::class, 'knowledgeBaseStore'])->name('user.knowledgeBase.store');
    Route::get('/user/knowledgeBase/details/{id}', [KnowledgeBaseController::class, 'knowledgeBaseDetails'])->name('user.knowledgeBase.details');
    Route::get('/user/knowledgeBase/edit/{id}', [KnowledgeBaseController::class, 'knowledgeBaseEdit'])->name('user.knowledgeBase.edit');
    Route::post('/user/knowledgeBase/update/{id}', [KnowledgeBaseController::class, 'knowledgeBaseUpdate'])->name('user.knowledgeBase.update');
    Route::get('/user/knowledgeBase/delete/{id}', [KnowledgeBaseController::class, 'knowledgeBaseDelete'])->name('user.knowledgeBase.delete');
    Route::get('/user/knowledgeBase/simulator', [KnowledgeBaseController::class, 'kbSimulator'])->name('user.knowledgeBase.simulator');
    Route::post('/user/knowledgeBase/simulator', [KnowledgeBaseController::class, 'kbSimulatorRun'])->name('user.knowledgeBase.simulator.run');

    Route::prefix('voice-pint')->name('user.voice-pint.')->group(function () {
        Route::get('/', [VoicePintController::class, 'index'])->name('index');
        Route::post('/upload', [VoicePintController::class, 'upload'])->name('upload');
        Route::get('/delete/{filename}', [VoicePintController::class, 'delete'])->name('delete');
        Route::get('/clear-all', [VoicePintController::class, 'deleteAll'])->name('clear-all');
        Route::get('/stream/{filename}', [VoicePintController::class, 'stream'])->name('stream');
    });

    Route::prefix('telephony-accounts')->name('user.telephonyAccounts.')->group(function () {
        Route::get('/', [TelephonyAccountController::class, 'index'])->name('index');
        Route::get('/create', [TelephonyAccountController::class, 'create'])->name('create');
        Route::post('/', [TelephonyAccountController::class, 'store'])->name('store');
        Route::put('/{telephonyAccount}', [TelephonyAccountController::class, 'update'])->name('update');
        Route::delete('/{telephonyAccount}', [TelephonyAccountController::class, 'destroy'])->name('destroy');
    });

});


































// // Authentication Routes
// Route::get('/login', [ApiAuthController::class, 'showLoginForm'])->name('login');
// Route::post('/login', [ApiAuthController::class, 'login']);
// Route::post('/logout', [ApiAuthController::class, 'logout'])->name('logout');

// // Protected Routes
// Route::middleware(['auth'])->group(function () {
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');

//     // User Management Routes
//     Route::prefix('users')->group(function () {
//         Route::get('/', [UserController::class, 'index'])->name('users.index');
//         Route::get('/create', [UserController::class, 'create'])->name('users.create');
//         Route::post('/', [UserController::class, 'store'])->name('users.store');
//         Route::get('/{id}', [UserController::class, 'show'])->name('users.show');
//         Route::get('/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
//         Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
//         Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
//         Route::get('/{id}/change-password', [UserController::class, 'showChangePasswordForm'])->name('users.change-password');
//         Route::post('/{id}/change-password', [UserController::class, 'changePassword'])->name('users.change-password.post');
//     });
// });

// Route::get('/', function () {
//     return redirect()->route('dashboard');
// });




// // Login route 
// Route::get('/', function () {
//     return redirect()->route('login'); 
// });

// // Google authentication route 
// Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
// Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// Auth::routes();

// =================User routes ======================




// // =================Admin routes ======================
// Route::prefix('admin')->name('admin.')->group(function () {
//     Route::middleware('guest:admin')->group(function () {
//         Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
//         Route::post('login', [AdminAuthController::class, 'login']);
//     });
    
//     Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
// });

// Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {

//     Route::get('dashboard', [AdminController::class, 'index'])->name('dashboard');

//     Route::resource('admins', AdminController::class)->except(['show']);

//     Route::get('plans', [PlanController::class, 'index'])->name('plans.index');
//     Route::get('plans/create', [PlanController::class, 'create'])->name('plans.create');
//     Route::post('plans', [PlanController::class, 'store'])->name('plans.store');
//     Route::get('plans/{plan}', [PlanController::class, 'show'])->name('plans.show');
//     Route::get('plans/{plan}/edit', [PlanController::class, 'edit'])->name('plans.edit');
//     Route::put('plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
//     Route::delete('plans/{plan}', [PlanController::class, 'destroy'])->name('plans.destroy');

//     Route::prefix('plans/{plan}')->group(function () {
//         Route::get('features', [PlanController::class, 'editFeatures'])->name('plans.features.edit');
//         Route::put('features', [PlanController::class, 'updateFeatures'])->name('plans.features.update');
//     });

//     Route::get('features', [FeatureController::class, 'index'])->name('features.index');
//     Route::get('features/create', [FeatureController::class, 'create'])->name('features.create');
//     Route::post('features', [FeatureController::class, 'store'])->name('features.store');
//     Route::get('features/{feature}', [FeatureController::class, 'show'])->name('features.show');
//     Route::get('features/{feature}/edit', [FeatureController::class, 'edit'])->name('features.edit');
//     Route::put('features/{feature}', [FeatureController::class, 'update'])->name('features.update');
//     Route::delete('features/{feature}', [FeatureController::class, 'destroy'])->name('features.destroy');

//     Route::get('subs/preview', [PlanController::class, 'preview'])->name('plans.preview');
//     Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
//     Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
// });
