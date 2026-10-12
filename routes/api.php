<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\ChapterController as AdminChapterController;
use App\Http\Controllers\Admin\ClassSubjectController;
use App\Http\Controllers\Admin\LiveClassController as AdminLiveClassController;
use App\Http\Controllers\Admin\NoteController as AdminNoteController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\RecordedClassController as AdminRecordedClassController;
use App\Http\Controllers\Admin\SubjectController as AdminSubjectController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\TestController as AdminTestController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\StudentAuthController;
use App\Http\Controllers\Student\ChapterController;
use App\Http\Controllers\Student\LiveClassController;
use App\Http\Controllers\Student\NoteController;
use App\Http\Controllers\Student\NotificationController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\RecordedClassController;
use App\Http\Controllers\Student\SubjectController;
use App\Http\Controllers\Student\SubscriptionController;
use App\Http\Controllers\Student\TestController;
use App\Http\Controllers\Teacher\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes (no authentication required)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('send-otp', [OtpController::class, 'sendOtp'])->name('auth.send-otp');
    Route::post('verify-otp', [OtpController::class, 'verifyOtp'])->name('auth.verify-otp');
    Route::post('register', [StudentAuthController::class, 'register'])->name('auth.register');
    Route::post('login', [StudentAuthController::class, 'login'])->name('auth.login');
    Route::post('forgot-password', [StudentAuthController::class, 'forgotPassword'])->middleware('auth:sanctum')->name('auth.forgot-password');
    Route::post('logout', [OtpController::class, 'logout'])->middleware('auth:sanctum')->name('auth.logout');
});

