<?php

use App\Models\Service;
use App\Models\Area;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Customer\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Services\Customer\AuthService;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Verifying Customer Auth & Services...\n\n";

try {
    // 1. Setup
    $area = Area::first();
    if (!$area) { echo "Error: No areas found.\n"; exit(1); }

    // 2. Test Registration
    $email = 'auth_test_' . time() . '@example.com';
    $password = 'password123';
    $phone = '010' . rand(10000000, 99999999);

    $authService = new AuthService();
    
    echo "Registering new customer...\n";
    $regResult = $authService->register([
        'name' => 'Auth Test User',
        'email' => $email,
        'password' => $password,
        'mobile' => $phone,
        'area' => $area->id
    ]);
    echo "Registration: PASS (User ID: {$regResult['user']->id})\n";
    $customer = $regResult['user'];

    // 3. Verify Customer Data
    $customer->refresh();
    echo "- Phone mapped correctly? " . ($customer->phone === $phone ? "PASS" : "FAIL") . "\n";
    echo "- Area mapped correctly? " . ($customer->area_id == $area->id ? "PASS" : "FAIL") . "\n";

    // 4. Test Login
    echo "\nLogging in...\n";
    $loginResult = $authService->login($email, $password);
    echo "Login: PASS\n";

    // 5. Test Service Fetching (via Controller)
    $serviceController = new ServiceController();
    $serviceRequest = Request::create('/api/customer/services', 'GET');
    $serviceRequest->setUserResolver(function () use ($customer) { return $customer; });

    echo "\nFetching services for logged in customer...\n";
    $response = $serviceController->index($serviceRequest);
    $data = $response->getData();

    echo "Services returned: " . count($data) . "\n";
    if (count($data) > 0) {
        echo "Service Fetch: PASS\n";
    } else {
        echo "Service Fetch: WARNING (No services found, but call succeeded)\n";
    }

    // Cleanup
    $customer->delete();
    echo "\nTest Complete.\n";

} catch (\Throwable $e) {
    echo "\nCRITICAL ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " on line " . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}
