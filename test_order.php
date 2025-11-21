<?php

// Get test data
$service = App\Models\Service::first();
$variant = $service->variants()->first();
$customer = App\Models\Customer::where('email', 'alice@example.com')->first();
$address = $customer->addresses()->first();
$date = now()->addDay()->format('Y-m-d');
$dayOfWeek = now()->addDay()->dayOfWeek;
$timeslot = App\Models\Timeslot::where('day', $dayOfWeek)->first();

echo "=== Test Data ===" . PHP_EOL;
echo "Service: {$service->name} (ID: {$service->id})" . PHP_EOL;
echo "Variant: {$variant->name} (ID: {$variant->id})" . PHP_EOL;
echo "Customer: {$customer->email}" . PHP_EOL;
echo "Address ID: {$address->id}" . PHP_EOL;
echo "Timeslot: {$timeslot->start_time}-{$timeslot->end_time} (ID: {$timeslot->id})" . PHP_EOL;
echo "Order Date: {$date}" . PHP_EOL;
echo PHP_EOL;

// Create test order
echo "=== Creating Test Order ===" . PHP_EOL;

$response = Http::post('http://localhost/api/customer/orders', [
    'customer_name' => $customer->name,
    'customer_email' => $customer->email,
    'customer_phone' => $customer->phone,
    'service_id' => $service->id,
    'variant_id' => $variant->id,
    'timeslot_id' => $timeslot->id,
    'customer_address_id' => $address->id,
    'space' => 75,
    'order_date' => $date,
    'payment_method' => 'cash',
    'notes' => 'Test order from seeded data'
]);

echo "Status: " . $response->status() . PHP_EOL;
echo "Response:" . PHP_EOL;
echo json_encode($response->json(), JSON_PRETTY_PRINT) . PHP_EOL;