/*
|--------------------------------------------------------------------------
| Payment Webhook (no auth — HMAC verified inside controller)
|--------------------------------------------------------------------------
*/
Route::post('subscriptions/webhook', [SubscriptionController::class, 'webhook'])->name('subscriptions.webhook');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    /*
    |----------------------------------------------------------------------
    | Profile Routes (auth required, no trial check)
    |----------------------------------------------------------------------
    */
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::post('/setup', [ProfileController::class, 'setup'])->name('profile.setup');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
    });

    /*
    |----------------------------------------------------------------------
    | Subscription Routes (auth required, NO trial check)
    |----------------------------------------------------------------------
    */
    Route::prefix('subscriptions')->group(function () {
        Route::get('/', [SubscriptionController::class, 'plans'])->name('subscriptions.plans');
        Route::get('/current', [SubscriptionController::class, 'current'])->name('subscriptions.current');
        Route::post('/{id}/initiate', [SubscriptionController::class, 'initiate'])->name('subscriptions.initiate');
    });

    /*
    |----------------------------------------------------------------------
    | Notification Routes (auth required, no trial check)
    |----------------------------------------------------------------------
    */
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    });

    /*
    |----------------------------------------------------------------------
    | Content Routes (auth required)
    |----------------------------------------------------------------------
    */
    // Subjects
    Route::get('subjects', [SubjectController::class, 'index'])->name('subjects.index');
    Route::get('subjects/{id}/chapters', [ChapterController::class, 'bySubject'])->name('subjects.chapters');

    // Chapters
    Route::get('chapters/{id}', [ChapterController::class, 'show'])->name('chapters.show');
    Route::post('chapters/{id}/complete', [ChapterController::class, 'complete'])->name('chapters.complete');

    // Recorded Classes
    Route::get('chapters/{id}/recorded-classes', [RecordedClassController::class, 'byChapter'])->name('recorded-classes.by-chapter');
    Route::get('recorded-classes/{id}', [RecordedClassController::class, 'show'])->name('recorded-classes.show');

    // Live Classes
    Route::get('chapters/{id}/live-classes', [LiveClassController::class, 'byChapter'])->name('live-classes.by-chapter');

    // Notes
    Route::get('chapters/{id}/notes', [NoteController::class, 'byChapter'])->name('notes.by-chapter');
    Route::get('notes/{id}', [NoteController::class, 'show'])->name('notes.show');

    // Tests
    Route::get('chapters/{id}/tests', [TestController::class, 'byChapter'])->name('tests.by-chapter');
    Route::get('tests/{id}', [TestController::class, 'show'])->name('tests.show');
    Route::post('tests/{id}/attempt', [TestController::class, 'attempt'])->name('tests.attempt');

    /*
    |----------------------------------------------------------------------
    | Teacher Routes (auth + role:teacher)
    |----------------------------------------------------------------------
    */
    Route::middleware('role:teacher')->prefix('teacher')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('teacher.dashboard');
        Route::get('/subjects', [DashboardController::class, 'subjects'])->name('teacher.subjects');
    });

    /*
    |----------------------------------------------------------------------
    | Admin Routes (auth + role:admin)
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->group(function () {

        // User management
        Route::get('admins', [UserController::class, 'admins'])->name('admin.admins');
        Route::post('admins', [UserController::class, 'createAdmin'])->name('admin.admins.create');
        Route::put('admins/{id}', [UserController::class, 'updateAdmin'])->name('admin.admins.update');
        Route::delete('admins/{id}', [UserController::class, 'deleteAdmin'])->name('admin.admins.delete');

        Route::get('students', [UserController::class, 'students'])->name('admin.students');
        Route::put('students/{id}', [UserController::class, 'updateStudent'])->name('admin.students.update');
        Route::get('teachers', [UserController::class, 'teachers'])->name('admin.teachers');
        Route::post('teachers', [UserController::class, 'createTeacher'])->name('admin.teachers.create');
        Route::put('teachers/{id}', [UserController::class, 'updateTeacher'])->name('admin.teachers.update');
        Route::delete('teachers/{id}', [UserController::class, 'deleteTeacher'])->name('admin.teachers.delete');
        Route::post('teachers/{id}/assign-subjects', [UserController::class, 'assignSubjects'])->name('admin.teachers.assign-subjects');

        // Subjects
        Route::get('subjects', [AdminSubjectController::class, 'index'])->name('admin.subjects.index');
        Route::post('subjects', [AdminSubjectController::class, 'store'])->name('admin.subjects.store');
        Route::put('subjects/{id}', [AdminSubjectController::class, 'update'])->name('admin.subjects.update');
        Route::delete('subjects/{id}', [AdminSubjectController::class, 'destroy'])->name('admin.subjects.destroy');
        Route::get('subjects/{id}/chapters', [AdminSubjectController::class, 'chapters'])->name('admin.subjects.chapters');

        // Class → Subject mapping
        Route::get('class-subjects', [ClassSubjectController::class, 'index'])->name('admin.class-subjects.index');
        Route::get('class-subjects/all', [ClassSubjectController::class, 'allMappings'])->name('admin.class-subjects.all');
        Route::post('class-subjects', [ClassSubjectController::class, 'store'])->name('admin.class-subjects.store');
        Route::delete('class-subjects/{classLevel}/{subjectId}', [ClassSubjectController::class, 'destroy'])->name('admin.class-subjects.destroy');
        Route::get('class-subjects/{classLevel}/chapters', [ClassSubjectController::class, 'chapters'])->name('admin.class-subjects.chapters');

        // Chapters
        Route::post('chapters', [AdminChapterController::class, 'store'])->name('admin.chapters.store');
        Route::put('chapters/{id}', [AdminChapterController::class, 'update'])->name('admin.chapters.update');
        Route::delete('chapters/{id}', [AdminChapterController::class, 'destroy'])->name('admin.chapters.destroy');

        // Recorded Classes
        Route::get('recorded-classes', [AdminRecordedClassController::class, 'index'])->name('admin.recorded-classes.index');
        Route::post('recorded-classes', [AdminRecordedClassController::class, 'store'])->name('admin.recorded-classes.store');
        Route::put('recorded-classes/{id}', [AdminRecordedClassController::class, 'update'])->name('admin.recorded-classes.update');
        Route::delete('recorded-classes/{id}', [AdminRecordedClassController::class, 'destroy'])->name('admin.recorded-classes.destroy');

        // Live Classes
        Route::get('live-classes', [AdminLiveClassController::class, 'index'])->name('admin.live-classes.index');
        Route::post('live-classes', [AdminLiveClassController::class, 'store'])->name('admin.live-classes.store');
        Route::put('live-classes/{id}', [AdminLiveClassController::class, 'update'])->name('admin.live-classes.update');
        Route::patch('live-classes/{id}/status', [AdminLiveClassController::class, 'updateStatus'])->name('admin.live-classes.status');
        Route::delete('live-classes/{id}', [AdminLiveClassController::class, 'destroy'])->name('admin.live-classes.destroy');

        // Notes
        Route::get('notes', [AdminNoteController::class, 'index'])->name('admin.notes.index');
        Route::post('notes', [AdminNoteController::class, 'store'])->name('admin.notes.store');
        Route::put('notes/{id}', [AdminNoteController::class, 'update'])->name('admin.notes.update');
        Route::delete('notes/{id}', [AdminNoteController::class, 'destroy'])->name('admin.notes.destroy');

        // Tests
        Route::get('tests', [AdminTestController::class, 'index'])->name('admin.tests.index');
        Route::post('tests', [AdminTestController::class, 'store'])->name('admin.tests.store');
        Route::put('tests/{id}', [AdminTestController::class, 'update'])->name('admin.tests.update');
        Route::delete('tests/{id}', [AdminTestController::class, 'destroy'])->name('admin.tests.destroy');

        // Questions
        Route::post('tests/{id}/questions', [QuestionController::class, 'bulkStore'])->name('admin.questions.bulk');
        Route::put('questions/{id}', [QuestionController::class, 'update'])->name('admin.questions.update');
        Route::delete('questions/{id}', [QuestionController::class, 'destroy'])->name('admin.questions.destroy');

        // Subscription Plans
        Route::get('subscription-plans', [SubscriptionPlanController::class, 'index'])->name('admin.plans.index');
        Route::post('subscription-plans', [SubscriptionPlanController::class, 'store'])->name('admin.plans.store');
        Route::put('subscription-plans/{id}', [SubscriptionPlanController::class, 'update'])->name('admin.plans.update');
        Route::delete('subscription-plans/{id}', [SubscriptionPlanController::class, 'destroy'])->name('admin.plans.destroy');
        Route::get('user-subscriptions', [SubscriptionPlanController::class, 'userSubscriptions'])->name('admin.user-subscriptions');

        // Analytics
        Route::get('analytics/overview', [AnalyticsController::class, 'overview'])->name('admin.analytics.overview');
        Route::get('analytics/popular-subjects', [AnalyticsController::class, 'popularSubjects'])->name('admin.analytics.popular-subjects');
        Route::get('analytics/test-performance', [AnalyticsController::class, 'testPerformance'])->name('admin.analytics.test-performance');

        // Notifications
        Route::post('notifications/broadcast', [AdminNotificationController::class, 'broadcast'])->name('admin.notifications.broadcast');
    });
});
