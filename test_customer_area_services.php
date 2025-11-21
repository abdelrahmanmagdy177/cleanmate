<?php

use App\Models\Service;
use App\Models\Area;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ServiceController;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Verifying Customer Area Service Filtering...\n\n";

// 1. Setup: Ensure we have areas and services
$area = Area::first();
if (!$area) {
    echo "Error: No areas found. Please seed the database.\n";
    exit(1);
}
echo "Using Area: {$area->name} (ID: {$area->id})\n";

$service = Service::first();
if (!$service) {
    echo "Error: No services found. Please seed the database.\n";
    exit(1);
}

// Ensure service is linked to area
if (!$service->areas()->where('areas.id', $area->id)->exists()) {
    $service->areas()->attach($area->id);
}

// Create exclusive service NOT in this area
$exclusiveService = Service::create([
    'name' => 'Exclusive Service',
    'description' => 'Only for another area',
    'active' => true
]);
echo "Created 'Exclusive Service' (ID: {$exclusiveService->id}) - Not linked to Area {$area->id}\n";

// 2. Create a customer in this area
$customer = Customer::create([
    'name' => 'Test Customer',
    'email' => 'test' . time() . '@example.com',
    'phone' => '1234567890',
    'area_id' => $area->id,
    'password' => bcrypt('password')
]);
echo "Created Customer (ID: {$customer->id}) in Area {$area->id}\n";

// 3. Simulate Request with Authenticated User
$controller = new ServiceController();
$request = Request::create('/api/services', 'GET');

// Mock the user on the request
$request->setUserResolver(function () use ($customer) {
    return $customer;
});

echo "\nCalling ServiceController::index with authenticated customer...\n";
$response = $controller->index($request);
$data = $response->getData();

// 4. Verify Results
$foundOriginal = false;
$foundExclusive = false;

foreach ($data as $s) {
    if ($s->id == $service->id) $foundOriginal = true;
    if ($s->id == $exclusiveService->id) $foundExclusive = true;
}

echo "- Original Service found? " . ($foundOriginal ? "PASS" : "FAIL") . "\n";
echo "- Exclusive Service found? " . (!$foundExclusive ? "PASS" : "FAIL (Should not be found)") . "\n";

// Cleanup
$exclusiveService->delete();
$customer->delete();
echo "\nTest Complete.\n";
