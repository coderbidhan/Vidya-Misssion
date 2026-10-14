<?php

use Illuminate\Support\Facades\Route;

test('docs page renders Swagger UI successfully', function () {
    $response = $this->get('/docs');

    $response->assertStatus(200);
    $response->assertSee('Vidya Mission — API Documentation');
    $response->assertSee('101 Live Classes');
    $response->assertSee('swagger-ui');
});

test('openapi json endpoint returns valid OpenAPI 3.0.3 specification with complete coverage', function () {
    $response = $this->get('/docs/openapi.json');

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/json; charset=utf-8');

    $data = $response->json();

    expect($data['openapi'])->toBe('3.0.3');
    expect($data['info']['title'])->toBe('Vidya Mission Educational Platform API');

    // Verify 12 core tags
    $tagNames = collect($data['tags'])->pluck('name')->toArray();
    expect($tagNames)->toContain(
        'Academic Classes & Curriculum',
        'Authentication',
        'Student - Academic Content',
        'Student - Assessments & Tests',
        'Student - Profile & Subscriptions',
        'Teacher - Portal',
        'Admin - Curriculum Management',
        'Admin - Live Classes Management',
        'Admin - Educational Content',
        'Admin - Tests & Assessments',
        'Admin - Users & Analytics',
        'Admin - Payments & Webhooks'
    );

    // Verify schemas
    $schemas = array_keys($data['components']['schemas']);
    expect($schemas)->toContain(
        'AcademicClass',
        'Subject',
        'Chapter',
        'RecordedClass',
        'LiveClass',
        'LiveClassDashboardStats',
        'Note',
        'Test',
        'Question',
        'StudentProfile',
        'Notification',
        'SubscriptionPlan',
        'PaymentRecord'
    );

    // Verify 100% coverage of registered API routes
    $specPaths = $data['paths'];
    $routes = Route::getRoutes();

    foreach ($routes as $route) {
        $uri = $route->uri();
        if (str_starts_with($uri, 'api/') && ! str_starts_with($uri, 'api/docs')) {
            $fullUri = '/'.$uri;
            expect(isset($specPaths[$fullUri]))->toBeTrue(
                "Route {$fullUri} is missing from OpenAPI paths specification."
            );

            foreach ($route->methods() as $method) {
                if ($method !== 'HEAD') {
                    $lowerMethod = strtolower($method);
                    expect(isset($specPaths[$fullUri][$lowerMethod]))->toBeTrue(
                        "HTTP method {$method} for {$fullUri} is missing from OpenAPI paths specification."
                    );
                }
            }
        }
    }
});

test('api docs openapi json route alias returns same valid json spec', function () {
    $response = $this->get('/api/docs/openapi.json');

    $response->assertStatus(200);
    $data = $response->json();
    expect($data['openapi'])->toBe('3.0.3');
});
