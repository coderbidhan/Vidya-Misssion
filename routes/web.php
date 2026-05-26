<?php

use App\Http\Controllers\Web\AdminWebController;
use Illuminate\Support\Facades\Route;

// -------------------------------------------------------
// Home / Landing Page
// -------------------------------------------------------
Route::get('/', function () {
    return view('home');
})->name('home');

// -------------------------------------------------------
// Admin Panel — Auth (no session required)
// -------------------------------------------------------
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login',  [AdminWebController::class, 'showLogin']) ->name('login');
    Route::post('login', [AdminWebController::class, 'login'])     ->name('login.submit');
    Route::post('logout',[AdminWebController::class, 'logout'])    ->name('logout');

    // Protected admin pages
    Route::middleware('admin_auth')->group(function () {
        Route::get('dashboard',     [AdminWebController::class, 'dashboard'])     ->name('dashboard');
        Route::get('admins',        [AdminWebController::class, 'admins'])        ->name('admins.creation');
        Route::get('students',      [AdminWebController::class, 'students'])      ->name('students.creation');
        Route::get('teachers',      [AdminWebController::class, 'teachers'])      ->name('teachers.creation');
        Route::get('subjects',      [AdminWebController::class, 'subjects'])      ->name('subjects');
        Route::get('content',       [AdminWebController::class, 'content'])       ->name('content');
        Route::get('tests',         [AdminWebController::class, 'tests'])         ->name('tests');
        Route::get('subscriptions', [AdminWebController::class, 'subscriptions']) ->name('subscriptions');
        Route::get('notifications', [AdminWebController::class, 'notifications']) ->name('notifications');
    });
});
