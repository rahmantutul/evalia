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
    DashboardController,
    SettingsController
};
use Illuminate\Support\Facades\Artisan;
Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('clear-compiled');
    Artisan::call('optimize:clear');
    return redirect()->back()->with('success', 'Cache cleared successfully!');
})->name('cache.clear');


Route::get('/', function () {
    return redirect()->route('login'); 
});

Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Auth::routes();


Route::group(['middleware' => 'auth'], function () {
    Route::get('/user-dashboard', [HomeController::class, 'index'])->name('home');
    Route::get('/user-profile', [HomeController::class, 'profile'])->name('user.profile');
    Route::put('/user-profile/update', [HomeController::class, 'updateProfile'])->name('user.profile.update');
    Route::get('/user/subscription', [HomeController::class, 'subscription'])->name('user.subscription');
    Route::get('/user/bots', [HomeController::class, 'bots'])->name('user.bot.list');
    Route::get('/user/bot/create', [HomeController::class, 'bot_create'])->name('user.bot.create');
    Route::get('/user/bot/store', [HomeController::class, 'bot_store'])->name('user.bot.store');
    Route::get('/user/bot/overview', [HomeController::class, 'overview'])->name('user.bot.overview');
    Route::get('/user/bot/inbox', [HomeController::class, 'inbox'])->name('user.bot.inbox');
    Route::get('/user/bot/support', [HomeController::class, 'support'])->name('user.support');
});

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
