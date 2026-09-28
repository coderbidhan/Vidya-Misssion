<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Chapter;
use App\Models\LiveClass;
use App\Models\Note;
use App\Models\RecordedClass;
use App\Models\Subject;
use App\Models\Subscription;
use App\Models\Test;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

echo "========================================================\n";
echo "   COMPREHENSIVE FULL SUITE API TEST (ALL 60+ ROUTES)   \n";
echo "========================================================\n\n";

$student = User::where('role', 'student')->first();
$student->update([
    'password' => Hash::make('password123'),
    'trial_starts_at' => now(),
    'trial_ends_at' => now()->addDays(3),
    'is_active' => true,
]);
$studentToken = $student->createToken('test-student')->plainTextToken;

$teacher = User::where('role', 'teacher')->first();
$teacherToken = $teacher->createToken('test-teacher')->plainTextToken;

$admin = User::where('role', 'admin')->first();
$adminToken = $admin->createToken('test-admin')->plainTextToken;

$subject = Subject::first();
$subjectId = $subject ? $subject->id : 1;

$chapter = Chapter::first();
$chapterId = $chapter ? $chapter->id : 1;

$recorded = RecordedClass::first();
$recordedId = $recorded ? $recorded->id : 1;

$live = LiveClass::first();
$liveId = $live ? $live->id : 1;

$note = Note::first();
$noteId = $note ? $note->id : 1;

$test = Test::first();
$testId = $test ? $test->id : 1;

$plan = Subscription::first();
$planId = $plan ? $plan->id : 1;

$endpoints = [
    // --- Public & Auth ---
    ['GET', '/api/privacy-policy', [], null, 'Public Privacy Policy'],
    ['GET', '/api/terms-and-conditions', [], null, 'Public Terms'],
    ['GET', '/api/schools', ['q' => 'School'], null, 'Schools Catalog Search'],
    ['GET', '/api/schools/districts', [], null, 'Districts List'],
    ['GET', '/api/app/share-info', [], null, 'App Share Info'],
    ['POST', '/api/auth/send-otp', ['mobile' => '98'.rand(10000000, 99999999)], null, 'Send OTP via Mobile'],
    ['POST', '/api/auth/login', ['mobile' => '9800000001', 'password' => 'password123'], null, 'Student Mobile Login'],
    ['POST', '/api/auth/forgot-password', ['mobile' => '9800000001', 'password' => 'password123', 'password_confirmation' => 'password123'], null, 'Forgot Password Reset'],

    // --- Student Profile & Learning ---
    ['GET', '/api/profile', [], $studentToken, 'Get Student Profile'],
    ['PUT', '/api/profile/update', ['name' => 'Test Student Updated', 'school_name' => 'Hare School', 'district' => 'Kolkata'], $studentToken, 'Update Profile'],
    ['GET', '/api/student/dashboard', [], $studentToken, 'Student Dashboard'],
    ['GET', '/api/student/exam-reports', [], $studentToken, 'Student Exam Reports'],
    ['GET', '/api/student/history', [], $studentToken, 'Student Learning History'],
    ['GET', '/api/notifications', [], $studentToken, 'Student Notifications'],

    // --- Subscriptions ---
    ['GET', '/api/subscriptions', [], null, 'List Subscription Plans'],
    ['GET', '/api/subscriptions/current', [], $studentToken, 'Current Student Subscription'],
    ['GET', '/api/subscriptions/history', [], $studentToken, 'Student Subscription History'],
    ['POST', "/api/subscriptions/{$planId}/initiate", [], $studentToken, 'Initiate Subscription Payment'],

    // --- Curriculum & Content (Student) ---
    ['GET', '/api/subjects', [], $studentToken, 'List Subjects for Student'],
    ['GET', "/api/subjects/{$subjectId}/chapters", [], $studentToken, 'List Chapters of Subject'],
    ['GET', "/api/chapters/{$chapterId}", [], $studentToken, 'Get Chapter Details'],
    ['POST', "/api/chapters/{$chapterId}/complete", [], $studentToken, 'Mark Chapter Completed'],
    ['GET', "/api/chapters/{$chapterId}/recorded-classes", [], $studentToken, 'Recorded Classes by Chapter'],
    ['GET', "/api/recorded-classes/{$recordedId}", [], $studentToken, 'Get Recorded Class Embed URL'],
    ['POST', "/api/recorded-classes/{$recordedId}/complete", [], $studentToken, 'Complete Video Reward'],
    ['GET', "/api/chapters/{$chapterId}/live-classes", [], $studentToken, 'Live Classes by Chapter'],
    ['POST', "/api/live-classes/{$liveId}/attend", [], $studentToken, 'Attend Live Class Reward'],
    ['GET', "/api/chapters/{$chapterId}/notes", [], $studentToken, 'Notes by Chapter'],
    ['GET', "/api/notes/{$noteId}", [], $studentToken, 'Get Note Details'],
    ['GET', "/api/chapters/{$chapterId}/tests", [], $studentToken, 'Tests by Chapter'],
    ['GET', "/api/tests/{$testId}", [], $studentToken, 'Get Test Questions'],
    ['POST', "/api/tests/{$testId}/attempt", ['answers' => [['question_id' => 1, 'selected_option' => 'A']], 'time_taken_seconds' => 120], $studentToken, 'Submit Test Attempt'],

    // --- Teacher Portal ---
    ['GET', '/api/teacher/dashboard', [], $teacherToken, 'Teacher Dashboard'],
    ['GET', '/api/teacher/subjects', [], $teacherToken, 'Teacher Subjects'],

    // --- Admin Portal (Reads & Updates) ---
    ['GET', '/api/admin/admins', [], $adminToken, 'Admin List Admins'],
    ['GET', '/api/admin/students', [], $adminToken, 'Admin List Students'],
    ['PUT', "/api/admin/students/{$student->id}", ['is_active' => true], $adminToken, 'Admin Update Student'],
    ['POST', "/api/admin/students/{$student->id}/grant-subscription", ['days' => 30], $adminToken, 'Admin Grant Free Subscription'],
    ['GET', '/api/admin/teachers', [], $adminToken, 'Admin List Teachers'],
    ['GET', '/api/admin/subjects', [], $adminToken, 'Admin List Subjects'],
    ['GET', "/api/admin/subjects/{$subjectId}/chapters", [], $adminToken, 'Admin Subject Chapters'],
    ['GET', '/api/admin/class-subjects/all', [], $adminToken, 'Admin Class Subject Mappings'],
    ['GET', "/api/admin/class-subjects/9/chapters?subject_id={$subjectId}", [], $adminToken, 'Admin Class Chapters'],
    ['GET', '/api/admin/recorded-classes', [], $adminToken, 'Admin List Recorded Classes'],
    ['GET', '/api/admin/live-classes', [], $adminToken, 'Admin List Live Classes'],
    ['PATCH', "/api/admin/live-classes/{$liveId}/status", ['status' => 'scheduled'], $adminToken, 'Admin Update Live Class Status'],
    ['GET', '/api/admin/notes', [], $adminToken, 'Admin List Notes'],
    ['GET', '/api/admin/tests', [], $adminToken, 'Admin List Tests'],
    ['GET', '/api/admin/subscription-plans', [], $adminToken, 'Admin List Plans'],
    ['GET', '/api/admin/user-subscriptions', [], $adminToken, 'Admin List User Subscriptions'],
    ['GET', '/api/admin/analytics/overview', [], $adminToken, 'Admin Analytics Overview'],
    ['GET', '/api/admin/analytics/popular-subjects', [], $adminToken, 'Admin Popular Subjects'],
    ['GET', '/api/admin/analytics/test-performance', [], $adminToken, 'Admin Test Performance'],
    ['POST', '/api/admin/notifications/broadcast', ['title' => 'Test Notification', 'message' => 'Hello students!'], $adminToken, 'Admin Broadcast Notification'],
];

