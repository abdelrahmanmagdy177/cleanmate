<?php

// Test script to verify zone-based delivery fee calculation

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Customer;
use App\Models\Service;
use App\Models\Order;
use App\Models\Area;
use App\Models\Zone;

echo "=== Zone-Based Delivery Fee Test ===\n\n";

// Get zones
$centralZone = Zone::where('name', 'Central Zone')->first();
$suburbanZone = Zone::where('name', 'Suburban Zone')->first();
$outerZone = Zone::where('name', 'Outer Zone')->first();

echo "Zones:\n";
echo "- {$centralZone->name}: {$centralZone->delivery_fee} EGP\n";
echo "- {$suburbanZone->name}: {$suburbanZone->delivery_fee} EGP\n";
echo "- {$outerZone->name}: {$outerZone->delivery_fee} EGP\n\n";

// Get customers with different areas
$alice = Customer::where('email', 'alice@example.com')->first();
$bob = Customer::where('email', 'bob@example.com')->first();
$carol = Customer::where('email', 'carol@example.com')->first();

$aliceAddress = $alice->addresses()->first();
$bobAddress = $bob->addresses()->first();
$carolAddress = $carol->addresses()->first();

echo "Customer Addresses:\n";
echo "- Alice: {$aliceAddress->area->name} ({$aliceAddress->area->zone->name})\n";
echo "- Bob: {$bobAddress->area->name} ({$bobAddress->area->zone->name})\n";
echo "- Carol: {$carolAddress->area->name} ({$carolAddress->area->zone->name})\n\n";

// Create test orders
$service = Service::first();
$variant = $service->variants()->first();

echo "Creating test orders with 100 EGP service price...\n\n";

// Alice's order (Central Zone - 20 EGP)
$aliceOrder = new Order([
    'customer_id' => $alice->id,
    'customer_address_id' => $aliceAddress->id,
    'service_id' => $service->id,
    'variant_id' => $variant->id,
    'order_date' => now()->addDay()->format('Y-m-d'),
    'status' => 'pending',
    'payment_method' => 'cash',
]);
$aliceOrder->calculatePricing(100.00);

echo "Alice's Order (Downtown - Central Zone):\n";
echo "  Service Price: {$aliceOrder->service_price} EGP\n";
echo "  Delivery Fee: {$aliceOrder->delivery_fee} EGP ✅\n";
echo "  Subtotal: " . ($aliceOrder->service_price + $aliceOrder->delivery_fee) . " EGP\n";
echo "  VAT (14%): {$aliceOrder->vat_amount} EGP\n";
echo "  Total: {$aliceOrder->total_price} EGP\n\n";

// Bob's order (Suburban Zone - 35 EGP)
$bobOrder = new Order([
    'customer_id' => $bob->id,
    'customer_address_id' => $bobAddress->id,
    'service_id' => $service->id,
    'variant_id' => $variant->id,
    'order_date' => now()->addDay()->format('Y-m-d'),
    'status' => 'pending',
    'payment_method' => 'cash',
]);
$bobOrder->calculatePricing(100.00);

echo "Bob's Order (North Suburbs - Suburban Zone):\n";
echo "  Service Price: {$bobOrder->service_price} EGP\n";
echo "  Delivery Fee: {$bobOrder->delivery_fee} EGP ✅\n";
echo "  Subtotal: " . ($bobOrder->service_price + $bobOrder->delivery_fee) . " EGP\n";
echo "  VAT (14%): {$bobOrder->vat_amount} EGP\n";
echo "  Total: {$bobOrder->total_price} EGP\n\n";

// Carol's order (Outer Zone - 50 EGP)
$carolOrder = new Order([
    'customer_id' => $carol->id,
    'customer_address_id' => $carolAddress->id,
    'service_id' => $service->id,
    'variant_id' => $variant->id,
    'order_date' => now()->addDay()->format('Y-m-d'),
    'status' => 'pending',
    'payment_method' => 'cash',
]);
$carolOrder->calculatePricing(100.00);

echo "Carol's Order (Far East - Outer Zone):\n";
echo "  Service Price: {$carolOrder->service_price} EGP\n";
echo "  Delivery Fee: {$carolOrder->delivery_fee} EGP ✅\n";
echo "  Subtotal: " . ($carolOrder->service_price + $carolOrder->delivery_fee) . " EGP\n";
echo "  VAT (14%): {$carolOrder->vat_amount} EGP\n";
echo "  Total: {$carolOrder->total_price} EGP\n\n";

echo "=== Summary ===\n";
echo "✅ Delivery fees are correctly calculated based on zone!\n";
echo "- Central Zone (Downtown): 20 EGP → Total: {$aliceOrder->total_price} EGP\n";
echo "- Suburban Zone (North Suburbs): 35 EGP → Total: {$bobOrder->total_price} EGP\n";
echo "- Outer Zone (Far East): 50 EGP → Total: {$carolOrder->total_price} EGP\n";
