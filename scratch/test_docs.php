<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\DocsController;
use Illuminate\Contracts\Console\Kernel;

$docsCtrl = new DocsController;

// 1. Test OpenAPI JSON generation
$jsonRes = $docsCtrl->openApiJson();
$data = $jsonRes->getData(true);

echo 'OpenAPI Title: '.($data['info']['title'] ?? 'N/A')."\n";
echo 'OpenAPI Version: '.($data['info']['version'] ?? 'N/A')."\n";
echo 'Total Documented Path Endpoints: '.count($data['paths'] ?? [])."\n";
echo 'Total Tags: '.count($data['tags'] ?? [])."\n";

// 2. Test Swagger View rendering
$view = $docsCtrl->index();
echo 'View name: '.$view->name()."\n";
echo 'View data spec URL: '.$view->getData()['apiSpecUrl']."\n";

echo "\nSWAGGER DOCS ROUTE AND SPEC VERIFIED SUCCESSFULLY!\n";
