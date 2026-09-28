<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DocsController extends Controller
{
    /**
     * Display the Swagger UI documentation page.
     * GET /docs
     */
    public function index(): View
    {
        $appUrl = config('app.url', url('/'));

        return view('docs.swagger', [
            'appUrl' => $appUrl,
            'apiSpecUrl' => url('/docs/openapi.json'),
        ]);
    }

    /**
     * Return the complete OpenAPI 3.0 specification in JSON format.
     * GET /docs/openapi.json
     */
    public function openApiJson(): JsonResponse
    {
        $baseUrl = url('/api');

        $spec = [
            'openapi' => '3.0.3',
            'info' => [
                'title' => 'Vidya Mission REST API',
                'description' => 'Comprehensive API documentation for the **Vidya Mission** mobile application and web portal. Built with Laravel 11 and secured via Laravel Sanctum (Bearer Token). Supports trial periods, single-device session enforcement, subscriptions, interactive test examinations, and dynamic curriculum management.',
                'version' => '1.0.0',
                'contact' => [
                    'name' => 'Vidya Mission Support Team',
                    'email' => 'support@vidyamission.in',
                ],
            ],
            'servers' => [
                [
                    'url' => $baseUrl,
                    'description' => 'Current Environment API Server',
                ],
            ],
            'components' => [
                'securitySchemes' => [
                    'bearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'Sanctum Token',
                        'description' => 'Enter your Sanctum API token obtained from `/auth/login` or `/auth/verify-otp`.',
                    ],
                ],
            ],
            'tags' => [
                ['name' => 'Authentication', 'description' => 'Student registration, OTP login, device token handling & logout.'],
                ['name' => 'Student Profile & Dashboard', 'description' => 'Student details, avatar photo, learning progress, exam history & account.'],
                ['name' => 'Curriculum & Subjects', 'description' => 'Classes, Bengali/English dual subjects & curriculum mapping.'],
                ['name' => 'Chapters', 'description' => 'Chapter details, styling themes & completion tracking.'],
                ['name' => 'Recorded Video Classes', 'description' => 'Drive / YouTube streaming videos & completion reward points.'],
                ['name' => 'Live Classes', 'description' => 'Live sessions (Google Meet/Zoom) & attendance reward points.'],
                ['name' => 'Study Notes & Materials', 'description' => 'PDFs, images, documents & chapter notes.'],
                ['name' => 'Tests & Examinations', 'description' => 'Practice tests, MCQs, timed exams & instant evaluation reports.'],
                ['name' => 'Subscriptions & Payments', 'description' => 'Subscription packages, payment initiation, Razorpay webhook & history.'],
                ['name' => 'Schools & Districts', 'description' => 'West Bengal Board schools catalog & 23 districts.'],
                ['name' => 'Notifications', 'description' => 'Student in-app announcements & unread tracking.'],
                ['name' => 'App Utilities & Legal', 'description' => 'Share referral metadata, privacy policy & terms.'],
                ['name' => 'Teacher Portal', 'description' => 'Teacher profile, assigned subjects & classes.'],
                ['name' => 'Admin - Students', 'description' => 'Manage registered students & grant free subscriptions.'],
                ['name' => 'Admin - Teachers', 'description' => 'Teacher CRUD, photo uploads & subject assignments.'],
                ['name' => 'Admin - Curriculum', 'description' => 'Subject CRUD, class mapping & chapters with themes.'],
                ['name' => 'Admin - Content', 'description' => 'Manage recorded videos, live schedules & notes.'],
                ['name' => 'Admin - Tests & Question Bank', 'description' => 'Create exams, bulk upload questions & edit questions.'],
                ['name' => 'Admin - Subscriptions', 'description' => 'Subscription plan management & student subscriber records.'],
                ['name' => 'Admin - Broadcast', 'description' => 'Broadcast notifications to all students or specific classes.'],
            ],
            'paths' => $this->getApiEndpoints(),
        ];

        return response()->json($spec, 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function getApiEndpoints(): array
    {
        return [
            // ==========================================
            // 1. AUTHENTICATION
            // ==========================================
            '/auth/register' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'Register a new student account',
                    'description' => 'Creates a student account, starts 3-day free trial, and issues a single-device authentication token.',
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['name', 'mobile', 'password', 'class'],
                                    'properties' => [
                                        'name' => ['type' => 'string', 'example' => 'Sourav Ganguly'],
                                        'email' => ['type' => 'string', 'format' => 'email', 'example' => 'sourav@vidyamission.in'],
                                        'mobile' => ['type' => 'string', 'example' => '9876543210'],
                                        'password' => ['type' => 'string', 'minLength' => 6, 'example' => 'secret123'],
                                        'class' => ['type' => 'string', 'example' => '9'],
                                        'school_name' => ['type' => 'string', 'example' => 'Hare School'],
                                        'district' => ['type' => 'string', 'example' => 'Kolkata'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Student registered successfully with token.'],
                        '422' => ['description' => 'Validation error (e.g. mobile taken, password < 6 chars).'],
                    ],
                ],
            ],
            '/auth/login' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'Student password login (Enforces single-device login)',
                    'description' => 'Authenticates student via mobile/email and password. **Revokes prior device tokens** so the previous phone logs out automatically.',
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['login', 'password'],
                                    'properties' => [
                                        'login' => ['type' => 'string', 'description' => 'Mobile number or email address', 'example' => '9876543210'],
                                        'password' => ['type' => 'string', 'example' => 'secret123'],
                                        'device_name' => ['type' => 'string', 'example' => 'Samsung Galaxy A54'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Login successful, returns Bearer token and user profile.'],
                        '401' => ['description' => 'Invalid credentials.'],
                    ],
                ],
            ],
            '/auth/send-otp' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'Send OTP for passwordless login / verification',
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['mobile'],
                                    'properties' => [
                                        'mobile' => ['type' => 'string', 'example' => '9876543210'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'OTP dispatched successfully.'],
                    ],
                ],
            ],
            '/auth/verify-otp' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'Verify OTP and authenticate student',
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['mobile', 'otp'],
                                    'properties' => [
                                        'mobile' => ['type' => 'string', 'example' => '9876543210'],
                                        'otp' => ['type' => 'string', 'example' => '123456'],
                                        'device_name' => ['type' => 'string', 'example' => 'Android App'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'OTP verified, returns auth token.'],
                        '422' => ['description' => 'Invalid or expired OTP.'],
                    ],
                ],
            ],
            '/auth/forgot-password' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'Reset student password',
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['mobile', 'password'],
                                    'properties' => [
                                        'mobile' => ['type' => 'string', 'example' => '9876543210'],
                                        'password' => ['type' => 'string', 'minLength' => 6, 'example' => 'newSecret123'],
                                        'password_confirmation' => ['type' => 'string', 'example' => 'newSecret123'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Password reset successfully.'],
                    ],
                ],
            ],
            '/auth/logout' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'Logout current device session',
                    'security' => [['bearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Logged out successfully.'],
                    ],
                ],
            ],

            // ==========================================
            // 2. STUDENT PROFILE & DASHBOARD
            // ==========================================
            '/profile' => [
                'get' => [
                    'tags' => ['Student Profile & Dashboard'],
                    'summary' => 'Get current authenticated student profile',
                    'security' => [['bearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Returns student profile, trial status, and subscription state.'],
                    ],
                ],
                'delete' => [
                    'tags' => ['Student Profile & Dashboard'],
                    'summary' => 'Delete student account and purge personal data',
                    'security' => [['bearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Account and records purged.'],
                    ],
                ],
            ],
            '/profile/update' => [
                'put' => [
                    'tags' => ['Student Profile & Dashboard'],
                    'summary' => 'Update student profile information',
                    'security' => [['bearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'name' => ['type' => 'string', 'example' => 'Sourav Ganguly'],
                                        'school_name' => ['type' => 'string', 'example' => 'Hare School'],
                                        'district' => ['type' => 'string', 'example' => 'Kolkata'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Profile updated successfully.'],
                    ],
                ],
            ],
            '/profile/avatar' => [
                'post' => [
                    'tags' => ['Student Profile & Dashboard'],
                    'summary' => 'Upload student profile photo / avatar',
                    'security' => [['bearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'multipart/form-data' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['avatar'],
                                    'properties' => [
                                        'avatar' => ['type' => 'string', 'format' => 'binary', 'description' => 'Image file (jpg, png, webp, max 5MB)'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Avatar uploaded, returns avatar_url.'],
                    ],
                ],
            ],
            '/student/dashboard' => [
                'get' => [
                    'tags' => ['Student Profile & Dashboard'],
                    'summary' => 'Get comprehensive student dashboard metrics',
                    'description' => 'Returns enrolled class, reward points, videos completed, tests passed, active subscription, and total subscription amount paid.',
                    'security' => [['bearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Student dashboard summary.'],
                    ],
                ],
            ],
            '/student/exam-reports' => [
                'get' => [
                    'tags' => ['Student Profile & Dashboard'],
                    'summary' => 'Get student exam attempt history and performance reports',
                    'security' => [['bearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'List of past test attempts with scores, percentages, and passed status.'],
                    ],
                ],
            ],
            '/student/history' => [
                'get' => [
                    'tags' => ['Student Profile & Dashboard'],
                    'summary' => 'Get student recent learning activity history',
                    'description' => 'Returns recently completed video classes, attended live classes, and recent tests.',
                    'security' => [['bearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Learning history timeline.'],
                    ],
                ],
            ],

            // ==========================================
            // 3. CURRICULUM & SUBJECTS
            // ==========================================
            '/subjects' => [
                'get' => [
                    'tags' => ['Curriculum & Subjects'],
                    'summary' => 'Get available subjects for student',
                    'description' => 'Returns subjects in dual language (e.g. বাংলা (Bengali), ইতিহাস (History)). When subscribed or on trial, students can access all content with optional class filtering via `?class_level=X` (or `?class_level=all` for all subjects).',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        [
                            'name' => 'class_level',
                            'in' => 'query',
                            'description' => 'Class level (1–12 or "all") to filter subjects (defaults to student class)',
                            'required' => false,
                            'schema' => ['type' => 'string', 'example' => '9'],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'List of mapped subjects.'],
                        '403' => ['description' => 'Trial expired or subscription required.'],
                    ],
                ],
            ],
            '/subjects/{id}/chapters' => [
                'get' => [
                    'tags' => ['Curriculum & Subjects'],
                    'summary' => 'Get all chapters in a subject',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]],
                        ['name' => 'class_level', 'in' => 'query', 'required' => false, 'schema' => ['type' => 'integer']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'List of chapters with image, color theme, and content counts.'],
                    ],
                ],
            ],

            // ==========================================
            // 4. CHAPTERS
            // ==========================================
            '/chapters/{id}' => [
                'get' => [
                    'tags' => ['Chapters'],
                    'summary' => 'Get single chapter details with full curriculum counts',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Chapter details with subject info and progress.'],
                    ],
                ],
            ],
            '/chapters/{id}/complete' => [
                'post' => [
                    'tags' => ['Chapters'],
                    'summary' => 'Mark chapter as completed and award reward badge',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Chapter marked completed.'],
                    ],
                ],
            ],

            // ==========================================
            // 5. RECORDED VIDEO CLASSES
            // ==========================================
            '/chapters/{id}/recorded-classes' => [
                'get' => [
                    'tags' => ['Recorded Video Classes'],
                    'summary' => 'Get all recorded video classes in a chapter',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'List of video classes.'],
                    ],
                ],
            ],
            '/recorded-classes/{id}' => [
                'get' => [
                    'tags' => ['Recorded Video Classes'],
                    'summary' => 'Get single video class details & streaming embed URL',
                    'description' => 'Provides responsive embed URL with secure playback token. Raw Drive IDs are hidden.',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Video player metadata & embed URL.'],
                    ],
                ],
            ],
            '/recorded-classes/{id}/complete' => [
                'post' => [
                    'tags' => ['Recorded Video Classes'],
                    'summary' => 'Record 100% video completion & claim +10 Reward Points',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Reward awarded (+10 points).'],
                    ],
                ],
            ],

            // ==========================================
            // 6. LIVE CLASSES
            // ==========================================
            '/chapters/{id}/live-classes' => [
                'get' => [
                    'tags' => ['Live Classes'],
                    'summary' => 'Get upcoming and past live classes for a chapter',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Upcoming and past live classes.'],
                    ],
                ],
            ],
            '/live-classes/{id}/attend' => [
                'post' => [
                    'tags' => ['Live Classes'],
                    'summary' => 'Record live session attendance & claim +15 Reward Points',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Attendance recorded and +15 points awarded.'],
                    ],
                ],
            ],

            // ==========================================
            // 7. STUDY NOTES & MATERIALS
            // ==========================================
            '/chapters/{id}/notes' => [
                'get' => [
                    'tags' => ['Study Notes & Materials'],
                    'summary' => 'Get all study notes and PDFs in a chapter',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'List of chapter study notes.'],
                    ],
                ],
            ],
            '/notes/{id}' => [
                'get' => [
                    'tags' => ['Study Notes & Materials'],
                    'summary' => 'Get note document details and download/view URL',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Note metadata and file URL.'],
                    ],
                ],
            ],

            // ==========================================
            // 8. TESTS & EXAMINATIONS
            // ==========================================
            '/chapters/{id}/tests' => [
                'get' => [
                    'tags' => ['Tests & Examinations'],
                    'summary' => 'Get practice tests available in a chapter',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'List of tests.'],
                    ],
                ],
            ],
            '/tests/{id}' => [
                'get' => [
                    'tags' => ['Tests & Examinations'],
                    'summary' => 'Get test questions and time limit',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Test details with questions and options (answers hidden).'],
                    ],
                ],
            ],
            '/tests/{id}/attempt' => [
                'post' => [
                    'tags' => ['Tests & Examinations'],
                    'summary' => 'Submit exam answers and receive instant score report',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['answers'],
                                    'properties' => [
                                        'time_taken_seconds' => ['type' => 'integer', 'example' => 450],
                                        'answers' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'object',
                                                'required' => ['question_id', 'selected_option'],
                                                'properties' => [
                                                    'question_id' => ['type' => 'integer', 'example' => 1],
                                                    'selected_option' => ['type' => 'string', 'example' => 'A'],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Exam report with score, pass/fail state, and explanation.'],
                    ],
                ],
            ],

            // ==========================================
            // 9. SUBSCRIPTIONS & PAYMENTS
            // ==========================================
            '/subscriptions' => [
                'get' => [
                    'tags' => ['Subscriptions & Payments'],
                    'summary' => 'Get all active subscription pricing plans',
                    'responses' => [
                        '200' => ['description' => 'List of available subscription plans with pricing and duration.'],
                    ],
                ],
            ],
            '/subscriptions/current' => [
                'get' => [
                    'tags' => ['Subscriptions & Payments'],
                    'summary' => 'Get current active student subscription status',
                    'security' => [['bearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Active plan, validity dates, or trial status.'],
                    ],
                ],
            ],
            '/subscriptions/history' => [
                'get' => [
                    'tags' => ['Subscriptions & Payments'],
                    'summary' => 'Get student subscription payment history and receipts',
                    'security' => [['bearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Full transaction history with plan names, amounts paid, dates, and gateway references.'],
                    ],
                ],
            ],
            '/subscriptions/{id}/initiate' => [
                'post' => [
                    'tags' => ['Subscriptions & Payments'],
                    'summary' => 'Initiate payment for a subscription plan',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Returns Razorpay / Payment Gateway order ID.'],
                    ],
                ],
            ],
            '/subscriptions/webhook' => [
                'post' => [
                    'tags' => ['Subscriptions & Payments'],
                    'summary' => 'Razorpay / Payment gateway server webhook',
                    'responses' => [
                        '200' => ['description' => 'Webhook processed and subscription activated.'],
                    ],
                ],
            ],

            // ==========================================
            // 10. SCHOOLS & DISTRICTS
            // ==========================================
            '/schools' => [
                'get' => [
                    'tags' => ['Schools & Districts'],
                    'summary' => 'Search West Bengal Board schools catalog',
                    'parameters' => [
                        ['name' => 'q', 'in' => 'query', 'description' => 'Search school name or code', 'schema' => ['type' => 'string']],
                        ['name' => 'district', 'in' => 'query', 'description' => 'Filter by district (e.g. Kolkata, Howrah)', 'schema' => ['type' => 'string']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'List of matching schools and all 23 districts.'],
                    ],
                ],
            ],
            '/schools/districts' => [
                'get' => [
                    'tags' => ['Schools & Districts'],
                    'summary' => 'Get all 23 West Bengal districts list for dropdowns',
                    'responses' => [
                        '200' => ['description' => 'List of district names.'],
                    ],
                ],
            ],

            // ==========================================
            // 11. NOTIFICATIONS
            // ==========================================
            '/notifications' => [
                'get' => [
                    'tags' => ['Notifications'],
                    'summary' => 'Get student notification inbox',
                    'security' => [['bearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'List of notifications and unread count.'],
                    ],
                ],
            ],
            '/notifications/{id}/read' => [
                'post' => [
                    'tags' => ['Notifications'],
                    'summary' => 'Mark a notification as read',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Notification marked as read.'],
                    ],
                ],
            ],

            // ==========================================
            // 12. APP UTILITIES & LEGAL
            // ==========================================
            '/app/share-info' => [
                'get' => [
                    'tags' => ['App Utilities & Legal'],
                    'summary' => 'Get in-app share referral metadata and download link',
                    'responses' => [
                        '200' => ['description' => 'App download URL, share message & title.'],
                    ],
                ],
            ],
            '/privacy-policy' => [
                'get' => [
                    'tags' => ['App Utilities & Legal'],
                    'summary' => 'Get structured Privacy Policy text',
                    'responses' => [
                        '200' => ['description' => 'Privacy policy sections and contact details.'],
                    ],
                ],
            ],
            '/terms-and-conditions' => [
                'get' => [
                    'tags' => ['App Utilities & Legal'],
                    'summary' => 'Get structured Terms and Conditions text',
                    'responses' => [
                        '200' => ['description' => 'Terms and conditions sections.'],
                    ],
                ],
            ],

            // ==========================================
            // 13. TEACHER PORTAL
            // ==========================================
            '/teacher/dashboard' => [
                'get' => [
                    'tags' => ['Teacher Portal'],
                    'summary' => 'Get teacher dashboard metrics',
                    'security' => [['bearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Teacher metrics (classes conducted, notes shared).'],
                    ],
                ],
            ],
            '/teacher/subjects' => [
                'get' => [
                    'tags' => ['Teacher Portal'],
                    'summary' => 'Get subjects and classes assigned to teacher',
                    'security' => [['bearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Assigned subjects list.'],
                    ],
                ],
            ],

            // ==========================================
            // 14. ADMIN - STUDENTS & FREE ACCESS
            // ==========================================
            '/admin/students' => [
                'get' => [
                    'tags' => ['Admin - Students'],
                    'summary' => 'Get paginated list of students',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'page', 'in' => 'query', 'schema' => ['type' => 'integer', 'default' => 1]],
                        ['name' => 'search', 'in' => 'query', 'schema' => ['type' => 'string']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Paginated students list with profiles and subscription amounts.'],
                    ],
                ],
            ],
            '/admin/students/{id}/grant-subscription' => [
                'post' => [
                    'tags' => ['Admin - Students'],
                    'summary' => 'Grant free full access subscription pass to a student',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['days'],
                                    'properties' => [
                                        'days' => ['type' => 'integer', 'example' => 30, 'description' => 'Number of days access is granted for'],
                                        'subscription_id' => ['type' => 'integer', 'nullable' => true, 'example' => 1],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Free subscription pass granted successfully.'],
                    ],
                ],
            ],

            // ==========================================
            // 15. ADMIN - TEACHERS
            // ==========================================
            '/admin/teachers' => [
                'get' => [
                    'tags' => ['Admin - Teachers'],
                    'summary' => 'Get all teachers with profile avatars and assigned subjects',
                    'security' => [['bearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'List of teachers.'],
                    ],
                ],
                'post' => [
                    'tags' => ['Admin - Teachers'],
                    'summary' => 'Create new teacher account with optional photo',
                    'security' => [['bearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'multipart/form-data' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['name', 'email', 'password'],
                                    'properties' => [
                                        'name' => ['type' => 'string', 'example' => 'Dr. Subhash Chandra'],
                                        'email' => ['type' => 'string', 'format' => 'email', 'example' => 'subhash@school.com'],
                                        'mobile' => ['type' => 'string', 'example' => '9830012345'],
                                        'password' => ['type' => 'string', 'minLength' => 6, 'example' => 'teacher123'],
                                        'qualification' => ['type' => 'string', 'example' => 'M.Sc Physics, B.Ed'],
                                        'bio' => ['type' => 'string', 'example' => '10 years experience in WB Board Physics'],
                                        'avatar' => ['type' => 'string', 'format' => 'binary', 'description' => 'Teacher photo'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Teacher created successfully.'],
                    ],
                ],
            ],
            '/admin/teachers/{id}/assign-subjects' => [
                'post' => [
                    'tags' => ['Admin - Teachers'],
                    'summary' => 'Assign classes and subjects to a teacher',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['assignments'],
                                    'properties' => [
                                        'assignments' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'object',
                                                'required' => ['class_level', 'subject_id'],
                                                'properties' => [
                                                    'class_level' => ['type' => 'integer', 'example' => 9],
                                                    'subject_id' => ['type' => 'integer', 'example' => 1],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Subjects assigned to teacher successfully.'],
                    ],
                ],
            ],

            // ==========================================
            // 16. ADMIN - CURRICULUM
            // ==========================================
            '/admin/subjects' => [
                'get' => [
                    'tags' => ['Admin - Curriculum'],
                    'summary' => 'List all global subjects',
                    'security' => [['bearerAuth' => []]],
                    'responses' => ['200' => ['description' => 'List of subjects.']],
                ],
                'post' => [
                    'tags' => ['Admin - Curriculum'],
                    'summary' => 'Create a subject with dual Bengali + English name',
                    'security' => [['bearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['name', 'color'],
                                    'properties' => [
                                        'name' => ['type' => 'string', 'example' => 'ভূগোল (Geography)'],
                                        'color' => ['type' => 'string', 'example' => '#F59E0B'],
                                        'icon' => ['type' => 'string', 'example' => 'globe'],
                                        'sort_order' => ['type' => 'integer', 'example' => 1],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => ['201' => ['description' => 'Subject created.']],
                ],
            ],
            '/admin/class-subjects' => [
                'get' => [
                    'tags' => ['Admin - Curriculum'],
                    'summary' => 'Get subjects assigned to a class level',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'class_level', 'in' => 'query', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 9]],
                    ],
                    'responses' => ['200' => ['description' => 'Class subjects.']],
                ],
                'post' => [
                    'tags' => ['Admin - Curriculum'],
                    'summary' => 'Assign subject to class level',
                    'security' => [['bearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['class_level', 'subject_id'],
                                    'properties' => [
                                        'class_level' => ['type' => 'integer', 'example' => 9],
                                        'subject_id' => ['type' => 'integer', 'example' => 2],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => ['201' => ['description' => 'Subject mapped to class.']],
                ],
            ],
            '/admin/chapters' => [
                'post' => [
                    'tags' => ['Admin - Curriculum'],
                    'summary' => 'Create chapter with image, color theme, and class level',
                    'security' => [['bearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['subject_id', 'title'],
                                    'properties' => [
                                        'subject_id' => ['type' => 'integer', 'example' => 1],
                                        'class_level' => ['type' => 'integer', 'nullable' => true, 'example' => 9],
                                        'title' => ['type' => 'string', 'example' => 'বল ও গতি (Force and Motion)'],
                                        'image' => ['type' => 'string', 'example' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d'],
                                        'color' => ['type' => 'string', 'example' => '#5B45FF'],
                                        'theme' => ['type' => 'string', 'example' => 'Royal Purple'],
                                        'sort_order' => ['type' => 'integer', 'example' => 1],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => ['201' => ['description' => 'Chapter created.']],
                ],
            ],

            // ==========================================
            // 17. ADMIN - CONTENT (VIDEOS, LIVE, NOTES)
            // ==========================================
            '/admin/recorded-classes' => [
                'get' => [
                    'tags' => ['Admin - Content'],
                    'summary' => 'List all recorded video classes',
                    'security' => [['bearerAuth' => []]],
                    'responses' => ['200' => ['description' => 'List of recorded classes.']],
                ],
                'post' => [
                    'tags' => ['Admin - Content'],
                    'summary' => 'Create recorded video class (Drive ID / YouTube link)',
                    'security' => [['bearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['chapter_id', 'teacher_id', 'title', 'gdrive_file_id'],
                                    'properties' => [
                                        'chapter_id' => ['type' => 'integer', 'example' => 1],
                                        'teacher_id' => ['type' => 'integer', 'example' => 2],
                                        'title' => ['type' => 'string', 'example' => 'Introduction to Newton Laws'],
                                        'gdrive_file_id' => ['type' => 'string', 'example' => '1a2b3c4d5e6f7g8h9i0j', 'description' => 'Google Drive File ID or YouTube URL'],
                                        'duration_seconds' => ['type' => 'integer', 'example' => 1800],
                                        'thumbnail_url' => ['type' => 'string', 'example' => 'https://img.youtube.com/...'],
                                        'description' => ['type' => 'string', 'example' => 'Full lecture on 1st & 2nd law.'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => ['201' => ['description' => 'Recorded class created.']],
                ],
            ],
            '/admin/live-classes' => [
                'get' => [
                    'tags' => ['Admin - Content'],
                    'summary' => 'List all live classes',
                    'security' => [['bearerAuth' => []]],
                    'responses' => ['200' => ['description' => 'List of live classes.']],
                ],
                'post' => [
                    'tags' => ['Admin - Content'],
                    'summary' => 'Schedule a new live class',
                    'security' => [['bearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['chapter_id', 'teacher_id', 'title', 'platform', 'meeting_url', 'scheduled_at', 'duration_minutes'],
                                    'properties' => [
                                        'chapter_id' => ['type' => 'integer', 'example' => 1],
                                        'teacher_id' => ['type' => 'integer', 'example' => 2],
                                        'title' => ['type' => 'string', 'example' => 'Live Doubt Clearing Session'],
                                        'platform' => ['type' => 'string', 'enum' => ['google_meet', 'zoom', 'teams', 'other'], 'example' => 'google_meet'],
                                        'meeting_url' => ['type' => 'string', 'example' => 'https://meet.google.com/abc-defg-hij'],
                                        'scheduled_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2026-08-20 18:00:00'],
                                        'duration_minutes' => ['type' => 'integer', 'example' => 60],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => ['201' => ['description' => 'Live class scheduled.']],
                ],
            ],
            '/admin/notes' => [
                'get' => [
                    'tags' => ['Admin - Content'],
                    'summary' => 'List all study notes',
                    'security' => [['bearerAuth' => []]],
                    'responses' => ['200' => ['description' => 'List of notes.']],
                ],
                'post' => [
                    'tags' => ['Admin - Content'],
                    'summary' => 'Upload / Add study note material',
                    'security' => [['bearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['chapter_id', 'teacher_id', 'title', 'file_type', 'file_url'],
                                    'properties' => [
                                        'chapter_id' => ['type' => 'integer', 'example' => 1],
                                        'teacher_id' => ['type' => 'integer', 'example' => 2],
                                        'title' => ['type' => 'string', 'example' => 'Complete Formula Handbook'],
                                        'file_type' => ['type' => 'string', 'enum' => ['pdf', 'image', 'doc'], 'example' => 'pdf'],
                                        'file_url' => ['type' => 'string', 'example' => 'https://drive.google.com/file/d/...'],
                                        'description' => ['type' => 'string', 'example' => 'Formulas and solved examples.'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => ['201' => ['description' => 'Note added.']],
                ],
            ],

            // ==========================================
            // 18. ADMIN - TESTS & QUESTION BANK
            // ==========================================
            '/admin/tests' => [
                'get' => [
                    'tags' => ['Admin - Tests & Question Bank'],
                    'summary' => 'List all tests',
                    'security' => [['bearerAuth' => []]],
                    'responses' => ['200' => ['description' => 'List of tests.']],
                ],
                'post' => [
                    'tags' => ['Admin - Tests & Question Bank'],
                    'summary' => 'Create a test / exam',
                    'security' => [['bearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['chapter_id', 'title', 'duration_minutes', 'pass_marks'],
                                    'properties' => [
                                        'chapter_id' => ['type' => 'integer', 'example' => 1],
                                        'title' => ['type' => 'string', 'example' => 'Chapter 1 Mock Assessment'],
                                        'duration_minutes' => ['type' => 'integer', 'example' => 30],
                                        'pass_marks' => ['type' => 'integer', 'example' => 12],
                                        'reward_points' => ['type' => 'integer', 'example' => 20],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => ['201' => ['description' => 'Test created.']],
                ],
            ],
            '/admin/tests/{id}/questions' => [
                'post' => [
                    'tags' => ['Admin - Tests & Question Bank'],
                    'summary' => 'Bulk add questions with options and explanations to a test',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['questions'],
                                    'properties' => [
                                        'questions' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'object',
                                                'required' => ['question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_option', 'marks'],
                                                'properties' => [
                                                    'question_text' => ['type' => 'string', 'example' => 'What is the SI unit of force?'],
                                                    'option_a' => ['type' => 'string', 'example' => 'Joule'],
                                                    'option_b' => ['type' => 'string', 'example' => 'Newton'],
                                                    'option_c' => ['type' => 'string', 'example' => 'Watt'],
                                                    'option_d' => ['type' => 'string', 'example' => 'Pascal'],
                                                    'correct_option' => ['type' => 'string', 'enum' => ['A', 'B', 'C', 'D'], 'example' => 'B'],
                                                    'marks' => ['type' => 'integer', 'example' => 1],
                                                    'explanation' => ['type' => 'string', 'example' => 'Newton (N) is the SI unit of force equal to kg·m/s².'],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => ['201' => ['description' => 'Questions added to test.']],
                ],
            ],

            // ==========================================
            // 19. ADMIN - SUBSCRIPTIONS
            // ==========================================
            '/admin/subscription-plans' => [
                'get' => [
                    'tags' => ['Admin - Subscriptions'],
                    'summary' => 'List all subscription pricing packages',
                    'security' => [['bearerAuth' => []]],
                    'responses' => ['200' => ['description' => 'List of plans.']],
                ],
                'post' => [
                    'tags' => ['Admin - Subscriptions'],
                    'summary' => 'Create a new subscription plan',
                    'security' => [['bearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['name', 'price', 'duration_days'],
                                    'properties' => [
                                        'name' => ['type' => 'string', 'example' => 'Annual Full Access Pass'],
                                        'price' => ['type' => 'number', 'example' => 999.00],
                                        'duration_days' => ['type' => 'integer', 'example' => 365],
                                        'description' => ['type' => 'string', 'example' => 'Access to all subjects, live classes & test series for 1 year.'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => ['201' => ['description' => 'Subscription plan created.']],
                ],
            ],
            '/admin/user-subscriptions' => [
                'get' => [
                    'tags' => ['Admin - Subscriptions'],
                    'summary' => 'Get all student subscriber transaction logs and active subscriptions',
                    'security' => [['bearerAuth' => []]],
                    'responses' => ['200' => ['description' => 'Subscriber logs.']],
                ],
            ],

            // ==========================================
            // 20. ADMIN - BROADCAST
            // ==========================================
            '/admin/notifications/broadcast' => [
                'post' => [
                    'tags' => ['Admin - Broadcast'],
                    'summary' => 'Broadcast push notification to all students or a specific class',
                    'security' => [['bearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['title', 'message'],
                                    'properties' => [
                                        'title' => ['type' => 'string', 'example' => 'Special Live Class Today at 7 PM!'],
                                        'message' => ['type' => 'string', 'example' => 'Important Madhyamik Physics problem-solving live session.'],
                                        'target_type' => ['type' => 'string', 'enum' => ['all', 'class', 'user'], 'example' => 'class'],
                                        'target_class' => ['type' => 'integer', 'example' => 10],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => ['200' => ['description' => 'Notification broadcasted.']],
                ],
            ],
        ];
    }
}
