<?php

use App\Models\Service;
use App\Models\Area;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\Customer\ProfileController;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Verifying Customer Area Update & Service Filtering...\n\n";

// 1. Setup: Ensure we have areas and services
$area1 = Area::first();
if (!$area1) {
    echo "Error: No areas found. Please seed the database.\n";
    exit(1);
}
$area2 = Area::where('id', '!=', $area1->id)->first();
if (!$area2) {
    echo "Error: Need at least 2 areas for this test.\n";
    exit(1);
}

echo "Area 1: {$area1->name} (ID: {$area1->id})\n";
echo "Area 2: {$area2->name} (ID: {$area2->id})\n";

// Create services exclusive to each area
$service1 = Service::create(['name' => 'Service Area 1', 'description' => 'Only Area 1', 'active' => true]);
$service1->areas()->sync([$area1->id]);

$service2 = Service::create(['name' => 'Service Area 2', 'description' => 'Only Area 2', 'active' => true]);
$service2->areas()->sync([$area2->id]);

// 2. Create a customer
$customer = Customer::create([
    'name' => 'Test Customer',
    'email' => 'test' . time() . '@example.com',
    'phone' => '1234567890',
    'password' => bcrypt('password')
]);
echo "Created Customer (ID: {$customer->id}) - No Area initially\n";

// 3. Test: Update Customer Area to Area 1
$profileController = new ProfileController();
$updateRequest = Request::create('/api/customer/area', 'POST', ['area_id' => $area1->id]);
$updateRequest->setUserResolver(function () use ($customer) { return $customer; });

echo "\nUpdating customer area to Area 1...\n";
$profileController->updateArea($updateRequest);
$customer->refresh();
echo "Customer Area ID: " . ($customer->area_id == $area1->id ? "PASS" : "FAIL") . "\n";

// 4. Test: Fetch Services (Should see Service 1, NOT Service 2)
$serviceController = new ServiceController();
$serviceRequest = Request::create('/api/services', 'GET');
$serviceRequest->setUserResolver(function () use ($customer) { return $customer; });

echo "\nFetching services for Area 1...\n";
$response = $serviceController->index($serviceRequest);
$data = $response->getData();

$hasService1 = false;
$hasService2 = false;
foreach ($data as $s) {
    if ($s->id == $service1->id) $hasService1 = true;
    if ($s->id == $service2->id) $hasService2 = true;
}
echo "- Has Service 1? " . ($hasService1 ? "PASS" : "FAIL") . "\n";
echo "- Has Service 2? " . (!$hasService2 ? "PASS" : "FAIL") . "\n";

// 5. Test: Update Customer Area to Area 2
$updateRequest2 = Request::create('/api/customer/area', 'POST', ['area_id' => $area2->id]);
$updateRequest2->setUserResolver(function () use ($customer) { return $customer; });

echo "\nUpdating customer area to Area 2...\n";
$profileController->updateArea($updateRequest2);
$customer->refresh();
echo "Customer Area ID: " . ($customer->area_id == $area2->id ? "PASS" : "FAIL") . "\n";

// 6. Test: Fetch Services (Should see Service 2, NOT Service 1)
echo "\nFetching services for Area 2...\n";
$response2 = $serviceController->index($serviceRequest);
$data2 = $response2->getData();

$hasService1 = false;
$hasService2 = false;
foreach ($data2 as $s) {
    if ($s->id == $service1->id) $hasService1 = true;
    if ($s->id == $service2->id) $hasService2 = true;
}
echo "- Has Service 1? " . (!$hasService1 ? "PASS" : "FAIL") . "\n";
echo "- Has Service 2? " . ($hasService2 ? "PASS" : "FAIL") . "\n";

// Cleanup
$service1->delete();
$service2->delete();
$customer->delete();
echo "\nTest Complete.\n";