$passed = 0;
$failed = 0;

foreach ($endpoints as $ep) {
    [$method, $uri, $payload, $token, $desc] = $ep;

    $parsed = parse_url($uri);
    $path = $parsed['path'];
    $query = [];
    if (isset($parsed['query'])) {
        parse_str($parsed['query'], $query);
    }

    $headers = [
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_CONTENT_TYPE' => 'application/json',
    ];
    if ($token) {
        $headers['HTTP_AUTHORIZATION'] = 'Bearer '.$token;
    }

    $parameters = array_merge($query, $payload);
    $content = ($method !== 'GET' && ! empty($payload)) ? json_encode($payload) : null;

    $server = array_merge($_SERVER, $headers);

    // Clear previously cached user in current PHP process
    auth()->forgetGuards();
    app('auth')->forgetGuards();

    $req = Request::create($uri, $method, $parameters, [], [], $server, $content);

    try {
        $res = $app->handle($req);
        $status = $res->getStatusCode();

        if ($status >= 200 && $status < 400) {
            echo " [PASS] {$method} {$uri} ({$status}) - {$desc}\n";
            $passed++;
        } else {
            echo " [FAIL] {$method} {$uri} ({$status}) - {$desc}\n";
            $body = $res->getContent();
            $decoded = json_decode($body, true);
            $msg = $decoded['message'] ?? substr($body, 0, 150);
            echo "        -> Error: {$msg}\n";
            $failed++;
        }
    } catch (Throwable $e) {
        echo " [EXCEPTION] {$method} {$uri} - {$desc}\n";
        echo '        -> '.$e->getMessage()."\n";
        $failed++;
    }
}

echo "\n--------------------------------------------------------\n";
echo 'TOTAL: '.count($endpoints)." | PASSED: {$passed} | FAILED: {$failed}\n";
echo "--------------------------------------------------------\n";
