<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$teacher = App\Models\User::where('role', 'teacher')->first();

$request = Illuminate\Http\Request::create('/api/admin/teachers/' . $teacher->id, 'PUT', [
    'name' => 'Updated Teacher',
    'email' => 'newemail@test.com',
    'mobile' => '9999999999',
    'password' => '',
    'qualification' => 'Ph.D',
    'bio' => 'New bio',
    'is_active' => true
]);

// We can test validation manually
$rules = [
    'name'          => ['sometimes', 'string', 'max:255'],
    'email'         => ['sometimes', 'email', 'unique:users,email,' . $teacher->id],
    'mobile'        => ['nullable', 'string', 'max:20'],
    'password'      => ['nullable', 'string', 'min:6'],
    'bio'           => ['nullable', 'string'],
    'qualification' => ['nullable', 'string', 'max:255'],
    'is_active'     => ['sometimes', 'boolean'],
];

try {
    $validator = Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        echo json_encode(['errors' => $validator->errors()]);
    } else {
        echo json_encode(['success' => true]);
    }
} catch (\Exception $e) {
    echo $e->getMessage();
}
