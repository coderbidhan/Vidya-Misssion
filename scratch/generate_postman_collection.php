<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$collection = [
    'info' => [
        'name' => 'Vidya Mission API',
        '_postman_id' => 'c891e45a-9310-4d40-9a2d-vidyamission01',
        'description' => "Official Postman Collection for **Vidya Mission** — West Bengal Board Online Learning & Examination Platform.\n\n### Environment Variables\n- `base_url`: `http://localhost:8000/api`\n- `token`: Bearer Sanctum Token (set automatically on Login/Register/Verify OTP)\n- `admin_token`: Bearer Sanctum Token for Admin requests\n- `teacher_token`: Bearer Sanctum Token for Teacher requests",
        'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
        'version' => '2.0.0',
    ],
    'variable' => [
        ['key' => 'base_url', 'value' => 'http://localhost:8000/api', 'type' => 'string'],
        ['key' => 'token', 'value' => '', 'type' => 'string'],
        ['key' => 'admin_token', 'value' => '', 'type' => 'string'],
        ['key' => 'teacher_token', 'value' => '', 'type' => 'string'],
        ['key' => 'student_id', 'value' => '1', 'type' => 'string'],
        ['key' => 'subject_id', 'value' => '1', 'type' => 'string'],
        ['key' => 'chapter_id', 'value' => '1', 'type' => 'string'],
        ['key' => 'class_id', 'value' => '1', 'type' => 'string'],
        ['key' => 'live_id', 'value' => '1', 'type' => 'string'],
        ['key' => 'note_id', 'value' => '1', 'type' => 'string'],
        ['key' => 'test_id', 'value' => '1', 'type' => 'string'],
        ['key' => 'plan_id', 'value' => '1', 'type' => 'string'],
        ['key' => 'teacher_id', 'value' => '1', 'type' => 'string'],
    ],
    'item' => [
        // ==========================================
        // 1. AUTHENTICATION
        // ==========================================
        [
            'name' => '1. Authentication',
            'item' => [
                [
                    'name' => 'Student Register',
                    'event' => [
                        [
                            'listen' => 'test',
                            'script' => [
                                'exec' => [
                                    'if (pm.response.code === 201 || pm.response.code === 200) {',
                                    '  var res = pm.response.json();',
                                    '  if (res.data && res.data.token) {',
                                    '    pm.environment.set("token", res.data.token);',
                                    '    pm.collectionVariables.set("token", res.data.token);',
                                    '  }',
                                    '}',
                                ],
                                'type' => 'text/javascript',
                            ],
                        ],
                    ],
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Accept', 'value' => 'application/json'],
                            ['key' => 'Content-Type', 'value' => 'application/json'],
                        ],
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'name' => 'Sourav Ganguly',
                                'mobile' => '9830012345',
                                'email' => 'sourav@vidyamission.in',
                                'password' => 'secret123',
                                'class' => '9',
                                'school_name' => 'Hare School',
                                'district' => 'Kolkata',
                            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                        ],
                        'url' => ['raw' => '{{base_url}}/auth/register', 'host' => ['{{base_url}}'], 'path' => ['auth', 'register']],
                    ],
                ],
                [
                    'name' => 'Student Login (Single Device Enforcement)',
                    'event' => [
                        [
                            'listen' => 'test',
                            'script' => [
                                'exec' => [
                                    'if (pm.response.code === 200) {',
                                    '  var res = pm.response.json();',
                                    '  if (res.data && res.data.token) {',
                                    '    pm.environment.set("token", res.data.token);',
                                    '    pm.collectionVariables.set("token", res.data.token);',
                                    '  }',
                                    '}',
                                ],
                                'type' => 'text/javascript',
                            ],
                        ],
                    ],
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Accept', 'value' => 'application/json'],
                            ['key' => 'Content-Type', 'value' => 'application/json'],
                        ],
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'mobile' => '9830012345',
                                'password' => 'secret123',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => ['raw' => '{{base_url}}/auth/login', 'host' => ['{{base_url}}'], 'path' => ['auth', 'login']],
                    ],
                ],
                [
                    'name' => 'Send OTP (Mobile / Email)',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Accept', 'value' => 'application/json'],
                            ['key' => 'Content-Type', 'value' => 'application/json'],
                        ],
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'mobile' => '9830012345',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => ['raw' => '{{base_url}}/auth/send-otp', 'host' => ['{{base_url}}'], 'path' => ['auth', 'send-otp']],
                    ],
                ],
                [
                    'name' => 'Verify OTP',
                    'event' => [
                        [
                            'listen' => 'test',
                            'script' => [
                                'exec' => [
                                    'if (pm.response.code === 200) {',
                                    '  var res = pm.response.json();',
                                    '  if (res.data && res.data.token) {',
                                    '    pm.environment.set("token", res.data.token);',
                                    '    pm.collectionVariables.set("token", res.data.token);',
                                    '  }',
                                    '}',
                                ],
                                'type' => 'text/javascript',
                            ],
                        ],
                    ],
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Accept', 'value' => 'application/json'],
                            ['key' => 'Content-Type', 'value' => 'application/json'],
                        ],
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'mobile' => '9830012345',
                                'otp' => '123456',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => ['raw' => '{{base_url}}/auth/verify-otp', 'host' => ['{{base_url}}'], 'path' => ['auth', 'verify-otp']],
                    ],
                ],
                [
                    'name' => 'Forgot Password (Reset)',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Accept', 'value' => 'application/json'],
                            ['key' => 'Content-Type', 'value' => 'application/json'],
                        ],
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'mobile' => '9830012345',
                                'password' => 'newSecret123',
                                'password_confirmation' => 'newSecret123',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => ['raw' => '{{base_url}}/auth/forgot-password', 'host' => ['{{base_url}}'], 'path' => ['auth', 'forgot-password']],
                    ],
                ],
                [
                    'name' => 'Logout',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/auth/logout', 'host' => ['{{base_url}}'], 'path' => ['auth', 'logout']],
                    ],
                ],
            ],
        ],

        // ==========================================
        // 2. STUDENT PROFILE & DASHBOARD
        // ==========================================
        [
            'name' => '2. Student Profile & Dashboard',
            'item' => [
                [
                    'name' => 'Get Profile',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/profile', 'host' => ['{{base_url}}'], 'path' => ['profile']],
                    ],
                ],
                [
                    'name' => 'Setup Profile (First Time)',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                            ['key' => 'Content-Type', 'value' => 'application/json'],
                        ],
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'name' => 'Sourav Ganguly',
                                'class' => '9',
                                'school_name' => 'Hare School',
                                'district' => 'Kolkata',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => ['raw' => '{{base_url}}/profile/setup', 'host' => ['{{base_url}}'], 'path' => ['profile', 'setup']],
                    ],
                ],
                [
                    'name' => 'Update Profile',
                    'request' => [
                        'method' => 'PUT',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                            ['key' => 'Content-Type', 'value' => 'application/json'],
                        ],
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'name' => 'Sourav Ganguly',
                                'class' => '9',
                                'school_name' => 'Hare School',
                                'district' => 'Kolkata',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => ['raw' => '{{base_url}}/profile/update', 'host' => ['{{base_url}}'], 'path' => ['profile', 'update']],
                    ],
                ],
                [
                    'name' => 'Upload Avatar Photo',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'body' => [
                            'mode' => 'formdata',
                            'formdata' => [
                                ['key' => 'avatar', 'type' => 'file', 'src' => ''],
                            ],
                        ],
                        'url' => ['raw' => '{{base_url}}/profile/avatar', 'host' => ['{{base_url}}'], 'path' => ['profile', 'avatar']],
                    ],
                ],
                [
                    'name' => 'Student Dashboard Summary',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/student/dashboard', 'host' => ['{{base_url}}'], 'path' => ['student', 'dashboard']],
                    ],
                ],
                [
                    'name' => 'Student Exam Reports',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/student/exam-reports', 'host' => ['{{base_url}}'], 'path' => ['student', 'exam-reports']],
                    ],
                ],
                [
                    'name' => 'Student Learning History',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/student/history', 'host' => ['{{base_url}}'], 'path' => ['student', 'history']],
                    ],
                ],
                [
                    'name' => 'Delete Account (Purge Data)',
                    'request' => [
                        'method' => 'DELETE',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/profile', 'host' => ['{{base_url}}'], 'path' => ['profile']],
                    ],
                ],
            ],
        ],

        // ==========================================
        // 3. CURRICULUM & SUBJECTS
        // ==========================================
        [
            'name' => '3. Curriculum & Subjects',
            'item' => [
                [
                    'name' => 'Get Subjects (Dual Bengali/English)',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => [
                            'raw' => '{{base_url}}/subjects?class_level=9',
                            'host' => ['{{base_url}}'],
                            'path' => ['subjects'],
                            'query' => [
                                ['key' => 'class_level', 'value' => '9', 'description' => 'Optional class exploration during 3-day trial'],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'Get Subject Chapters',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/subjects/{{subject_id}}/chapters', 'host' => ['{{base_url}}'], 'path' => ['subjects', '{{subject_id}}', 'chapters']],
                    ],
                ],
                [
                    'name' => 'Get Chapter Details',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/chapters/{{chapter_id}}', 'host' => ['{{base_url}}'], 'path' => ['chapters', '{{chapter_id}}']],
                    ],
                ],
                [
                    'name' => 'Complete Chapter (Reward)',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/chapters/{{chapter_id}}/complete', 'host' => ['{{base_url}}'], 'path' => ['chapters', '{{chapter_id}}', 'complete']],
                    ],
                ],
            ],
        ],

        // ==========================================
        // 4. RECORDED CLASSES & VIDEOS
        // ==========================================
        [
            'name' => '4. Recorded Video Classes',
            'item' => [
                [
                    'name' => 'Get Recorded Classes by Chapter',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/chapters/{{chapter_id}}/recorded-classes', 'host' => ['{{base_url}}'], 'path' => ['chapters', '{{chapter_id}}', 'recorded-classes']],
                    ],
                ],
                [
                    'name' => 'Get Video Embed Player URL',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/recorded-classes/{{class_id}}', 'host' => ['{{base_url}}'], 'path' => ['recorded-classes', '{{class_id}}']],
                    ],
                ],
                [
                    'name' => 'Record Video Watch Complete (+10 Points Reward)',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/recorded-classes/{{class_id}}/complete', 'host' => ['{{base_url}}'], 'path' => ['recorded-classes', '{{class_id}}', 'complete']],
                    ],
                ],
            ],
        ],

        // ==========================================
        // 5. LIVE CLASSES
        // ==========================================
        [
            'name' => '5. Live Classes',
            'item' => [
                [
                    'name' => 'Get Live Classes by Chapter',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/chapters/{{chapter_id}}/live-classes', 'host' => ['{{base_url}}'], 'path' => ['chapters', '{{chapter_id}}', 'live-classes']],
                    ],
                ],
                [
                    'name' => 'Attend Live Session (+15 Points Reward)',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/live-classes/{{live_id}}/attend', 'host' => ['{{base_url}}'], 'path' => ['live-classes', '{{live_id}}', 'attend']],
                    ],
                ],
            ],
        ],

        // ==========================================
        // 6. STUDY NOTES & PDFS
        // ==========================================
        [
            'name' => '6. Study Notes & Materials',
            'item' => [
                [
                    'name' => 'Get Notes by Chapter',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/chapters/{{chapter_id}}/notes', 'host' => ['{{base_url}}'], 'path' => ['chapters', '{{chapter_id}}', 'notes']],
                    ],
                ],
                [
                    'name' => 'Get Single Note Document URL',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/notes/{{note_id}}', 'host' => ['{{base_url}}'], 'path' => ['notes', '{{note_id}}']],
                    ],
                ],
            ],
        ],

        // ==========================================
        // 7. TESTS & EXAMINATIONS
        // ==========================================
        [
            'name' => '7. Tests & Examinations',
            'item' => [
                [
                    'name' => 'Get Tests by Chapter',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/chapters/{{chapter_id}}/tests', 'host' => ['{{base_url}}'], 'path' => ['chapters', '{{chapter_id}}', 'tests']],
                    ],
                ],
                [
                    'name' => 'Get Test Questions Paper',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/tests/{{test_id}}', 'host' => ['{{base_url}}'], 'path' => ['tests', '{{test_id}}']],
                    ],
                ],
                [
                    'name' => 'Submit Test Attempt & Get Score Report',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                            ['key' => 'Content-Type', 'value' => 'application/json'],
                        ],
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'time_taken_seconds' => 300,
                                'answers' => [
                                    '1' => 'B',
                                    '2' => 'A',
                                ],
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => ['raw' => '{{base_url}}/tests/{{test_id}}/attempt', 'host' => ['{{base_url}}'], 'path' => ['tests', '{{test_id}}', 'attempt']],
                    ],
                ],
            ],
        ],

        // ==========================================
        // 8. SUBSCRIPTIONS & PAYMENTS
        // ==========================================
        [
            'name' => '8. Subscriptions & Payments',
            'item' => [
                [
                    'name' => 'List Subscription Pricing Plans (Public)',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/subscriptions', 'host' => ['{{base_url}}'], 'path' => ['subscriptions']],
                    ],
                ],
                [
                    'name' => 'Get Current Student Subscription',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/subscriptions/current', 'host' => ['{{base_url}}'], 'path' => ['subscriptions', 'current']],
                    ],
                ],
                [
                    'name' => 'Get Student Subscription Payment Receipts',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/subscriptions/history', 'host' => ['{{base_url}}'], 'path' => ['subscriptions', 'history']],
                    ],
                ],
                [
                    'name' => 'Initiate Subscription Payment Order',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/subscriptions/{{plan_id}}/initiate', 'host' => ['{{base_url}}'], 'path' => ['subscriptions', '{{plan_id}}', 'initiate']],
                    ],
                ],
            ],
        ],

        // ==========================================
        // 9. SCHOOLS, DISTRICTS & APP INFO
        // ==========================================
        [
            'name' => '9. Schools & Districts Directory',
            'item' => [
                [
                    'name' => 'Search West Bengal Schools',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => [
                            'raw' => '{{base_url}}/schools?q=School&district=Kolkata',
                            'host' => ['{{base_url}}'],
                            'path' => ['schools'],
                            'query' => [
                                ['key' => 'q', 'value' => 'School'],
                                ['key' => 'district', 'value' => 'Kolkata'],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'Get All 23 West Bengal Districts',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/schools/districts', 'host' => ['{{base_url}}'], 'path' => ['schools', 'districts']],
                    ],
                ],
                [
                    'name' => 'Get App Share Referral Info',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/app/share-info', 'host' => ['{{base_url}}'], 'path' => ['app', 'share-info']],
                    ],
                ],
                [
                    'name' => 'Get Privacy Policy',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/privacy-policy', 'host' => ['{{base_url}}'], 'path' => ['privacy-policy']],
                    ],
                ],
                [
                    'name' => 'Get Terms and Conditions',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/terms-and-conditions', 'host' => ['{{base_url}}'], 'path' => ['terms-and-conditions']],
                    ],
                ],
                [
                    'name' => 'Get Notifications',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/notifications', 'host' => ['{{base_url}}'], 'path' => ['notifications']],
                    ],
                ],
            ],
        ],

        // ==========================================
        // 10. TEACHER PORTAL
        // ==========================================
        [
            'name' => '10. Teacher Portal',
            'item' => [
                [
                    'name' => 'Teacher Dashboard',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{teacher_token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/teacher/dashboard', 'host' => ['{{base_url}}'], 'path' => ['teacher', 'dashboard']],
                    ],
                ],
                [
                    'name' => 'Teacher Assigned Subjects',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{teacher_token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/teacher/subjects', 'host' => ['{{base_url}}'], 'path' => ['teacher', 'subjects']],
                    ],
                ],
            ],
        ],

        // ==========================================
        // 11. ADMIN PORTAL
        // ==========================================
        [
            'name' => '11. Admin Management Portal',
            'item' => [
                [
                    'name' => 'Students - List Students',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{admin_token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/admin/students?page=1&search=', 'host' => ['{{base_url}}'], 'path' => ['admin', 'students']],
                    ],
                ],
                [
                    'name' => 'Students - Grant Free Subscription Pass (Req 7)',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{admin_token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                            ['key' => 'Content-Type', 'value' => 'application/json'],
                        ],
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'days' => 30,
                                'subscription_id' => 1,
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => ['raw' => '{{base_url}}/admin/students/{{student_id}}/grant-subscription', 'host' => ['{{base_url}}'], 'path' => ['admin', 'students', '{{student_id}}', 'grant-subscription']],
                    ],
                ],
                [
                    'name' => 'Teachers - List Teachers',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{admin_token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/admin/teachers', 'host' => ['{{base_url}}'], 'path' => ['admin', 'teachers']],
                    ],
                ],
                [
                    'name' => 'Teachers - Create Teacher with Photo',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{admin_token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'body' => [
                            'mode' => 'formdata',
                            'formdata' => [
                                ['key' => 'name', 'value' => 'Dr. Subhash Chandra', 'type' => 'text'],
                                ['key' => 'email', 'value' => 'subhash@school.com', 'type' => 'text'],
                                ['key' => 'mobile', 'value' => '9830099999', 'type' => 'text'],
                                ['key' => 'password', 'value' => 'teacher123', 'type' => 'text'],
                                ['key' => 'qualification', 'value' => 'M.Sc Physics, B.Ed', 'type' => 'text'],
                                ['key' => 'bio', 'value' => 'Senior Physics faculty with 12+ years experience', 'type' => 'text'],
                                ['key' => 'avatar', 'type' => 'file', 'src' => ''],
                            ],
                        ],
                        'url' => ['raw' => '{{base_url}}/admin/teachers', 'host' => ['{{base_url}}'], 'path' => ['admin', 'teachers']],
                    ],
                ],
                [
                    'name' => 'Teachers - Assign Class & Subjects',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{admin_token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                            ['key' => 'Content-Type', 'value' => 'application/json'],
                        ],
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'assignments' => [
                                    ['class_level' => 9, 'subject_id' => 1],
                                    ['class_level' => 10, 'subject_id' => 1],
                                ],
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => ['raw' => '{{base_url}}/admin/teachers/{{teacher_id}}/assign-subjects', 'host' => ['{{base_url}}'], 'path' => ['admin', 'teachers', '{{teacher_id}}', 'assign-subjects']],
                    ],
                ],
                [
                    'name' => 'Curriculum - List Subjects',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{admin_token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/admin/subjects', 'host' => ['{{base_url}}'], 'path' => ['admin', 'subjects']],
                    ],
                ],
                [
                    'name' => 'Curriculum - Create Subject (Dual Name)',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{admin_token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                            ['key' => 'Content-Type', 'value' => 'application/json'],
                        ],
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'name' => 'জীবন বিজ্ঞান (Life Science)',
                                'color' => '#10B981',
                                'icon' => 'leaf',
                                'sort_order' => 5,
                            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                        ],
                        'url' => ['raw' => '{{base_url}}/admin/subjects', 'host' => ['{{base_url}}'], 'path' => ['admin', 'subjects']],
                    ],
                ],
                [
                    'name' => 'Curriculum - Class Subject Mappings',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{admin_token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/admin/class-subjects/all', 'host' => ['{{base_url}}'], 'path' => ['admin', 'class-subjects', 'all']],
                    ],
                ],
                [
                    'name' => 'Curriculum - Create Chapter with Theme',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{admin_token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                            ['key' => 'Content-Type', 'value' => 'application/json'],
                        ],
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'subject_id' => 1,
                                'class_level' => 9,
                                'title' => 'Chapter 1 — বল ও গতি',
                                'color' => '#5B45FF',
                                'theme' => 'Purple Modern',
                                'image' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d',
                                'sort_order' => 1,
                            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                        ],
                        'url' => ['raw' => '{{base_url}}/admin/chapters', 'host' => ['{{base_url}}'], 'path' => ['admin', 'chapters']],
                    ],
                ],
                [
                    'name' => 'Content - Add Recorded Video Class',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{admin_token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                            ['key' => 'Content-Type', 'value' => 'application/json'],
                        ],
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'chapter_id' => 1,
                                'teacher_id' => 2,
                                'title' => 'Newton Laws of Motion Detailed Lecture',
                                'gdrive_file_id' => '1a2b3c4d5e6f7g8h9i0j',
                                'duration_seconds' => 1800,
                                'description' => 'Complete chapter 1 video explanation with numerical solutions.',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => ['raw' => '{{base_url}}/admin/recorded-classes', 'host' => ['{{base_url}}'], 'path' => ['admin', 'recorded-classes']],
                    ],
                ],
                [
                    'name' => 'Content - Schedule Live Class',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{admin_token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                            ['key' => 'Content-Type', 'value' => 'application/json'],
                        ],
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'chapter_id' => 1,
                                'teacher_id' => 2,
                                'title' => 'Madhyamik Physics Live Doubt Solving',
                                'platform' => 'google_meet',
                                'meeting_url' => 'https://meet.google.com/abc-defg-hij',
                                'scheduled_at' => '2026-08-25 18:00:00',
                                'duration_minutes' => 60,
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => ['raw' => '{{base_url}}/admin/live-classes', 'host' => ['{{base_url}}'], 'path' => ['admin', 'live-classes']],
                    ],
                ],
                [
                    'name' => 'Content - Add Study Note',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{admin_token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                            ['key' => 'Content-Type', 'value' => 'application/json'],
                        ],
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'chapter_id' => 1,
                                'teacher_id' => 2,
                                'title' => 'Formula Sheet & Important Questions PDF',
                                'file_type' => 'pdf',
                                'file_url' => 'https://drive.google.com/file/d/sample-pdf',
                                'description' => 'Comprehensive revision notes for final exams.',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => ['raw' => '{{base_url}}/admin/notes', 'host' => ['{{base_url}}'], 'path' => ['admin', 'notes']],
                    ],
                ],
                [
                    'name' => 'Tests - Create Assessment Exam',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{admin_token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                            ['key' => 'Content-Type', 'value' => 'application/json'],
                        ],
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'chapter_id' => 1,
                                'title' => 'Chapter 1 Mock Assessment',
                                'duration_minutes' => 30,
                                'pass_marks' => 12,
                                'reward_points' => 20,
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => ['raw' => '{{base_url}}/admin/tests', 'host' => ['{{base_url}}'], 'path' => ['admin', 'tests']],
                    ],
                ],
                [
                    'name' => 'Tests - Bulk Upload Questions',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{admin_token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                            ['key' => 'Content-Type', 'value' => 'application/json'],
                        ],
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'questions' => [
                                    [
                                        'question_text' => 'What is the SI unit of force?',
                                        'option_a' => 'Joule',
                                        'option_b' => 'Newton',
                                        'option_c' => 'Watt',
                                        'option_d' => 'Pascal',
                                        'correct_option' => 'B',
                                        'marks' => 1,
                                        'explanation' => 'Newton (N) is the SI unit of force equal to 1 kg·m/s².',
                                    ],
                                ],
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => ['raw' => '{{base_url}}/admin/tests/{{test_id}}/questions', 'host' => ['{{base_url}}'], 'path' => ['admin', 'tests', '{{test_id}}', 'questions']],
                    ],
                ],
                [
                    'name' => 'Subscriptions - Create Subscription Plan',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{admin_token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                            ['key' => 'Content-Type', 'value' => 'application/json'],
                        ],
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'name' => 'Annual Madhyamik Master Pass',
                                'price' => 999.00,
                                'duration_days' => 365,
                                'description' => 'Unlimited access to all subjects, live doubt classes, and test series for 1 year.',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => ['raw' => '{{base_url}}/admin/subscription-plans', 'host' => ['{{base_url}}'], 'path' => ['admin', 'subscription-plans']],
                    ],
                ],
                [
                    'name' => 'Subscriptions - Subscriber Payment Logs',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{admin_token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/admin/user-subscriptions', 'host' => ['{{base_url}}'], 'path' => ['admin', 'user-subscriptions']],
                    ],
                ],
                [
                    'name' => 'Analytics - Overview Metrics',
                    'request' => [
                        'method' => 'GET',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{admin_token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => ['raw' => '{{base_url}}/admin/analytics/overview', 'host' => ['{{base_url}}'], 'path' => ['admin', 'analytics', 'overview']],
                    ],
                ],
                [
                    'name' => 'Notifications - Broadcast to Students',
                    'request' => [
                        'method' => 'POST',
                        'header' => [
                            ['key' => 'Authorization', 'value' => 'Bearer {{admin_token}}'],
                            ['key' => 'Accept', 'value' => 'application/json'],
                            ['key' => 'Content-Type', 'value' => 'application/json'],
                        ],
                        'body' => [
                            'mode' => 'raw',
                            'raw' => json_encode([
                                'title' => 'Live Revision Class Today at 7 PM!',
                                'message' => 'Join our special Madhyamik problem solving live session.',
                                'target_type' => 'all',
                            ], JSON_PRETTY_PRINT),
                        ],
                        'url' => ['raw' => '{{base_url}}/admin/notifications/broadcast', 'host' => ['{{base_url}}'], 'path' => ['admin', 'notifications', 'broadcast']],
                    ],
                ],
            ],
        ],
    ],
];

file_put_contents(
    __DIR__.'/../VidyaMission.postman_collection.json',
    json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
);

echo "VidyaMission.postman_collection.json generated successfully!\n";
echo 'Total top-level groups: '.count($collection['item'])."\n";
