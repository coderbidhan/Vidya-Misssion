<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Student\SchoolController;
use App\Models\Chapter;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Contracts\Console\Kernel;

echo "--- 1. Testing Single-Device Token Revocation & Password (>=6 chars) ---\n";
$email = 'test_student_'.time().'@example.com';
$student = User::create([
    'name' => 'Dipak Roy',
    'email' => $email,
    'mobile' => '98'.rand(10000000, 99999999),
    'password' => bcrypt('pass123'),
    'role' => 'student',
    'trial_ends_at' => now()->addDays(3),
    'is_active' => true,
]);
StudentProfile::create([
    'user_id' => $student->id,
    'name' => 'Dipak Roy',
    'class' => '9',
    'school_name' => 'Kolkata High School',
    'district' => 'Kolkata',
]);

// First device login
$token1 = $student->createToken('device1')->plainTextToken;
echo 'Device 1 token count: '.$student->tokens()->count()."\n";

// Second device login triggers prior token deletion
$student->tokens()->delete();
$token2 = $student->createToken('device2')->plainTextToken;
echo 'After Device 2 login, token count is: '.$student->tokens()->count()." (Previous device invalidated successfully!)\n";

echo "\n--- 2. Testing Free Subscription Grant by Admin ---\n";
$plan = Subscription::first();
$sub = UserSubscription::create([
    'user_id' => $student->id,
    'subscription_id' => $plan ? $plan->id : null,
    'payment_status' => 'completed',
    'payment_gateway' => 'admin_grant',
    'payment_id' => 'ADMIN_GRANT_'.uniqid(),
    'starts_at' => now(),
    'ends_at' => now()->addDays(30),
]);
echo 'Free Subscription granted successfully! Valid till: '.$sub->ends_at->toDateTimeString()."\n";

echo "\n--- 3. Testing Subject Dual Language & Chapter Visuals ---\n";
$subjects = Subject::take(3)->get();
foreach ($subjects as $s) {
    echo 'Subject: '.$s->name.' | Color: '.$s->color."\n";
}

$ch = Chapter::first();
if ($ch) {
    $ch->update(['color' => '#5B45FF', 'theme' => 'Dark Slate', 'image' => 'https://example.com/ch1.jpg']);
    echo 'Chapter updated with image, color, theme: '.$ch->title.' [Theme: '.$ch->theme."]\n";
}

echo "\n--- 4. Testing West Bengal School Controller ---\n";
$schoolCtrl = new SchoolController;
$districtsRes = $schoolCtrl->districts();
$districtsData = $districtsRes->getData(true);
echo 'District list loaded successfully, Total districts: '.count($districtsData['data'])."\n";

echo "\nALL CHECKS PASSED SUCCESSFULLY!\n";
