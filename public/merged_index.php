<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 🔥 Handle Raw POST from Face Device BEFORE Laravel boots
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/') {
    file_put_contents(__DIR__.'/face_device_direct.log', date('Y-m-d H:i:s') . " - DEVICE HIT\n", FILE_APPEND);
    file_put_contents(__DIR__.'/face_device_direct.log', "HEADERS:\n" . print_r(getallheaders(), true), FILE_APPEND);
    file_put_contents(__DIR__.'/face_device_direct.log', "BODY:\n" . file_get_contents('php://input') . "\n\n", FILE_APPEND);

    // Respond directly to device, skip Laravel
    echo "OK DEVICE HANDLED";
    exit;
}

// Laravel Maintenance Mode
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Composer Autoloader
require __DIR__.'/../vendor/autoload.php';

// Laravel Bootstrapping
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
