<?php

use App\Http\Controllers\DocsController;
use App\Http\Controllers\Web\AdminWebController;
use Illuminate\Support\Facades\Route;

// -------------------------------------------------------
// Home / Landing Page
// -------------------------------------------------------
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/privacy-policy', function () {
    return view('privacy');
})->name('privacy-policy');

Route::get('/terms-and-conditions', function () {
    return view('terms');
})->name('terms-and-conditions');

// -------------------------------------------------------
// Swagger / OpenAPI Interactive Documentation
// -------------------------------------------------------
Route::get('/docs', [DocsController::class, 'index'])->name('docs');
Route::get('/docs/openapi.json', [DocsController::class, 'openApiJson'])->name('docs.json');
Route::get('/openapi.json', [DocsController::class, 'openApiJson']);

// -------------------------------------------------------
// Global Login Route Alias (Points to Admin Login)
// -------------------------------------------------------
Route::get('/login', [AdminWebController::class, 'showLogin'])->name('login');

// -------------------------------------------------------
// Admin Panel Web Routes
// -------------------------------------------------------
Route::get('/admin', function () {
    return redirect()->route('admin.dashboard');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminWebController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminWebController::class, 'login'])->name('login.submit');
    Route::post('logout', [AdminWebController::class, 'logout'])->name('logout');

    // Protected admin pages
    Route::middleware('admin_auth')->group(function () {
        Route::get('dashboard', [AdminWebController::class, 'dashboard'])->name('dashboard');
        Route::get('admins', [AdminWebController::class, 'admins'])->name('admins');
        Route::get('students', [AdminWebController::class, 'students'])->name('students');
        Route::get('teachers', [AdminWebController::class, 'teachers'])->name('teachers');
        Route::get('subjects', [AdminWebController::class, 'subjects'])->name('subjects');
        Route::get('content', [AdminWebController::class, 'content'])->name('content');
        Route::get('tests', [AdminWebController::class, 'tests'])->name('tests');
        Route::get('subscriptions', [AdminWebController::class, 'subscriptions'])->name('subscriptions');
        Route::get('notifications', [AdminWebController::class, 'notifications'])->name('notifications');
    });
});
