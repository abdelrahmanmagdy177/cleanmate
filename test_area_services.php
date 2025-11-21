<?php

use App\Models\Service;
use App\Models\Area;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Verifying Area-Based Service Filtering...\n\n";

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
echo "Using Service: {$service->name} (ID: {$service->id})\n";

// 2. Test: Service should be linked to area (from seeder)
$isLinked = $service->areas()->where('areas.id', $area->id)->exists();
echo "Service linked to Area? " . ($isLinked ? "YES" : "NO") . "\n";

if (!$isLinked) {
    echo "Linking service to area for testing...\n";
    $service->areas()->attach($area->id);
}

// 3. Test: Create a service NOT linked to this area
$exclusiveService = Service::create([
    'name' => 'Exclusive Service',
    'description' => 'Only for another area',
    'active' => true
]);
echo "Created 'Exclusive Service' (ID: {$exclusiveService->id}) - Not linked to Area {$area->id}\n";

// 4. Test: API Logic Simulation
// Query WITH area_id filter
$filteredServices = Service::whereHas('areas', function ($q) use ($area) {
    $q->where('areas.id', $area->id);
})->get();

echo "\nQuerying services for Area ID {$area->id}...\n";
$foundOriginal = $filteredServices->contains('id', $service->id);
$foundExclusive = $filteredServices->contains('id', $exclusiveService->id);

echo "- Original Service found? " . ($foundOriginal ? "PASS" : "FAIL") . "\n";
echo "- Exclusive Service found? " . (!$foundExclusive ? "PASS" : "FAIL (Should not be found)") . "\n";

// Cleanup
$exclusiveService->delete();
echo "\nTest Complete.\n";
