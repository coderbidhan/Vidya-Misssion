<?php

namespace App\Http\Controllers;

use App\Services\OpenApiSpecService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ApiDocController extends Controller
{
    /**
     * Render the Swagger UI documentation interface.
     */
    public function index(): View
    {
        return view('docs.swagger');
    }

    /**
     * Return the complete OpenAPI 3.0.3 specification JSON.
     */
    public function json(OpenApiSpecService $specService): JsonResponse
    {
        return response()->json(
            $specService->build(),
            200,
            [
                'Content-Type' => 'application/json; charset=utf-8',
                'Access-Control-Allow-Origin' => '*',
            ],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }
}
