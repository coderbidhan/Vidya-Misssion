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
                ['name' => 'Student - Profile & Subscriptions', 'description' => 'Student profile setup, subscription plans, and Razorpay payment orders'],
                ['name' => 'Admin - Curriculum Management', 'description' => 'Class 1–10 subject mapping, subject master, and chapter management'],
                ['name' => 'Admin - Educational Content', 'description' => 'Management of recorded videos, live classes, and study notes with class/subject filters'],
                ['name' => 'Admin - Tests & Assessments', 'description' => 'Management of chapter tests and bulk MCQ question banks'],
                ['name' => 'Admin - Users & Analytics', 'description' => 'Student/teacher management, subscription plans, and learning analytics'],
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
                    'name' => ['type' => 'string', 'example' => 'একচল বিশিষ্ট দ্বিঘাত সমীকরণ (Quadratic Equations in One Variable)'],
                    'description' => ['type' => 'string', 'example' => 'Concept of quadratic equations, roots, factorization and Sridhar Acharya formula.'],
                    'subject' => ['$ref' => '#/components/schemas/Subject'],
                ],
            ],
            'RecordedClass' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'example' => 101],
                    'chapter_id' => ['type' => 'integer', 'example' => 45],
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
                    'chapter_id' => ['type' => 'integer', 'example' => 45],
                    'teacher_id' => ['type' => 'integer', 'nullable' => true, 'example' => 2],
                    'title' => ['type' => 'string', 'example' => 'লাইভ ডাউট ক্লিয়ারিং: দ্বিঘাত সমীকরণ'],
                    'description' => ['type' => 'string', 'example' => 'Interactive live session with expert math faculty.'],
                    'scheduled_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2026-10-20T17:00:00Z'],
                    'duration_minutes' => ['type' => 'integer', 'example' => 60],
                    'meeting_link' => ['type' => 'string', 'example' => 'https://meet.google.com/vid-math-live'],
                    'status' => ['type' => 'string', 'enum' => ['scheduled', 'live', 'completed', 'cancelled'], 'example' => 'scheduled'],
                    'is_free' => ['type' => 'boolean', 'example' => false],
                ],
            ],
            'Note' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'example' => 301],
                    'chapter_id' => ['type' => 'integer', 'example' => 45],
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
                    'question_type' => ['type' => 'string', 'enum' => ['mcq', 'true_false'], 'example' => 'mcq'],
                    'options' => [
                        'type' => 'array',
                        'items' => ['type' => 'string'],
                        'example' => ['b^2 - 4ac > 0', 'b^2 - 4ac = 0', 'b^2 - 4ac < 0', 'b^2 + 4ac = 0'],
                    ],
                    'correct_answer' => ['type' => 'string', 'example' => 'b^2 - 4ac = 0'],
                    'marks' => ['type' => 'integer', 'example' => 1],
                    'explanation' => ['type' => 'string', 'example' => 'নিরূপক D = b^2 - 4ac = 0 হলে দ্বিঘাত সমীকরণের বীজ দুটি বাস্তব ও সমান হয়।'],
                ],
            ],
        ];
    }

    protected function getPaths(): array
    {
        return [
            // ==========================================
            // 1. Academic Classes & Curriculum
            // ==========================================
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
                        [
                            'name' => 'classLevel',
                            'in' => 'path',
                            'required' => true,
                            'schema' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 12, 'example' => 10],
                            'description' => 'Class level number (1 through 10)',
                        ],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Subjects and chapters mapped to the class',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'data' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'class_level' => ['type' => 'integer', 'example' => 10],
                                                    'name' => ['type' => 'string', 'example' => 'Class 10'],
                                                    'stage' => ['type' => 'string', 'example' => 'Secondary (Madhyamik)'],
                                                    'subjects' => [
                                                        'type' => 'array',
                                                        'items' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'id' => ['type' => 'integer', 'example' => 3],
                                                                'name' => ['type' => 'string', 'example' => 'গণিত (Mathematics)'],
                                                                'code' => ['type' => 'string', 'example' => 'MATH'],
                                                                'chapters_count' => ['type' => 'integer', 'example' => 26],
                                                                'chapters' => [
                                                                    'type' => 'array',
                                                                    'items' => ['$ref' => '#/components/schemas/Chapter'],
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
                        ],
                        '400' => ['$ref' => '#/components/responses/400BadRequest'],
                    ],
                ],
            ],
            '/api/subjects' => [
                'get' => [
                    'tags' => ['Academic Classes & Curriculum'],
                    'summary' => 'List all active subjects',
                    'responses' => [
                        '200' => [
                            'description' => 'List of subjects',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'data' => [
                                                'type' => 'array',
                                                'items' => ['$ref' => '#/components/schemas/Subject'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '/api/subjects/{id}/chapters' => [
                'get' => [
                    'tags' => ['Academic Classes & Curriculum'],
                    'summary' => 'List chapters for a subject with optional class_level filter',
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'required' => true,
                            'schema' => ['type' => 'integer', 'example' => 3],
                            'description' => 'Subject ID',
                        ],
                        [
                            'name' => 'class_level',
                            'in' => 'query',
                            'required' => false,
                            'schema' => ['type' => 'integer', 'example' => 10],
                            'description' => 'Filter by class grade (1 through 10)',
                        ],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Chapters list',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'data' => [
                                                'type' => 'array',
                                                'items' => ['$ref' => '#/components/schemas/Chapter'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 2. Authentication
            // ==========================================
            '/api/auth/register' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'Register a new student',
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['name', 'phone', 'password', 'class_level'],
                                    'properties' => [
                                        'name' => ['type' => 'string', 'example' => 'Sourav Mukherjee'],
                                        'phone' => ['type' => 'string', 'example' => '9876543210'],
                                        'email' => ['type' => 'string', 'format' => 'email', 'example' => 'sourav@example.com'],
                                        'password' => ['type' => 'string', 'format' => 'password', 'example' => 'secret123'],
                                        'class_level' => ['type' => 'integer', 'enum' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], 'example' => 10],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => [
                            'description' => 'Student registered successfully, returns token',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'token' => ['type' => 'string', 'example' => '1|laravel_sanctum_token_string...'],
                                            'user' => ['type' => 'object'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        '422' => ['$ref' => '#/components/responses/422Unprocessable'],
                    ],
                ],
            ],
            '/api/auth/login' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'Log in with phone or email and password',
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['login', 'password'],
                                    'properties' => [
                                        'login' => ['type' => 'string', 'example' => '9876543210', 'description' => 'Email address or 10-digit mobile number'],
                                        'password' => ['type' => 'string', 'format' => 'password', 'example' => 'secret123'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Login successful',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'token' => ['type' => 'string', 'example' => '1|laravel_sanctum_token_string...'],
                                            'user' => ['type' => 'object'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        '401' => ['$ref' => '#/components/responses/401Unauthorized'],
                    ],
                ],
            ],
            '/api/auth/send-otp' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'Send OTP to mobile phone',
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
                        '200' => [
                            'description' => 'OTP dispatched successfully',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'message' => ['type' => 'string', 'example' => 'OTP sent to mobile phone.'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
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
                        '200' => [
                            'description' => 'OTP verified, returns Sanctum token',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'token' => ['type' => 'string', 'example' => '2|sanctum_bearer_token...'],
                                            'user' => ['type' => 'object'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        '400' => ['$ref' => '#/components/responses/400BadRequest'],
                    ],
                ],
            ],
            '/api/auth/logout' => [
                'post' => [
                    'tags' => ['Authentication'],
                    'summary' => 'Log out current session and invalidate bearer token',
                    'security' => [['BearerAuth' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'Logged out successfully',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'message' => ['type' => 'string', 'example' => 'Successfully logged out.'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        '401' => ['$ref' => '#/components/responses/401Unauthorized'],
                    ],
                ],
            ],

            // ==========================================
            // 3. Student - Academic Content
            // ==========================================
            '/api/chapters/{id}' => [
                'get' => [
                    'tags' => ['Student - Academic Content'],
                    'summary' => 'Get chapter details by ID',
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
                            'description' => 'List of live classes',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'data' => [
                                                'type' => 'array',
                                                'items' => ['$ref' => '#/components/schemas/LiveClass'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 4. Student - Assessments & Tests
            // ==========================================
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
                    'summary' => 'Start test and retrieve questions (without correct answers)',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 401]],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Test with questions array ready for student attempt',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'data' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'test' => ['$ref' => '#/components/schemas/Test'],
                                                    'questions' => [
                                                        'type' => 'array',
                                                        'items' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'id' => ['type' => 'integer', 'example' => 501],
                                                                'question_text' => ['type' => 'string'],
                                                                'question_type' => ['type' => 'string'],
                                                                'options' => ['type' => 'array', 'items' => ['type' => 'string']],
                                                                'marks' => ['type' => 'integer'],
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
                    ],
                ],
            ],
            '/api/tests/{id}/attempt' => [
                'post' => [
                    'tags' => ['Student - Assessments & Tests'],
                    'summary' => 'Submit test answers and receive score, percentage, and answer breakdown',
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
                                                    'selected_answer' => ['type' => 'string', 'example' => 'b^2 - 4ac = 0'],
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

            // ==========================================
            // 5. Admin - Educational Content (Filtered)
            // ==========================================
            '/api/admin/recorded-classes' => [
                'get' => [
                    'tags' => ['Admin - Educational Content'],
                    'summary' => 'List recorded classes with Class 1–10, Subject, and Status filtering',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'class_level', 'in' => 'query', 'schema' => ['type' => 'integer', 'example' => 10], 'description' => 'Filter by class grade (1-10)'],
                        ['name' => 'subject_id', 'in' => 'query', 'schema' => ['type' => 'integer', 'example' => 3], 'description' => 'Filter by subject ID'],
                        ['name' => 'chapter_id', 'in' => 'query', 'schema' => ['type' => 'integer'], 'description' => 'Filter by chapter ID'],
                        ['name' => 'is_active', 'in' => 'query', 'schema' => ['type' => 'string', 'enum' => ['0', '1', 'all']], 'description' => 'Filter active status'],
                        ['name' => 'search', 'in' => 'query', 'schema' => ['type' => 'string', 'example' => 'দ্বিঘাত'], 'description' => 'Search by title or description'],
                        ['name' => 'per_page', 'in' => 'query', 'schema' => ['type' => 'integer', 'example' => 15], 'description' => 'Pagination per page'],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Paginated list of recorded classes',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'data' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/RecordedClass']],
                                            'current_page' => ['type' => 'integer', 'example' => 1],
                                            'total' => ['type' => 'integer', 'example' => 101],
                                        ],
                                    ],
                                ],
                            ],
                        ],
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
                        '201' => [
                            'description' => 'Recorded class created',
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
                        '200' => [
                            'description' => 'Recorded class found',
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
                        '200' => [
                            'description' => 'Updated successfully',
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
                'delete' => [
                    'tags' => ['Admin - Educational Content'],
                    'summary' => 'Delete a recorded class',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer', 'example' => 101]]],
                    'responses' => [
                        '200' => [
                            'description' => 'Deleted successfully',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'message' => ['type' => 'string', 'example' => 'Recorded class deleted.'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 6. Admin - Notes
            // ==========================================
            '/api/admin/notes' => [
                'get' => [
                    'tags' => ['Admin - Educational Content'],
                    'summary' => 'List study notes with filters (class_level, subject_id, status, search)',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'class_level', 'in' => 'query', 'schema' => ['type' => 'integer', 'example' => 10]],
                        ['name' => 'subject_id', 'in' => 'query', 'schema' => ['type' => 'integer']],
                        ['name' => 'chapter_id', 'in' => 'query', 'schema' => ['type' => 'integer']],
                        ['name' => 'is_active', 'in' => 'query', 'schema' => ['type' => 'string']],
                        ['name' => 'search', 'in' => 'query', 'schema' => ['type' => 'string']],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Paginated list of study notes',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'data' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Note']],
                                        ],
                                    ],
                                ],
                            ],
                        ],
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
                        '201' => [
                            'description' => 'Note created',
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

            // ==========================================
            // 7. Admin - Tests & Questions
            // ==========================================
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
                        '200' => [
                            'description' => 'Paginated list of tests',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'data' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Test']],
                                        ],
                                    ],
                                ],
                            ],
                        ],
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
                        '201' => [
                            'description' => 'Test created',
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
                                                'required' => ['question_text', 'question_type', 'options', 'correct_answer', 'marks'],
                                                'properties' => [
                                                    'question_text' => ['type' => 'string', 'example' => '2x^2 + kx + 3 = 0 সমীকরণের দুটি বীজ বাস্তব ও সমান হলে k এর মান কত?'],
                                                    'question_type' => ['type' => 'string', 'enum' => ['mcq', 'true_false'], 'example' => 'mcq'],
                                                    'options' => ['type' => 'array', 'items' => ['type' => 'string'], 'example' => ['±√24', '±2√6', '±6', '±4']],
                                                    'correct_answer' => ['type' => 'string', 'example' => '±2√6'],
                                                    'marks' => ['type' => 'integer', 'example' => 1],
                                                    'explanation' => ['type' => 'string', 'example' => 'D = k^2 - 4(2)(3) = 0 => k^2 = 24 => k = ±2√6.'],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '201' => [
                            'description' => 'Questions added to test',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'success' => ['type' => 'boolean', 'example' => true],
                                            'count' => ['type' => 'integer', 'example' => 5],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 8. Admin - Analytics
            // ==========================================
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

            // ==========================================
            // 9. Student - Subscriptions & Payments
            // ==========================================
            '/api/subscriptions' => [
                'get' => [
                    'tags' => ['Student - Profile & Subscriptions'],
                    'summary' => 'List active subscription plans',
                    'description' => 'Returns all currently available subscription plans with pricing, duration in days, and included features.',
                    'responses' => [
                        '200' => ['description' => 'Plans retrieved successfully'],
                    ],
                ],
            ],
            '/api/subscriptions/current' => [
                'get' => [
                    'tags' => ['Student - Profile & Subscriptions'],
                    'summary' => 'Get current active subscription',
                    'security' => [['BearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Current subscription status retrieved'],
                    ],
                ],
            ],
            '/api/subscriptions/{id}/initiate' => [
                'post' => [
                    'tags' => ['Student - Profile & Subscriptions'],
                    'summary' => 'Initiate Razorpay order for plan purchase',
                    'security' => [['BearerAuth' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Razorpay order created with order_id and key_id'],
                    ],
                ],
            ],
            '/api/subscriptions/verify' => [
                'post' => [
                    'tags' => ['Student - Profile & Subscriptions'],
                    'summary' => 'Verify Razorpay payment signature and activate subscription',
                    'security' => [['BearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature'],
                                    'properties' => [
                                        'razorpay_order_id' => ['type' => 'string'],
                                        'razorpay_payment_id' => ['type' => 'string'],
                                        'razorpay_signature' => ['type' => 'string'],
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
            '/api/subscriptions/webhook' => [
                'post' => [
                    'tags' => ['Student - Profile & Subscriptions'],
                    'summary' => 'Razorpay Webhook endpoint (HMAC signature protected)',
                    'description' => 'Receives asynchronous events such as payment.captured, payment.failed, and refund.processed from Razorpay.',
                    'responses' => [
                        '200' => ['description' => 'Webhook received and processed idempotently'],
                    ],
                ],
            ],
            '/api/admin/payments/dashboard' => [
                'get' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'Get admin payments dashboard KPIs',
                    'security' => [['BearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Revenue aggregates, captured counts, and refund totals'],
                    ],
                ],
            ],
            '/api/admin/payments/{id}/refund' => [
                'post' => [
                    'tags' => ['Admin - Users & Analytics'],
                    'summary' => 'Issue a full or partial refund for a captured payment',
                    'security' => [['BearerAuth' => []]],
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
        ];
    }
}
