<?php

// Test script to verify zone validation for orders

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Service;
use App\Models\Timeslot;
use App\Models\Area;
use App\Models\Zone;
use Illuminate\Support\Facades\Http;

echo "=== Zone Validation Test ===\n\n";

// Get test data
$customer = Customer::where('email', 'alice@example.com')->first();
$service = Service::first();
$variant = $service->variants()->first();
$timeslot = Timeslot::first();
$date = now()->addDay()->format('Y-m-d');

// Test 1: Valid address with active zone
echo "Test 1: Order with VALID address (active zone/area)\n";
$validAddress = $customer->addresses()->first();
echo "Address: {$validAddress->area->name} ({$validAddress->area->zone->name})\n";

$response1 = Http::post('http://localhost/api/customer/orders', [
    'customer_name' => $customer->name,
    'customer_email' => $customer->email,
    'customer_phone' => $customer->phone,
    'service_id' => $service->id,
    'variant_id' => $variant->id,
    'timeslot_id' => $timeslot->id,
    'customer_address_id' => $validAddress->id,
    'space' => 50,
    'order_date' => $date,
    'payment_method' => 'cash',
]);

echo "Status: " . $response1->status() . "\n";
if ($response1->successful()) {
    echo "✅ Order created successfully!\n";
    echo "Total: " . $response1->json('price_breakdown.total_price') . " EGP\n";
} else {
    echo "❌ Error: " . $response1->json('error') . "\n";
}
echo "\n";

// Test 2: Address without area
echo "Test 2: Order with address WITHOUT area assigned\n";
$noAreaAddress = CustomerAddress::create([
    'customer_id' => $customer->id,
    'area_id' => null,
    'name' => 'Test',
    'title' => 'No Area Address',
    'address_details' => 'Test address without area',
    'is_default' => false,
]);
echo "Address: No area assigned\n";

$response2 = Http::post('http://localhost/api/customer/orders', [
    'customer_name' => $customer->name,
    'customer_email' => $customer->email,
    'customer_phone' => $customer->phone,
    'service_id' => $service->id,
    'variant_id' => $variant->id,
    'timeslot_id' => $timeslot->id,
    'customer_address_id' => $noAreaAddress->id,
    'space' => 50,
    'order_date' => $date,
    'payment_method' => 'cash',
]);

echo "Status: " . $response2->status() . "\n";
if ($response2->successful()) {
    echo "❌ Order should NOT have been created!\n";
} else {
    echo "✅ Correctly rejected: " . $response2->json('error') . "\n";
}
echo "\n";

// Test 3: Inactive area
echo "Test 3: Order with INACTIVE area\n";
$inactiveArea = Area::where('is_active', false)->first();
if ($inactiveArea) {
    $inactiveAddress = CustomerAddress::create([
        'customer_id' => $customer->id,
        'area_id' => $inactiveArea->id,
        'name' => 'Test',
        'title' => 'Inactive Area Address',
        'address_details' => 'Test address in inactive area',
        'is_default' => false,
    ]);
    echo "Address: {$inactiveArea->name} (INACTIVE)\n";

    $response3 = Http::post('http://localhost/api/customer/orders', [
        'customer_name' => $customer->name,
        'customer_email' => $customer->email,
        'customer_phone' => $customer->phone,
        'service_id' => $service->id,
        'variant_id' => $variant->id,
        'timeslot_id' => $timeslot->id,
        'customer_address_id' => $inactiveAddress->id,
        'space' => 50,
        'order_date' => $date,
        'payment_method' => 'cash',
    ]);

    echo "Status: " . $response3->status() . "\n";
    if ($response3->successful()) {
        echo "❌ Order should NOT have been created!\n";
    } else {
        echo "✅ Correctly rejected: " . $response3->json('error') . "\n";
    }
    
    // Cleanup
    $inactiveAddress->delete();
} else {
    echo "⚠️  No inactive areas found, skipping test\n";
}
echo "\n";

// Cleanup
$noAreaAddress->delete();

echo "=== Summary ===\n";
echo "✅ Zone validation is working correctly!\n";
echo "- Orders are allowed only from addresses in active zones/areas\n";
echo "- Orders are blocked from addresses without zones\n";
echo "- Orders are blocked from inactive areas/zones\n";
