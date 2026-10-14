<?php

namespace App\Services;

class OpenApiSpecService
{
    /**
     * Build the complete OpenAPI 3.0.3 specification for Vidya Mission.
     */
    public function build(): array
    {
        return [
            'openapi' => '3.0.3',
            'info' => [
                'title' => 'Vidya Mission Educational Platform API',
                'version' => '1.0.0',
                'description' => "Complete REST API documentation for the Vidya Mission e-learning platform.\n\n"
                    ."Supports educational content management for **Classes 1 through 10** aligned with the West Bengal Board of Secondary Education (WBBSE) curriculum, including Bengali, English, Mathematics, Environmental Studies/Science, History, Geography, Physical Science, and Life Science.\n\n"
                    ."### Authentication\n"
                    ."Protected routes require a Bearer token via Laravel Sanctum. Obtain a token from `/api/auth/login` or `/api/auth/verify-otp` and supply it in the `Authorization` header:\n"
                    ."```\nAuthorization: Bearer <sanctum_token>\n```",
                'contact' => [
                    'name' => 'Vidya Mission API Support',
                    'email' => 'support@vidyamission.com',
                ],
                'license' => [
                    'name' => 'Proprietary',
                ],
            ],
            'servers' => [
                [
                    'url' => '/',
                    'description' => 'Current Environment Server',
                ],
            ],
            'tags' => [
                ['name' => 'Academic Classes & Curriculum', 'description' => 'Class 1–10 levels, subjects, and curriculum hierarchy discovery'],
                ['name' => 'Authentication', 'description' => 'Student and staff authentication, registration, OTP login, and password management'],
                ['name' => 'Student - Academic Content', 'description' => 'Class 1–10 subjects, chapters, recorded classes, live classes, and notes'],
                ['name' => 'Student - Assessments & Tests', 'description' => 'Practice chapter tests, MCQ questions, submissions, and instant scoring'],
                ['name' => 'Student - Profile & Subscriptions', 'description' => 'Student profile setup, notifications, subscription plans, and Razorpay payment orders'],
                ['name' => 'Teacher - Portal', 'description' => 'Teacher dashboard metrics, assigned subjects, and teaching schedules'],
                ['name' => 'Admin - Curriculum Management', 'description' => 'Class 1–10 subject mapping, subject master, and chapter management'],
                ['name' => 'Admin - Live Classes Management', 'description' => 'Comprehensive management of live classes, scheduling, meeting link validation, rescheduling, and status lifecycles'],
                ['name' => 'Admin - Educational Content', 'description' => 'Management of recorded videos and study notes with class/subject filters'],
                ['name' => 'Admin - Tests & Assessments', 'description' => 'Management of chapter tests and bulk MCQ question banks'],
                ['name' => 'Admin - Users & Analytics', 'description' => 'Student/teacher management, subscription plans, and learning analytics'],
                ['name' => 'Admin - Payments & Webhooks', 'description' => 'Payment transaction reconciliation, refunds, audit logs, and webhooks'],
            ],
            'components' => [
                'securitySchemes' => [
                    'BearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'Sanctum Token',
                        'description' => 'Enter your Sanctum Bearer token received after login.',
                    ],
                ],
                'schemas' => $this->getSchemas(),
                'responses' => [
                    '400BadRequest' => [
                        'description' => 'Bad Request',
                        'content' => [
                            'application/json' => [
                                'schema' => ['$ref' => '#/components/schemas/StandardErrorResponse'],
                            ],
                        ],
                    ],
                    '401Unauthorized' => [
                        'description' => 'Unauthenticated / Invalid Bearer Token',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => ['type' => 'string', 'example' => 'Unauthenticated.'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    '403Forbidden' => [
                        'description' => 'Forbidden / Insufficient Permissions or Trial Expired',
                        'content' => [
                            'application/json' => [
                                'schema' => ['$ref' => '#/components/schemas/StandardErrorResponse'],
                            ],
                        ],
                    ],
                    '404NotFound' => [
                        'description' => 'Resource Not Found',
                        'content' => [
                            'application/json' => [
                                'schema' => ['$ref' => '#/components/schemas/StandardErrorResponse'],
                            ],
                        ],
                    ],
                    '422Unprocessable' => [
                        'description' => 'Validation Failed',
                        'content' => [
                            'application/json' => [
                                'schema' => ['$ref' => '#/components/schemas/StandardErrorResponse'],
                            ],
                        ],
                    ],
                ],
            ],
            'paths' => $this->getPaths(),
        ];
    }

    protected function getSchemas(): array
    {
        return [
            'StandardSuccessResponse' => [
                'type' => 'object',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => true],
                    'message' => ['type' => 'string', 'example' => 'Operation completed successfully.'],
                    'data' => ['type' => 'object'],
                ],
            ],
            'StandardErrorResponse' => [
                'type' => 'object',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => false],
                    'message' => ['type' => 'string', 'example' => 'Validation failed or resource not found.'],
                    'errors' => ['type' => 'object', 'example' => ['field' => ['The field is required.']]],
                ],
            ],
            'AcademicClass' => [
                'type' => 'object',
                'properties' => [
                    'class_level' => ['type' => 'integer', 'example' => 10],
                    'name' => ['type' => 'string', 'example' => 'Class 10'],
                    'grade_number' => ['type' => 'integer', 'example' => 10],
                    'stage' => ['type' => 'string', 'example' => 'Secondary (Madhyamik)'],
                    'board' => ['type' => 'string', 'example' => 'WBBSE'],
                    'subjects_count' => ['type' => 'integer', 'example' => 7],
                ],
            ],
            'Subject' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'example' => 3],
                    'name' => ['type' => 'string', 'example' => 'গণিত (Mathematics)'],
                    'code' => ['type' => 'string', 'example' => 'MATH'],
                    'icon_url' => ['type' => 'string', 'nullable' => true, 'example' => 'https://example.com/icons/math.svg'],
                    'color_code' => ['type' => 'string', 'example' => '#3b82f6'],
                    'sort_order' => ['type' => 'integer', 'example' => 3],
                    'is_active' => ['type' => 'boolean', 'example' => true],
                ],
            ],
            'Chapter' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'example' => 45],
                    'class_level' => ['type' => 'integer', 'example' => 10],
                    'subject_id' => ['type' => 'integer', 'example' => 3],
                    'chapter_number' => ['type' => 'integer', 'example' => 1],
                    'title' => ['type' => 'string', 'example' => 'একচল বিশিষ্ট দ্বিঘাত সমীকরণ (Quadratic Equations in One Variable)'],
                    'description' => ['type' => 'string', 'example' => 'Concept of quadratic equations, roots, factorization and Sridhar Acharya formula.'],
                    'sort_order' => ['type' => 'integer', 'example' => 1],
                    'is_active' => ['type' => 'boolean', 'example' => true],
                    'subject' => ['$ref' => '#/components/schemas/Subject'],
                ],
            ],
            'RecordedClass' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'example' => 101],
                    'chapter_id' => ['type' => 'integer', 'example' => 45],
                    'teacher_id' => ['type' => 'integer', 'nullable' => true, 'example' => 2],
                    'title' => ['type' => 'string', 'example' => 'দ্বিঘাত সমীকরণের ধারণা ও মূল নির্ণয়'],
                    'description' => ['type' => 'string', 'example' => 'Introduction to ax^2 + bx + c = 0 with solved examples.'],
                    'video_url' => ['type' => 'string', 'example' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
                    'thumbnail_url' => ['type' => 'string', 'nullable' => true, 'example' => 'https://images.unsplash.com/photo-math.jpg'],
                    'duration_minutes' => ['type' => 'integer', 'example' => 45],
                    'is_free' => ['type' => 'boolean', 'example' => true],
                    'is_active' => ['type' => 'boolean', 'example' => true],
                    'view_count' => ['type' => 'integer', 'example' => 120],
                ],
            ],
            'LiveClass' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'example' => 201],
                    'class_level' => ['type' => 'integer', 'example' => 10],
                    'subject_id' => ['type' => 'integer', 'example' => 1],
                    'chapter_id' => ['type' => 'integer', 'nullable' => true, 'example' => 45],
                    'teacher_id' => ['type' => 'integer', 'nullable' => true, 'example' => 2],
                    'title' => ['type' => 'string', 'example' => 'লাইভ ডাউট ক্লিয়ারিং: দ্বিঘাত সমীকরণ'],
                    'description' => ['type' => 'string', 'example' => 'Interactive live session with expert math faculty.'],
                    'learning_objectives' => ['type' => 'string', 'example' => 'Master factorization methods and discriminant rules.'],
                    'platform' => ['type' => 'string', 'enum' => ['google_meet', 'zoom', 'teams', 'youtube_live', 'other'], 'example' => 'google_meet'],
                    'scheduled_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2026-10-20T17:00:00Z'],
                    'ends_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2026-10-20T18:00:00Z'],
                    'duration_minutes' => ['type' => 'integer', 'example' => 60],
                    'meeting_url' => ['type' => 'string', 'nullable' => true, 'example' => 'https://meet.google.com/vid-math-live'],
                    'recording_url' => ['type' => 'string', 'nullable' => true, 'example' => 'https://www.youtube.com/watch?v=rec123'],
                    'status' => ['type' => 'string', 'enum' => ['scheduled', 'live', 'completed', 'cancelled'], 'example' => 'scheduled'],
                    'is_locked' => ['type' => 'boolean', 'example' => false],
                    'is_published' => ['type' => 'boolean', 'example' => true],
                ],
            ],
            'LiveClassDashboardStats' => [
                'type' => 'object',
                'properties' => [
                    'total_classes' => ['type' => 'integer', 'example' => 101],
                    'scheduled_classes' => ['type' => 'integer', 'example' => 85],
                    'live_classes' => ['type' => 'integer', 'example' => 2],
                    'completed_classes' => ['type' => 'integer', 'example' => 12],
                    'cancelled_classes' => ['type' => 'integer', 'example' => 2],
                    'upcoming_classes' => ['type' => 'integer', 'example' => 45],
                    'requiring_attention' => ['type' => 'integer', 'example' => 4],
                    'grade_breakdown' => [
                        'type' => 'object',
                        'example' => ['1' => 4, '2' => 4, '3' => 4, '4' => 4, '5' => 8, '6' => 12, '7' => 12, '8' => 12, '9' => 12, '10' => 16],
                    ],
                    'platform_breakdown' => [
                        'type' => 'object',
                        'example' => ['google_meet' => 60, 'zoom' => 25, 'teams' => 10, 'youtube_live' => 6],
                    ],
                ],
            ],
            'Note' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'example' => 301],
                    'chapter_id' => ['type' => 'integer', 'example' => 45],
                    'teacher_id' => ['type' => 'integer', 'nullable' => true, 'example' => 2],
                    'title' => ['type' => 'string', 'example' => 'দ্বিঘাত সমীকরণ সম্পূর্ণ সমাধান ও সূত্রাবলী'],
                    'description' => ['type' => 'string', 'example' => 'Comprehensive revision notes with formula sheet and Madhyamik question bank.'],
                    'file_url' => ['type' => 'string', 'example' => 'https://example.com/notes/class10-math-ch1.pdf'],
                    'file_type' => ['type' => 'string', 'example' => 'pdf'],
                    'file_size_bytes' => ['type' => 'integer', 'example' => 2621440],
                    'page_count' => ['type' => 'integer', 'example' => 16],
                    'is_free' => ['type' => 'boolean', 'example' => true],
                    'is_active' => ['type' => 'boolean', 'example' => true],
                    'download_count' => ['type' => 'integer', 'example' => 54],
                ],
            ],
            'Test' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'example' => 401],
                    'chapter_id' => ['type' => 'integer', 'example' => 45],
                    'title' => ['type' => 'string', 'example' => 'অধ্যায় ভিত্তিক মূল্যায়ন: দ্বিঘাত সমীকরণ'],
                    'description' => ['type' => 'string', 'example' => '10 MCQs testing core concepts and calculation speed.'],
                    'duration_minutes' => ['type' => 'integer', 'example' => 20],
                    'total_marks' => ['type' => 'integer', 'example' => 10],
                    'passing_marks' => ['type' => 'integer', 'example' => 4],
                    'is_free' => ['type' => 'boolean', 'example' => true],
                    'is_active' => ['type' => 'boolean', 'example' => true],
                    'questions_count' => ['type' => 'integer', 'example' => 10],
                ],
            ],
            'Question' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'example' => 501],
                    'test_id' => ['type' => 'integer', 'example' => 401],
                    'question_text' => ['type' => 'string', 'example' => 'ax^2 + bx + c = 0 সমীকরণের বীজ দুটি বাস্তব ও সমান হওয়ার শর্ত কী?'],
                    'option_a' => ['type' => 'string', 'example' => 'b^2 - 4ac > 0'],
                    'option_b' => ['type' => 'string', 'example' => 'b^2 - 4ac = 0'],
                    'option_c' => ['type' => 'string', 'example' => 'b^2 - 4ac < 0'],
                    'option_d' => ['type' => 'string', 'example' => 'b^2 + 4ac = 0'],
                    'correct_option' => ['type' => 'string', 'enum' => ['A', 'B', 'C', 'D'], 'example' => 'B'],
                    'marks' => ['type' => 'integer', 'example' => 1],
                    'sort_order' => ['type' => 'integer', 'example' => 0],
                ],
            ],
            'StudentProfile' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'example' => 10],
                    'user_id' => ['type' => 'integer', 'example' => 5],
                    'name' => ['type' => 'string', 'example' => 'Sourav Mukherjee'],
                    'class' => ['type' => 'integer', 'example' => 10],
                    'school_name' => ['type' => 'string', 'example' => 'Ballygunge Government High School'],
                    'district' => ['type' => 'string', 'example' => 'Kolkata'],
                    'avatar_url' => ['type' => 'string', 'nullable' => true, 'example' => '/storage/avatars/avatar1.jpg'],
                ],
            ],
            'Notification' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'example' => 1],
                    'user_id' => ['type' => 'integer', 'example' => 5],
                    'title' => ['type' => 'string', 'example' => 'Live Class Starting Soon!'],
                    'body' => ['type' => 'string', 'example' => 'Your Class 10 Math session starts in 15 minutes.'],
                    'type' => ['type' => 'string', 'example' => 'broadcast'],
                    'read_at' => ['type' => 'string', 'format' => 'date-time', 'nullable' => true],
                    'created_at' => ['type' => 'string', 'format' => 'date-time'],
                ],
            ],
            'SubscriptionPlan' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'example' => 1],
                    'name' => ['type' => 'string', 'example' => 'Madhyamik 2027 Annual Pass'],
                    'price' => ['type' => 'number', 'example' => 999.00],
                    'duration_days' => ['type' => 'integer', 'example' => 365],
                    'description' => ['type' => 'string', 'example' => 'Full unlimited access to all subjects, live classes, and question banks.'],
                    'features' => ['type' => 'array', 'items' => ['type' => 'string']],
                    'is_active' => ['type' => 'boolean', 'example' => true],
                ],
            ],
            'PaymentRecord' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'example' => 1],
                    'uuid' => ['type' => 'string', 'example' => 'pay-uuid-8a7b6c5d'],
                    'user_id' => ['type' => 'integer', 'example' => 5],
                    'amount' => ['type' => 'integer', 'description' => 'Amount in paise', 'example' => 99900],
                    'currency' => ['type' => 'string', 'example' => 'INR'],
                    'purpose' => ['type' => 'string', 'example' => 'subscription_purchase'],
                    'gateway' => ['type' => 'string', 'example' => 'razorpay'],
                    'gateway_order_id' => ['type' => 'string', 'example' => 'order_K8d82n92nd9'],
                    'gateway_payment_id' => ['type' => 'string', 'example' => 'pay_K8d83nd82nd'],
                    'status' => ['type' => 'string', 'enum' => ['pending', 'captured', 'failed', 'refunded', 'partially_refunded'], 'example' => 'captured'],
                    'receipt' => ['type' => 'string', 'example' => 'rcpt_882910'],
                    'created_at' => ['type' => 'string', 'format' => 'date-time'],
                ],
            ],
        ];
    }

    protected function getPaths(): array
    {
        return array_merge(
            $this->getCurriculumReferencePaths(),
            $this->getAuthPaths(),
            $this->getStudentProfileAndSubscriptionPaths(),
            $this->getStudentAcademicContentPaths(),
            $this->getStudentAssessmentPaths(),
            $this->getTeacherPaths(),
            $this->getAdminCurriculumPaths(),
            $this->getAdminLiveClassPaths(),
            $this->getAdminContentPaths(),
            $this->getAdminAssessmentPaths(),
            $this->getAdminUserAndAnalyticsPaths(),
            $this->getAdminPaymentPaths()
        );
    }

    // 1. Academic Classes & Curriculum Reference
    protected function getCurriculumReferencePaths(): array
    {
        return [
            '/api/classes' => [
                'get' => [
                    'tags' => ['Academic Classes & Curriculum'],
                    'summary' => 'List all academic classes (Classes 1 through 10)',
                    'description' => 'Returns supported class levels (1 to 10) with curriculum stage, WBBSE board affiliation, and count of mapped subjects.',
                    'responses' => [
                        '200' => [
                            'description' => 'Classes retrieved successfully',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'data' => [
                                                'type' => 'array',
                                                'items' => ['$ref' => '#/components/schemas/AcademicClass'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '/api/classes/{classLevel}/subjects' => [
                'get' => [
                    'tags' => ['Academic Classes & Curriculum'],
                    'summary' => 'Get subjects and chapters for a specific class (1–10)',
                    'parameters' => [
                        ['name' => 'classLevel', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 10, 'example' => 10]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Subjects and active chapters retrieved'],
                        '400' => ['$ref' => '#/components/responses/400BadRequest'],
                    ],
                ],
            ],
        ];
    }

    // 2. Authentication
    protected function getAuthPaths(): array
    {
        return [
            '/api/auth/register' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'Register a new student account and initiate 3-day free trial',
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['name', 'mobile', 'password', 'password_confirmation', 'class', 'school_name', 'district'],
                                    'properties' => [
                                        'name' => ['type' => 'string', 'example' => 'Sourav Mukherjee'],
                                        'mobile' => ['type' => 'string', 'pattern' => '^[6-9]\d{9}$', 'example' => '9876543210'],
                                        'email' => ['type' => 'string', 'format' => 'email', 'example' => 'sourav@example.com'],
                                        'password' => ['type' => 'string', 'format' => 'password', 'minLength' => 8, 'example' => 'StudentPass123!'],
                                        'password_confirmation' => ['type' => 'string', 'format' => 'password', 'example' => 'StudentPass123!'],
                                        'class' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 10, 'example' => 10],
                                        'school_name' => ['type' => 'string', 'example' => 'Ballygunge Government High School'],
                                        'district' => ['type' => 'string', 'example' => 'Kolkata'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Student registered, trial activated, returns Sanctum token'],
                        '422' => ['$ref' => '#/components/responses/422Unprocessable'],
                    ],
                ],
            ],
            '/api/auth/login' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'Student login using mobile number and password',
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['mobile', 'password'],
                                    'properties' => [
                                        'mobile' => ['type' => 'string', 'example' => '9876543210'],
                                        'password' => ['type' => 'string', 'format' => 'password', 'example' => 'StudentPass123!'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Login successful, returns Sanctum token'],
                        '401' => ['$ref' => '#/components/responses/401Unauthorized'],
                    ],
                ],
            ],
            '/api/auth/send-otp' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'Send OTP to mobile phone for passwordless login',
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['phone'],
                                    'properties' => [
                                        'phone' => ['type' => 'string', 'example' => '9876543210'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'OTP dispatched successfully'],
                    ],
                ],
            ],
            '/api/auth/verify-otp' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'Verify mobile OTP and authenticate student',
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['phone', 'otp'],
                                    'properties' => [
                                        'phone' => ['type' => 'string', 'example' => '9876543210'],
                                        'otp' => ['type' => 'string', 'example' => '123456'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'OTP verified, returns Sanctum token'],
                        '400' => ['$ref' => '#/components/responses/400BadRequest'],
                    ],
                ],
            ],
            '/api/auth/forgot-password' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'Reset or update password for authenticated student',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['password', 'password_confirmation'],
                                    'properties' => [
                                        'password' => ['type' => 'string', 'format' => 'password', 'minLength' => 8, 'example' => 'NewSecret123!'],
                                        'password_confirmation' => ['type' => 'string', 'format' => 'password', 'example' => 'NewSecret123!'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Password updated successfully'],
                        '401' => ['$ref' => '#/components/responses/401Unauthorized'],
                    ],
                ],
            ],
            '/api/auth/refresh-token' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'Refresh the active API token for the current device session',
                    'security' => [['BearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Token refreshed successfully'],
                        '401' => ['$ref' => '#/components/responses/401Unauthorized'],
                    ],
                ],
            ],
            '/api/auth/logout' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'Log out current session and invalidate bearer token',
                    'security' => [['BearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Successfully logged out'],
                        '401' => ['$ref' => '#/components/responses/401Unauthorized'],
                    ],
                ],
            ],
        ];
    }

    // 3. Student - Profile & Subscriptions / Payments
    protected function getStudentProfileAndSubscriptionPaths(): array
    {
        return [
            '/api/profile' => [
                'get' => [
                    'tags' => ['Student - Profile & Subscriptions'],
                    'summary' => 'Get authenticated student profile and access/subscription status',
                    'security' => [['BearerAuth' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'Profile, trial status, and subscription state retrieved',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'data' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'user' => ['type' => 'object'],
                                                    'profile' => ['$ref' => '#/components/schemas/StudentProfile'],
                                                    'is_in_trial' => ['type' => 'boolean', 'example' => true],
                                                    'is_trial_expired' => ['type' => 'boolean', 'example' => false],
                                                    'has_active_subscription' => ['type' => 'boolean', 'example' => false],
                                                    'has_access' => ['type' => 'boolean', 'example' => true],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        '401' => ['$ref' => '#/components/responses/401Unauthorized'],
                    ],
                ],
            ],
            '/api/profile/setup' => [
                'post' => [
                    'tags' => ['Student - Profile & Subscriptions'],
                    'summary' => 'Complete initial student profile setup and activate 3-day free trial',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['name', 'class', 'school_name', 'district'],
                                    'properties' => [
                                        'name' => ['type' => 'string', 'example' => 'Sourav Mukherjee'],
                                        'class' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 10, 'example' => 10],
                                        'school_name' => ['type' => 'string', 'example' => 'Ballygunge Government High School'],
                                        'district' => ['type' => 'string', 'example' => 'Kolkata'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Profile created and 3-day free trial initiated'],
                        '409' => ['description' => 'Profile already set up'],
                    ],
                ],
            ],
            '/api/profile/update' => [
                'put' => [
                    'tags' => ['Student - Profile & Subscriptions'],
                    'summary' => 'Update student profile fields and optional avatar image',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => false,
                        'content' => [
                            'multipart/form-data' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'name' => ['type' => 'string', 'example' => 'Sourav Mukherjee'],
                                        'class' => ['type' => 'integer', 'example' => 10],
                                        'school_name' => ['type' => 'string', 'example' => 'Ballygunge Government High School'],
                                        'district' => ['type' => 'string', 'example' => 'Kolkata'],
                                        'avatar' => ['type' => 'string', 'format' => 'binary'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Profile updated successfully'],
                    ],
                ],
            ],
            '/api/notifications' => [
                'get' => [
                    'tags' => ['Student - Profile & Subscriptions'],
                    'summary' => 'List notifications for the authenticated student (unread first)',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'page', 'in' => 'query', 'schema' => ['type' => 'integer', 'example' => 1]],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Paginated notifications list',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'data' => [
                                                'type' => 'array',
                                                'items' => ['$ref' => '#/components/schemas/Notification'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '/api/notifications/{id}/read' => [
                'post' => [
                    'tags' => ['Student - Profile & Subscriptions'],
                    'summary' => 'Mark a notification as read',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Notification marked as read'],
                    ],
                ],
            ],
            '/api/subscriptions' => [
                'get' => [
                    'tags' => ['Student - Profile & Subscriptions'],
                    'summary' => 'List active subscription plans available for purchase',
                    'responses' => [
                        '200' => ['description' => 'Plans retrieved successfully'],
                    ],
                ],
            ],
            '/api/subscriptions/current' => [
                'get' => [
                    'tags' => ['Student - Profile & Subscriptions'],
                    'summary' => 'Get current active student subscription and validity',
                    'security' => [['BearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Current subscription status retrieved'],
                    ],
                ],
            ],
            '/api/subscriptions/{id}/initiate' => [
                'post' => [
                    'tags' => ['Student - Profile & Subscriptions'],
                    'summary' => 'Initiate Razorpay order for subscription plan purchase',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Razorpay order created with order_id and key_id'],
                    ],
                ],
            ],
            '/api/subscriptions/verify' => [
                'post' => [
                    'tags' => ['Student - Profile & Subscriptions'],
                    'summary' => 'Verify Razorpay subscription payment signature and activate access',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature'],
                                    'properties' => [
                                        'razorpay_order_id' => ['type' => 'string', 'example' => 'order_K8d82n92nd9'],
                                        'razorpay_payment_id' => ['type' => 'string', 'example' => 'pay_K8d83nd82nd'],
                                        'razorpay_signature' => ['type' => 'string', 'example' => '9a8d7f6e5...'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Payment verified and subscription activated'],
                        '400' => ['description' => 'Signature verification failed'],
                    ],
                ],
            ],
            '/api/subscriptions/failed' => [
                'post' => [
                    'tags' => ['Student - Profile & Subscriptions'],
                    'summary' => 'Report failed subscription checkout or customer dismissal',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['razorpay_order_id'],
                                    'properties' => [
                                        'razorpay_order_id' => ['type' => 'string', 'example' => 'order_K8d82n92nd9'],
                                        'error_code' => ['type' => 'string', 'example' => 'BAD_REQUEST_ERROR'],
                                        'error_description' => ['type' => 'string', 'example' => 'Payment failed by user'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Failure recorded'],
                    ],
                ],
            ],
            '/api/subscriptions/webhook' => [
                'post' => [
                    'tags' => ['Student - Profile & Subscriptions'],
                    'summary' => 'Razorpay subscription webhook endpoint',
                    'description' => 'Receives asynchronous events such as payment.captured, payment.failed, and refund.processed from Razorpay.',
                    'responses' => [
                        '200' => ['description' => 'Webhook received and processed idempotently'],
                    ],
                ],
            ],
            '/api/payments/one-time' => [
                'post' => [
                    'tags' => ['Student - Profile & Subscriptions'],
                    'summary' => 'Initiate a generic one-time Razorpay payment order',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['amount'],
                                    'properties' => [
                                        'amount' => ['type' => 'number', 'example' => 299.00],
                                        'purpose' => ['type' => 'string', 'example' => 'one_time_purchase'],
                                        'description' => ['type' => 'string', 'example' => 'Class 10 Madhyamik Revision Booklet'],
                                        'notes' => ['type' => 'object'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Payment order created with order_id and key_id'],
                    ],
                ],
            ],
            '/api/payments/verify' => [
                'post' => [
                    'tags' => ['Student - Profile & Subscriptions'],
                    'summary' => 'Verify one-time Razorpay checkout signature and capture payment',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature'],
                                    'properties' => [
                                        'razorpay_order_id' => ['type' => 'string', 'example' => 'order_K8d82n92nd9'],
                                        'razorpay_payment_id' => ['type' => 'string', 'example' => 'pay_K8d83nd82nd'],
                                        'razorpay_signature' => ['type' => 'string', 'example' => '9a8d7f6e5...'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Payment verified and captured'],
                        '400' => ['description' => 'Signature verification failed'],
                    ],
                ],
            ],
            '/api/payments/failed' => [
                'post' => [
                    'tags' => ['Student - Profile & Subscriptions'],
                    'summary' => 'Record one-time payment failure or checkout dismissal',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['razorpay_order_id'],
                                    'properties' => [
                                        'razorpay_order_id' => ['type' => 'string', 'example' => 'order_K8d82n92nd9'],
                                        'error_code' => ['type' => 'string', 'example' => 'PAYMENT_CANCELLED'],
                                        'error_description' => ['type' => 'string', 'example' => 'Checkout cancelled by customer.'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Payment failure logged'],
                    ],
                ],
            ],
            '/api/payments/webhook' => [
                'post' => [
                    'tags' => ['Student - Profile & Subscriptions'],
                    'summary' => 'Razorpay payment webhook endpoint',
                    'description' => 'Receives asynchronous payment and refund webhook notifications from Razorpay.',
                    'responses' => [
                        '200' => ['description' => 'Webhook received and processed'],
                    ],
                ],
            ],
            '/api/payments/{id}' => [
                'get' => [
                    'tags' => ['Student - Profile & Subscriptions'],
                    'summary' => 'Get student transaction receipt by ID, UUID, or Razorpay Order ID',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string', 'example' => 'PAY-UUID-1234']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Payment details with refunds'],
                        '404' => ['$ref' => '#/components/responses/404NotFound'],
                    ],
                ],
            ],
        ];
    }

    // 4. Student - Academic Content
    protected function getStudentAcademicContentPaths(): array
    {
        return [
            '/api/subjects' => [
                'get' => [
                    'tags' => ['Student - Academic Content'],
                    'summary' => 'List all active curriculum subjects',
                    'security' => [['BearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Active subjects list'],
                    ],
                ],
            ],
            '/api/subjects/{id}/chapters' => [
                'get' => [
                    'tags' => ['Student - Academic Content'],
                    'summary' => 'List chapters for subject matching student class level with completion state',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 3]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Chapters list with is_completed and is_locked flags'],
                    ],
                ],
            ],
            '/api/chapters/{id}' => [
                'get' => [
                    'tags' => ['Student - Academic Content'],
                    'summary' => 'Get chapter details by ID',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 45]],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Chapter details with subject',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'data' => ['$ref' => '#/components/schemas/Chapter'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '/api/chapters/{id}/complete' => [
                'post' => [
                    'tags' => ['Student - Academic Content'],
                    'summary' => 'Mark chapter completed by student and award points/badge',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 45]],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Chapter marked complete and reward returned',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'message' => ['type' => 'string', 'example' => 'Chapter marked as completed!'],
                                            'data' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'completed' => ['type' => 'boolean', 'example' => true],
                                                    'reward' => ['type' => 'object'],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        '401' => ['$ref' => '#/components/responses/401Unauthorized'],
                        '403' => ['description' => 'Trial expired or subscription required'],
                    ],
                ],
            ],
            '/api/chapters/{id}/recorded-classes' => [
                'get' => [
                    'tags' => ['Student - Academic Content'],
                    'summary' => 'List recorded video classes for a chapter',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 45]],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'List of recorded classes',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'data' => [
                                                'type' => 'array',
                                                'items' => ['$ref' => '#/components/schemas/RecordedClass'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '/api/recorded-classes/{id}' => [
                'get' => [
                    'tags' => ['Student - Academic Content'],
                    'summary' => 'View single recorded video class details',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 101]],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Recorded class details',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'data' => ['$ref' => '#/components/schemas/RecordedClass'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '/api/chapters/{id}/notes' => [
                'get' => [
                    'tags' => ['Student - Academic Content'],
                    'summary' => 'List study notes & PDFs for a chapter',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 45]],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'List of notes',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'data' => [
                                                'type' => 'array',
                                                'items' => ['$ref' => '#/components/schemas/Note'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '/api/notes/{id}' => [
                'get' => [
                    'tags' => ['Student - Academic Content'],
                    'summary' => 'View single study note details',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 301]],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Note details',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'data' => ['$ref' => '#/components/schemas/Note'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '/api/chapters/{id}/live-classes' => [
                'get' => [
                    'tags' => ['Student - Academic Content'],
                    'summary' => 'List scheduled live classes for a chapter',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 45]],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'List of chapter live classes grouped by upcoming, live, past',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'data' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'upcoming' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/LiveClass']],
                                                    'live' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/LiveClass']],
                                                    'past' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/LiveClass']],
                                                    'has_access' => ['type' => 'boolean', 'example' => true],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '/api/live-classes' => [
                'get' => [
                    'tags' => ['Student - Academic Content'],
                    'summary' => 'List all upcoming and live classes for student (filtered by class_level and subject)',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'class_level', 'in' => 'query', 'required' => false, 'schema' => ['type' => 'integer', 'example' => 10]],
                        ['name' => 'subject_id', 'in' => 'query', 'required' => false, 'schema' => ['type' => 'integer', 'example' => 1]],
                        ['name' => 'status', 'in' => 'query', 'required' => false, 'schema' => ['type' => 'string', 'example' => 'scheduled']],
                        ['name' => 'page', 'in' => 'query', 'required' => false, 'schema' => ['type' => 'integer', 'example' => 1]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Paginated live classes for student'],
                    ],
                ],
            ],
            '/api/live-classes/now' => [
                'get' => [
                    'tags' => ['Student - Academic Content'],
                    'summary' => 'Get currently active live classes for student grade',
                    'security' => [['BearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Currently active live sessions'],
                    ],
                ],
            ],
            '/api/live-classes/{id}' => [
                'get' => [
                    'tags' => ['Student - Academic Content'],
                    'summary' => 'Get single live class details (unlocked meeting link if subscribed/trial)',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 201]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Live class details with join link or subscription required message'],
                    ],
                ],
            ],
        ];
    }

    // 5. Student - Assessments & Tests
    protected function getStudentAssessmentPaths(): array
    {
        return [
            '/api/chapters/{id}/tests' => [
                'get' => [
                    'tags' => ['Student - Assessments & Tests'],
                    'summary' => 'List practice tests available for a chapter',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 45]],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'List of chapter tests',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'data' => [
                                                'type' => 'array',
                                                'items' => ['$ref' => '#/components/schemas/Test'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '/api/tests/{id}' => [
                'get' => [
                    'tags' => ['Student - Assessments & Tests'],
                    'summary' => 'Get test questions and begin attempt',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 401]],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Test details with questions (options A-D, answers hidden)',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'data' => ['$ref' => '#/components/schemas/Test'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '/api/tests/{id}/attempt' => [
                'post' => [
                    'tags' => ['Student - Assessments & Tests'],
                    'summary' => 'Submit student answers for automated evaluation and scoring',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 401]],
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['answers'],
                                    'properties' => [
                                        'answers' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'object',
                                                'required' => ['question_id', 'selected_answer'],
                                                'properties' => [
                                                    'question_id' => ['type' => 'integer', 'example' => 501],
                                                    'selected_answer' => ['type' => 'string', 'example' => 'B'],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Test evaluated successfully with instant feedback',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'score' => ['type' => 'integer', 'example' => 8],
                                            'total_marks' => ['type' => 'integer', 'example' => 10],
                                            'percentage' => ['type' => 'number', 'example' => 80.0],
                                            'is_passed' => ['type' => 'boolean', 'example' => true],
                                            'results' => ['type' => 'array', 'items' => ['type' => 'object']],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    // 6. Teacher - Portal
    protected function getTeacherPaths(): array
    {
        return [
            '/api/teacher/dashboard' => [
                'get' => [
                    'tags' => ['Teacher - Portal'],
                    'summary' => 'Get teacher operational dashboard stats',
                    'security' => [['BearerAuth' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'Counts of assigned classes, uploaded notes, and upcoming live sessions',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'data' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'recorded_classes' => ['type' => 'integer', 'example' => 12],
                                                    'live_classes' => ['type' => 'integer', 'example' => 8],
                                                    'upcoming_classes' => ['type' => 'integer', 'example' => 3],
                                                    'notes_uploaded' => ['type' => 'integer', 'example' => 15],
                                                    'subjects_assigned' => ['type' => 'integer', 'example' => 2],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '/api/teacher/subjects' => [
                'get' => [
                    'tags' => ['Teacher - Portal'],
                    'summary' => 'Get subjects and chapters assigned to authenticated teacher',
                    'security' => [['BearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Assigned subjects list with chapters'],
                    ],
                ],
            ],
        ];
    }

    // 7. Admin - Curriculum Management
    protected function getAdminCurriculumPaths(): array
    {
        return [
            '/api/admin/subjects' => [
                'get' => [
                    'tags' => ['Admin - Curriculum Management'],
                    'summary' => 'List all curriculum subjects',
                    'security' => [['BearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'All subjects ordered by sort_order'],
                    ],
                ],
                'post' => [
                    'tags' => ['Admin - Curriculum Management'],
                    'summary' => 'Create a new subject',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['name', 'slug', 'color', 'icon'],
                                    'properties' => [
                                        'name' => ['type' => 'string', 'example' => 'জীবন বিজ্ঞান (Life Science)'],
                                        'slug' => ['type' => 'string', 'example' => 'life-science'],
                                        'color' => ['type' => 'string', 'example' => '#10b981'],
                                        'icon' => ['type' => 'string', 'example' => 'leaf'],
                                        'sort_order' => ['type' => 'integer', 'example' => 6],
                                        'is_active' => ['type' => 'boolean', 'example' => true],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Subject created successfully'],
                    ],
                ],
            ],
            '/api/admin/subjects/{id}' => [
                'put' => [
                    'tags' => ['Admin - Curriculum Management'],
                    'summary' => 'Update an existing subject',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Subject']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Subject updated'],
                    ],
                ],
                'delete' => [
                    'tags' => ['Admin - Curriculum Management'],
                    'summary' => 'Delete a subject',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'responses' => [
                        '200' => ['description' => 'Subject deleted'],
                    ],
                ],
            ],
            '/api/admin/subjects/{id}/chapters' => [
                'get' => [
                    'tags' => ['Admin - Curriculum Management'],
                    'summary' => 'List chapters under a specific subject',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'responses' => [
                        '200' => ['description' => 'Chapters list ordered by sort_order'],
                    ],
                ],
            ],
            '/api/admin/classes' => [
                'get' => [
                    'tags' => ['Admin - Curriculum Management'],
                    'summary' => 'List all academic classes with metrics and counts',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'search', 'in' => 'query', 'schema' => ['type' => 'string']],
                        ['name' => 'board', 'in' => 'query', 'schema' => ['type' => 'string']],
                        ['name' => 'stage', 'in' => 'query', 'schema' => ['type' => 'string']],
                        ['name' => 'status', 'in' => 'query', 'schema' => ['type' => 'string', 'enum' => ['active', 'inactive']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'List of academic classes'],
                    ],
                ],
                'post' => [
                    'tags' => ['Admin - Curriculum Management'],
                    'summary' => 'Create a new academic class',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['class_level', 'name'],
                                    'properties' => [
                                        'class_level' => ['type' => 'integer', 'example' => 11],
                                        'name' => ['type' => 'string', 'example' => 'Class 11'],
                                        'display_name' => ['type' => 'string', 'example' => 'Class 11 (Higher Secondary)'],
                                        'board' => ['type' => 'string', 'example' => 'WBCHSE'],
                                        'stage' => ['type' => 'string', 'example' => 'Higher Secondary'],
                                        'stream' => ['type' => 'string', 'example' => 'Science'],
                                        'sort_order' => ['type' => 'integer', 'example' => 11],
                                        'is_active' => ['type' => 'boolean', 'example' => true],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Class created successfully'],
                    ],
                ],
            ],
            '/api/admin/classes/{id}' => [
                'get' => [
                    'tags' => ['Admin - Curriculum Management'],
                    'summary' => 'Get academic class details with assigned subjects',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'responses' => [
                        '200' => ['description' => 'Class details with assigned subjects'],
                    ],
                ],
                'put' => [
                    'tags' => ['Admin - Curriculum Management'],
                    'summary' => 'Update an academic class',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'name' => ['type' => 'string', 'example' => 'Class 11'],
                                        'display_name' => ['type' => 'string', 'example' => 'Class 11 (Higher Secondary)'],
                                        'board' => ['type' => 'string', 'example' => 'WBCHSE'],
                                        'stage' => ['type' => 'string', 'example' => 'Higher Secondary'],
                                        'is_active' => ['type' => 'boolean', 'example' => true],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Class updated successfully'],
                    ],
                ],
                'delete' => [
                    'tags' => ['Admin - Curriculum Management'],
                    'summary' => 'Delete an academic class',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]],
                        ['name' => 'force', 'in' => 'query', 'schema' => ['type' => 'boolean']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Class deleted successfully'],
                    ],
                ],
            ],
            '/api/admin/classes/{id}/toggle-status' => [
                'post' => [
                    'tags' => ['Admin - Curriculum Management'],
                    'summary' => 'Toggle active status of an academic class',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'responses' => [
                        '200' => ['description' => 'Class status updated'],
                    ],
                ],
            ],
            '/api/admin/class-subjects' => [
                'get' => [
                    'tags' => ['Admin - Curriculum Management'],
                    'summary' => 'Get subjects assigned to a specific class level (1-10)',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'class_level', 'in' => 'query', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 10]]],
                    'responses' => [
                        '200' => ['description' => 'Assigned and unassigned subjects for class'],
                    ],
                ],
                'post' => [
                    'tags' => ['Admin - Curriculum Management'],
                    'summary' => 'Assign a subject to a class level',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['class_level', 'subject_id'],
                                    'properties' => [
                                        'class_level' => ['type' => 'integer', 'example' => 10],
                                        'subject_id' => ['type' => 'integer', 'example' => 3],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Subject assigned to class'],
                    ],
                ],
            ],
            '/api/admin/class-subjects/all' => [
                'get' => [
                    'tags' => ['Admin - Curriculum Management'],
                    'summary' => 'Get all class-subject mappings across Class 1 to 10',
                    'security' => [['BearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Complete array of class-subject mappings'],
                    ],
                ],
            ],
            '/api/admin/class-subjects/{classLevel}/{subjectId}' => [
                'delete' => [
                    'tags' => ['Admin - Curriculum Management'],
                    'summary' => 'Unassign a subject from a class level',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'classLevel', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 10]],
                        ['name' => 'subjectId', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 3]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Subject mapping removed'],
                    ],
                ],
            ],
            '/api/admin/class-subjects/{classLevel}/chapters' => [
                'get' => [
                    'tags' => ['Admin - Curriculum Management'],
                    'summary' => 'Get chapters for specific class and subject combination',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'classLevel', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 10]],
                        ['name' => 'subject_id', 'in' => 'query', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 3]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Chapters list matching class and subject'],
                    ],
                ],
            ],
            '/api/admin/chapters' => [
                'post' => [
                    'tags' => ['Admin - Curriculum Management'],
                    'summary' => 'Create a new curriculum chapter',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['subject_id', 'title'],
                                    'properties' => [
                                        'subject_id' => ['type' => 'integer', 'example' => 3],
                                        'class_level' => ['type' => 'integer', 'nullable' => true, 'example' => 10],
                                        'title' => ['type' => 'string', 'example' => 'বৃত্তের স্পর্শক সংক্রান্ত উপপাদ্য'],
                                        'description' => ['type' => 'string', 'example' => 'Tangents to a circle properties and theorems.'],
                                        'sort_order' => ['type' => 'integer', 'example' => 5],
                                        'is_active' => ['type' => 'boolean', 'example' => true],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Chapter created successfully'],
                    ],
                ],
            ],
            '/api/admin/chapters/{id}' => [
                'get' => [
                    'tags' => ['Admin - Curriculum Management'],
                    'summary' => 'Get chapter details with resource counts',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 45]]],
                    'responses' => [
                        '200' => ['description' => 'Chapter detail with counts of videos, live classes, notes, tests'],
                    ],
                ],
                'put' => [
                    'tags' => ['Admin - Curriculum Management'],
                    'summary' => 'Update a chapter',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 45]]],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Chapter']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Chapter updated successfully'],
                    ],
                ],
                'delete' => [
                    'tags' => ['Admin - Curriculum Management'],
                    'summary' => 'Delete a chapter',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 45]]],
                    'responses' => [
                        '200' => ['description' => 'Chapter deleted'],
                    ],
                ],
            ],
        ];
    }

    // 8. Admin - Live Classes Management
    protected function getAdminLiveClassPaths(): array
    {
        return [
            '/api/admin/live-classes/dashboard-stats' => [
                'get' => [
                    'tags' => ['Admin - Live Classes Management'],
                    'summary' => 'Get real-time operational dashboard metrics for live classes',
                    'description' => 'Returns counts of total, scheduled, live, completed, cancelled, upcoming, and classes requiring attention (e.g. past due or missing link), along with grade and platform breakdowns.',
                    'security' => [['BearerAuth' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'Operational metrics retrieved successfully',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'data' => ['$ref' => '#/components/schemas/LiveClassDashboardStats'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '/api/admin/live-classes' => [
                'get' => [
                    'tags' => ['Admin - Live Classes Management'],
                    'summary' => 'List live classes with multi-criteria filters and server-side pagination',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'class_level', 'in' => 'query', 'schema' => ['type' => 'integer', 'example' => 10]],
                        ['name' => 'subject_id', 'in' => 'query', 'schema' => ['type' => 'integer']],
                        ['name' => 'chapter_id', 'in' => 'query', 'schema' => ['type' => 'integer']],
                        ['name' => 'teacher_id', 'in' => 'query', 'schema' => ['type' => 'integer']],
                        ['name' => 'status', 'in' => 'query', 'schema' => ['type' => 'string', 'enum' => ['scheduled', 'live', 'completed', 'cancelled', 'attention']]],
                        ['name' => 'platform', 'in' => 'query', 'schema' => ['type' => 'string', 'enum' => ['google_meet', 'zoom', 'teams', 'youtube_live', 'other']]],
                        ['name' => 'is_published', 'in' => 'query', 'schema' => ['type' => 'string', 'enum' => ['0', '1', 'all']]],
                        ['name' => 'from_date', 'in' => 'query', 'schema' => ['type' => 'string', 'format' => 'date']],
                        ['name' => 'to_date', 'in' => 'query', 'schema' => ['type' => 'string', 'format' => 'date']],
                        ['name' => 'search', 'in' => 'query', 'schema' => ['type' => 'string']],
                        ['name' => 'per_page', 'in' => 'query', 'schema' => ['type' => 'integer', 'example' => 15]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Paginated live class list'],
                    ],
                ],
                'post' => [
                    'tags' => ['Admin - Live Classes Management'],
                    'summary' => 'Create and schedule a new live class with meeting link validation',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['class_level', 'subject_id', 'title', 'scheduled_at', 'duration_minutes', 'platform', 'meeting_url'],
                                    'properties' => [
                                        'class_level' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 10, 'example' => 10],
                                        'subject_id' => ['type' => 'integer', 'example' => 1],
                                        'chapter_id' => ['type' => 'integer', 'nullable' => true, 'example' => 45],
                                        'teacher_id' => ['type' => 'integer', 'nullable' => true, 'example' => 2],
                                        'title' => ['type' => 'string', 'example' => 'মাধ্যমিক টেস্ট পেপার সমাধান: জীবন বিজ্ঞান'],
                                        'description' => ['type' => 'string', 'example' => 'Previous years Madhyamik question paper analysis.'],
                                        'learning_objectives' => ['type' => 'string', 'example' => 'Detailed 2-mark and 5-mark answer formulation technique.'],
                                        'platform' => ['type' => 'string', 'enum' => ['google_meet', 'zoom', 'teams', 'youtube_live', 'other'], 'example' => 'google_meet'],
                                        'scheduled_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2026-10-25T18:00:00Z'],
                                        'duration_minutes' => ['type' => 'integer', 'minimum' => 15, 'maximum' => 240, 'example' => 60],
                                        'meeting_url' => ['type' => 'string', 'example' => 'https://meet.google.com/abc-defg-hij'],
                                        'is_published' => ['type' => 'boolean', 'example' => true],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Live class created successfully'],
                        '422' => ['$ref' => '#/components/responses/422Unprocessable'],
                    ],
                ],
            ],
            '/api/admin/live-classes/{id}' => [
                'get' => [
                    'tags' => ['Admin - Live Classes Management'],
                    'summary' => 'Get details of a single live class',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'responses' => [
                        '200' => ['description' => 'Live class details with relationships'],
                    ],
                ],
                'put' => [
                    'tags' => ['Admin - Live Classes Management'],
                    'summary' => 'Update live class metadata, schedule, or meeting URL',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/LiveClass']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Updated successfully'],
                    ],
                ],
                'delete' => [
                    'tags' => ['Admin - Live Classes Management'],
                    'summary' => 'Delete a live class record',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'responses' => [
                        '200' => ['description' => 'Deleted successfully'],
                    ],
                ],
            ],
            '/api/admin/live-classes/{id}/status' => [
                'patch' => [
                    'tags' => ['Admin - Live Classes Management'],
                    'summary' => 'Update live class session status (live, completed, cancelled) or add post-class recording',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['status'],
                                    'properties' => [
                                        'status' => ['type' => 'string', 'enum' => ['scheduled', 'live', 'completed', 'cancelled'], 'example' => 'live'],
                                        'recording_url' => ['type' => 'string', 'nullable' => true, 'example' => 'https://www.youtube.com/watch?v=recorded123'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Status transitioned successfully'],
                    ],
                ],
            ],
            '/api/admin/live-classes/{id}/reschedule' => [
                'patch' => [
                    'tags' => ['Admin - Live Classes Management'],
                    'summary' => 'Reschedule a live class to a new date and start time with optional student notification',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['scheduled_at'],
                                    'properties' => [
                                        'scheduled_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2026-10-26T19:00:00Z'],
                                        'duration_minutes' => ['type' => 'integer', 'example' => 60],
                                        'notify_students' => ['type' => 'boolean', 'example' => true],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Rescheduled successfully'],
                    ],
                ],
            ],
            '/api/admin/live-classes/{id}/cancel' => [
                'patch' => [
                    'tags' => ['Admin - Live Classes Management'],
                    'summary' => 'Cancel a live class session with mandatory cancellation reason',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['reason'],
                                    'properties' => [
                                        'reason' => ['type' => 'string', 'example' => 'Teacher emergency medical leave.'],
                                        'notify_students' => ['type' => 'boolean', 'example' => true],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Cancelled successfully'],
                    ],
                ],
            ],
        ];
    }

    // 9. Admin - Educational Content (Recorded Classes & Study Notes)
    protected function getAdminContentPaths(): array
    {
        return [
            '/api/admin/recorded-classes' => [
                'get' => [
                    'tags' => ['Admin - Educational Content'],
                    'summary' => 'List recorded classes with Class 1–10, Subject, and Status filtering',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'class_level', 'in' => 'query', 'schema' => ['type' => 'integer', 'example' => 10], 'description' => 'Filter by class grade (1-10)'],
                        ['name' => 'subject_id', 'in' => 'query', 'schema' => ['type' => 'integer', 'example' => 3], 'description' => 'Filter by subject ID'],
                        ['name' => 'chapter_id', 'in' => 'query', 'schema' => ['type' => 'integer'], 'description' => 'Filter by chapter ID'],
                        ['name' => 'is_active', 'in' => 'query', 'schema' => ['type' => 'string', 'enum' => ['0', '1', 'all']]],
                        ['name' => 'search', 'in' => 'query', 'schema' => ['type' => 'string']],
                        ['name' => 'per_page', 'in' => 'query', 'schema' => ['type' => 'integer', 'example' => 15]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Paginated list of recorded classes'],
                    ],
                ],
                'post' => [
                    'tags' => ['Admin - Educational Content'],
                    'summary' => 'Create a new recorded class lesson',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['chapter_id', 'title', 'video_url'],
                                    'properties' => [
                                        'chapter_id' => ['type' => 'integer', 'example' => 45],
                                        'title' => ['type' => 'string', 'example' => 'দ্বিঘাত সমীকরণ বাস্তব সমস্যা সমাধান'],
                                        'description' => ['type' => 'string', 'example' => 'Word problem formulation and solving techniques.'],
                                        'video_url' => ['type' => 'string', 'example' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
                                        'duration_minutes' => ['type' => 'integer', 'example' => 40],
                                        'is_free' => ['type' => 'boolean', 'example' => false],
                                        'is_active' => ['type' => 'boolean', 'example' => true],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Recorded class created'],
                        '422' => ['$ref' => '#/components/responses/422Unprocessable'],
                    ],
                ],
            ],
            '/api/admin/recorded-classes/{id}' => [
                'get' => [
                    'tags' => ['Admin - Educational Content'],
                    'summary' => 'Fetch a single recorded class by ID',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 101]]],
                    'responses' => [
                        '200' => ['description' => 'Recorded class found'],
                        '404' => ['$ref' => '#/components/responses/404NotFound'],
                    ],
                ],
                'put' => [
                    'tags' => ['Admin - Educational Content'],
                    'summary' => 'Update an existing recorded class',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 101]]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'title' => ['type' => 'string'],
                                        'description' => ['type' => 'string'],
                                        'is_active' => ['type' => 'boolean'],
                                        'is_free' => ['type' => 'boolean'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Updated successfully'],
                    ],
                ],
                'delete' => [
                    'tags' => ['Admin - Educational Content'],
                    'summary' => 'Delete a recorded class',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 101]]],
                    'responses' => [
                        '200' => ['description' => 'Deleted successfully'],
                    ],
                ],
            ],
            '/api/admin/notes' => [
                'get' => [
                    'tags' => ['Admin - Educational Content'],
                    'summary' => 'List study notes with filters (class_level, subject_id, status, search)',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'class_level', 'in' => 'query', 'schema' => ['type' => 'integer', 'example' => 10]],
                        ['name' => 'subject_id', 'in' => 'query', 'schema' => ['type' => 'integer']],
                        ['name' => 'chapter_id', 'in' => 'query', 'schema' => ['type' => 'integer']],
                        ['name' => 'file_type', 'in' => 'query', 'schema' => ['type' => 'string']],
                        ['name' => 'is_active', 'in' => 'query', 'schema' => ['type' => 'string']],
                        ['name' => 'search', 'in' => 'query', 'schema' => ['type' => 'string']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Paginated list of study notes'],
                    ],
                ],
                'post' => [
                    'tags' => ['Admin - Educational Content'],
                    'summary' => 'Upload / publish a study note',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['chapter_id', 'title', 'file_url'],
                                    'properties' => [
                                        'chapter_id' => ['type' => 'integer', 'example' => 45],
                                        'title' => ['type' => 'string', 'example' => 'দ্বিঘাত সমীকরণ সাজেশন ও উত্তরমালা'],
                                        'file_url' => ['type' => 'string', 'example' => 'https://example.com/notes/math-qna.pdf'],
                                        'file_type' => ['type' => 'string', 'example' => 'pdf'],
                                        'page_count' => ['type' => 'integer', 'example' => 12],
                                        'is_free' => ['type' => 'boolean', 'example' => true],
                                        'is_active' => ['type' => 'boolean', 'example' => true],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Note created'],
                    ],
                ],
            ],
            '/api/admin/notes/{id}' => [
                'get' => [
                    'tags' => ['Admin - Educational Content'],
                    'summary' => 'Get single study note details',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'responses' => [
                        '200' => ['description' => 'Note details with chapter and teacher'],
                    ],
                ],
                'put' => [
                    'tags' => ['Admin - Educational Content'],
                    'summary' => 'Update study note details or replace file',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'requestBody' => [
                        'required' => false,
                        'content' => ['multipart/form-data' => ['schema' => ['$ref' => '#/components/schemas/Note']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Note updated successfully'],
                    ],
                ],
                'delete' => [
                    'tags' => ['Admin - Educational Content'],
                    'summary' => 'Delete a study note',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'responses' => [
                        '200' => ['description' => 'Note deleted successfully'],
                    ],
                ],
            ],
        ];
    }

    // 10. Admin - Tests & Assessments
    protected function getAdminAssessmentPaths(): array
    {
        return [
            '/api/admin/tests' => [
                'get' => [
                    'tags' => ['Admin - Tests & Assessments'],
                    'summary' => 'List tests with class and subject filters',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'class_level', 'in' => 'query', 'schema' => ['type' => 'integer', 'example' => 10]],
                        ['name' => 'subject_id', 'in' => 'query', 'schema' => ['type' => 'integer']],
                        ['name' => 'search', 'in' => 'query', 'schema' => ['type' => 'string']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Paginated list of tests'],
                    ],
                ],
                'post' => [
                    'tags' => ['Admin - Tests & Assessments'],
                    'summary' => 'Create a practice test',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['chapter_id', 'title'],
                                    'properties' => [
                                        'chapter_id' => ['type' => 'integer', 'example' => 45],
                                        'title' => ['type' => 'string', 'example' => 'দ্বিঘাত সমীকরণ প্র্যাকটিস টেস্ট'],
                                        'duration_minutes' => ['type' => 'integer', 'example' => 20],
                                        'total_marks' => ['type' => 'integer', 'example' => 10],
                                        'passing_marks' => ['type' => 'integer', 'example' => 4],
                                        'is_free' => ['type' => 'boolean', 'example' => true],
                                        'is_active' => ['type' => 'boolean', 'example' => true],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Test created'],
                    ],
                ],
            ],
            '/api/admin/tests/{id}' => [
                'get' => [
                    'tags' => ['Admin - Tests & Assessments'],
                    'summary' => 'Get single test details with ordered questions',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'responses' => [
                        '200' => ['description' => 'Test details with questions'],
                    ],
                ],
                'put' => [
                    'tags' => ['Admin - Tests & Assessments'],
                    'summary' => 'Update test parameters (duration, pass %, status)',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Test']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Test updated successfully'],
                    ],
                ],
                'delete' => [
                    'tags' => ['Admin - Tests & Assessments'],
                    'summary' => 'Delete a practice test',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'responses' => [
                        '200' => ['description' => 'Test deleted successfully'],
                    ],
                ],
            ],
            '/api/admin/tests/{id}/questions' => [
                'post' => [
                    'tags' => ['Admin - Tests & Assessments'],
                    'summary' => 'Bulk insert MCQ questions for a test',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 401]]],
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
                                                'required' => ['question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_option'],
                                                'properties' => [
                                                    'question_text' => ['type' => 'string', 'example' => '2x^2 + kx + 3 = 0 সমীকরণের দুটি বীজ বাস্তব ও সমান হলে k এর মান কত?'],
                                                    'option_a' => ['type' => 'string', 'example' => '±√24'],
                                                    'option_b' => ['type' => 'string', 'example' => '±2√6'],
                                                    'option_c' => ['type' => 'string', 'example' => '±6'],
                                                    'option_d' => ['type' => 'string', 'example' => '±4'],
                                                    'correct_option' => ['type' => 'string', 'enum' => ['A', 'B', 'C', 'D'], 'example' => 'B'],
                                                    'marks' => ['type' => 'integer', 'example' => 1],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Questions added to test'],
                    ],
                ],
            ],
            '/api/admin/questions/{id}' => [
                'put' => [
                    'tags' => ['Admin - Tests & Assessments'],
                    'summary' => 'Update a test question and options',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Question']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Question updated and test total marks recalculated'],
                    ],
                ],
                'delete' => [
                    'tags' => ['Admin - Tests & Assessments'],
                    'summary' => 'Delete a single question from test',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'responses' => [
                        '200' => ['description' => 'Question deleted and total marks recalculated'],
                    ],
                ],
            ],
        ];
    }

    // 11. Admin - Users & Analytics
    protected function getAdminUserAndAnalyticsPaths(): array
    {
        return [
            '/api/admin/admins' => [
                'get' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'List all admin users',
                    'security' => [['BearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Paginated list of admin users'],
                    ],
                ],
                'post' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'Create a new admin account',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['name', 'email', 'password'],
                                    'properties' => [
                                        'name' => ['type' => 'string', 'example' => 'Admin User'],
                                        'email' => ['type' => 'string', 'format' => 'email', 'example' => 'admin2@vidyamission.com'],
                                        'password' => ['type' => 'string', 'format' => 'password', 'example' => 'AdminPass123!'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Admin user created successfully'],
                    ],
                ],
            ],
            '/api/admin/admins/{id}' => [
                'put' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'Update admin account details or active status',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'name' => ['type' => 'string'],
                                        'email' => ['type' => 'string'],
                                        'is_active' => ['type' => 'boolean'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Admin updated'],
                    ],
                ],
                'delete' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'Deactivate an admin account',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 2]]],
                    'responses' => [
                        '200' => ['description' => 'Admin deactivated'],
                        '400' => ['description' => 'Cannot deactivate the last active admin'],
                    ],
                ],
            ],
            '/api/admin/students' => [
                'get' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'List students with subscription status and search filters',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'search', 'in' => 'query', 'schema' => ['type' => 'string'], 'description' => 'Search name, email, mobile, school, district'],
                        ['name' => 'status', 'in' => 'query', 'schema' => ['type' => 'string', 'enum' => ['active', 'expired', 'subscribed']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Paginated students list with studentProfile and active subscription'],
                    ],
                ],
                'post' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'Create a new student account from admin panel',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['name', 'mobile', 'password'],
                                    'properties' => [
                                        'name' => ['type' => 'string', 'example' => 'Rahul Sharma'],
                                        'mobile' => ['type' => 'string', 'example' => '9876543210'],
                                        'email' => ['type' => 'string', 'format' => 'email', 'example' => 'rahul@example.com'],
                                        'password' => ['type' => 'string', 'example' => 'Student@1234'],
                                        'class' => ['type' => 'integer', 'example' => 10],
                                        'school_name' => ['type' => 'string', 'example' => 'Kolkata High School'],
                                        'district' => ['type' => 'string', 'example' => 'Kolkata'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Student account created successfully'],
                    ],
                ],
            ],
            '/api/admin/students/{id}/password' => [
                'post' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'Set or reset a student password from admin panel',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 5]]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['password'],
                                    'properties' => [
                                        'password' => ['type' => 'string', 'minLength' => 6, 'example' => 'NewSecret@2026'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Student password updated successfully'],
                    ],
                ],
            ],
            '/api/admin/students/{id}' => [
                'put' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'Update student account (e.g. deactivate or activate)',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 5]]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'is_active' => ['type' => 'boolean', 'example' => true],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Student updated successfully'],
                    ],
                ],
            ],
            '/api/admin/students/{id}/grant-access' => [
                'post' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'Grant complimentary subscription or trial extension to a student',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 5]]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['access_type', 'duration_days'],
                                    'properties' => [
                                        'access_type' => ['type' => 'string', 'enum' => ['subscription', 'trial'], 'example' => 'subscription'],
                                        'duration_days' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 3650, 'example' => 30],
                                        'subscription_id' => ['type' => 'integer', 'nullable' => true, 'example' => 1],
                                        'reason' => ['type' => 'string', 'example' => 'Merit scholarship recipient'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Complimentary access granted successfully'],
                    ],
                ],
            ],
            '/api/admin/teachers' => [
                'get' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'List teachers with assigned subjects and class levels',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'class_level', 'in' => 'query', 'schema' => ['type' => 'integer', 'example' => 10]],
                        ['name' => 'subject_id', 'in' => 'query', 'schema' => ['type' => 'integer', 'example' => 1]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Paginated teachers list'],
                    ],
                ],
                'post' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'Create a teacher account',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['name', 'email', 'mobile', 'password'],
                                    'properties' => [
                                        'name' => ['type' => 'string', 'example' => 'Prof. Ananya Sen'],
                                        'email' => ['type' => 'string', 'format' => 'email', 'example' => 'ananya@vidyamission.com'],
                                        'mobile' => ['type' => 'string', 'example' => '9830098300'],
                                        'password' => ['type' => 'string', 'format' => 'password', 'example' => 'TeacherPass123!'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Teacher account created'],
                    ],
                ],
            ],
            '/api/admin/teachers/{id}' => [
                'put' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'Update teacher profile details or password',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 2]]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'name' => ['type' => 'string'],
                                        'email' => ['type' => 'string'],
                                        'mobile' => ['type' => 'string'],
                                        'is_active' => ['type' => 'boolean'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Teacher updated'],
                    ],
                ],
                'delete' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'Deactivate a teacher account',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 2]]],
                    'responses' => [
                        '200' => ['description' => 'Teacher deactivated'],
                    ],
                ],
            ],
            '/api/admin/teachers/{id}/assign-subjects' => [
                'post' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'Assign curriculum subjects and grade levels to a teacher',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 2]]],
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
                                                'required' => ['subject_id', 'class_level'],
                                                'properties' => [
                                                    'subject_id' => ['type' => 'integer', 'example' => 3],
                                                    'class_level' => ['type' => 'integer', 'example' => 10],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Subjects assigned successfully'],
                    ],
                ],
            ],
            '/api/admin/users/{id}/revoke-session' => [
                'post' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'Revoke a user active device session',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'responses' => [
                        '200' => ['description' => 'Active session revoked successfully'],
                    ],
                ],
            ],
            '/api/admin/subscription-plans' => [
                'get' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'List all subscription plans for admin configuration',
                    'security' => [['BearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'All subscription plans ordered by price'],
                    ],
                ],
                'post' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'Create a new subscription plan',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['name', 'price', 'duration_days'],
                                    'properties' => [
                                        'name' => ['type' => 'string', 'example' => 'Madhyamik 2027 Full Pass'],
                                        'price' => ['type' => 'number', 'example' => 999.00],
                                        'duration_days' => ['type' => 'integer', 'example' => 365],
                                        'description' => ['type' => 'string', 'example' => 'Full access to Class 10 video lessons, live classes, notes, and tests'],
                                        'features' => ['type' => 'array', 'items' => ['type' => 'string']],
                                        'is_active' => ['type' => 'boolean', 'example' => true],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Subscription plan created'],
                    ],
                ],
            ],
            '/api/admin/subscription-plans/{id}' => [
                'put' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'Update a subscription plan',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/SubscriptionPlan']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Plan updated'],
                    ],
                ],
                'delete' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'Delete a subscription plan',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'responses' => [
                        '200' => ['description' => 'Plan deleted'],
                    ],
                ],
            ],
            '/api/admin/user-subscriptions' => [
                'get' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'List student subscription purchases and active orders',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'status', 'in' => 'query', 'schema' => ['type' => 'string', 'enum' => ['completed', 'pending', 'failed']]],
                        ['name' => 'search', 'in' => 'query', 'schema' => ['type' => 'string'], 'description' => 'Search student email'],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Paginated user subscriptions list'],
                    ],
                ],
            ],
            '/api/admin/analytics/overview' => [
                'get' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'Get platform analytics overview',
                    'security' => [['BearerAuth' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'Overview statistics',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'data' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'total_students' => ['type' => 'integer', 'example' => 1250],
                                                    'total_chapters' => ['type' => 'integer', 'example' => 88],
                                                    'total_videos' => ['type' => 'integer', 'example' => 101],
                                                    'total_notes' => ['type' => 'integer', 'example' => 101],
                                                    'total_tests' => ['type' => 'integer', 'example' => 101],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '/api/admin/analytics/popular-subjects' => [
                'get' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'Get most popular subjects ranked by chapter completion',
                    'security' => [['BearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Subjects ordered by completed chapters count'],
                    ],
                ],
            ],
            '/api/admin/analytics/test-performance' => [
                'get' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'Get test attempt statistics and pass rate benchmarks',
                    'security' => [['BearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Paginated test performance metrics with pass rate %'],
                    ],
                ],
            ],
            '/api/admin/notifications/broadcast' => [
                'post' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'Broadcast push notification to all students or targeted user IDs',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['title', 'body', 'user_ids'],
                                    'properties' => [
                                        'title' => ['type' => 'string', 'example' => 'Special Live Class Tonight at 7 PM!'],
                                        'body' => ['type' => 'string', 'example' => 'Madhyamik Mathematics special problem solving live class will start at 7:00 PM.'],
                                        'user_ids' => [
                                            'oneOf' => [
                                                ['type' => 'string', 'enum' => ['all'], 'example' => 'all'],
                                                ['type' => 'array', 'items' => ['type' => 'integer'], 'example' => [1, 2, 3]],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Notification broadcast dispatched'],
                    ],
                ],
            ],
        ];
    }

    // 12. Admin - Payments & Webhooks
    protected function getAdminPaymentPaths(): array
    {
        return [
            '/api/admin/payments/dashboard' => [
                'get' => [
                    'tags' => ['Admin - Payments & Webhooks'],
                    'summary' => 'Get admin payments dashboard KPIs',
                    'security' => [['BearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Revenue aggregates, captured counts, and refund totals'],
                    ],
                ],
            ],
            '/api/admin/payments' => [
                'get' => [
                    'tags' => ['Admin - Payments & Webhooks'],
                    'summary' => 'Search and filter all payment transactions with date range and status',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'status', 'in' => 'query', 'schema' => ['type' => 'string', 'enum' => ['captured', 'pending', 'failed', 'refunded', 'partially_refunded']]],
                        ['name' => 'purpose', 'in' => 'query', 'schema' => ['type' => 'string']],
                        ['name' => 'search', 'in' => 'query', 'schema' => ['type' => 'string']],
                        ['name' => 'from_date', 'in' => 'query', 'schema' => ['type' => 'string', 'format' => 'date']],
                        ['name' => 'to_date', 'in' => 'query', 'schema' => ['type' => 'string', 'format' => 'date']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Paginated payments list with user and refund associations'],
                    ],
                ],
            ],
            '/api/admin/payments/webhook-events' => [
                'get' => [
                    'tags' => ['Admin - Payments & Webhooks'],
                    'summary' => 'Audit log of incoming Razorpay webhook events',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'status', 'in' => 'query', 'schema' => ['type' => 'string']],
                        ['name' => 'event_type', 'in' => 'query', 'schema' => ['type' => 'string']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Paginated webhook event log'],
                    ],
                ],
            ],
            '/api/admin/payments/{id}' => [
                'get' => [
                    'tags' => ['Admin - Payments & Webhooks'],
                    'summary' => 'Get detailed payment transaction with audit trail and related webhook events',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'responses' => [
                        '200' => ['description' => 'Payment detail with refunds, payable, and webhook events'],
                    ],
                ],
            ],
            '/api/admin/payments/{id}/refund' => [
                'post' => [
                    'tags' => ['Admin - Payments & Webhooks'],
                    'summary' => 'Issue a full or partial refund for a captured payment',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['amount'],
                                    'properties' => [
                                        'amount' => ['type' => 'number', 'example' => 499.00],
                                        'reason' => ['type' => 'string', 'example' => 'Customer requested refund'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Refund processed via Razorpay'],
                    ],
                ],
            ],
            '/api/admin/payments/{id}/reconcile' => [
                'post' => [
                    'tags' => ['Admin - Payments & Webhooks'],
                    'summary' => 'Reconcile payment status directly against Razorpay API',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 1]]],
                    'responses' => [
                        '200' => ['description' => 'Payment status reconciled with gateway'],
                        '400' => ['description' => 'Reconciliation error'],
                    ],
                ],
            ],
        ];
    }
}
