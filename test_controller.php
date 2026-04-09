<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FeeLine;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// 1. Get a test user (Admin or Accounting)
$user = User::whereHas('roles', function($q) {
    $q->whereIn('name', ['Admin', 'Accounting']);
})->first();

if (!$user) {
    echo "No valid user found.\n";
    exit(1);
}

Auth::login($user);

// 2. Get a test fee line
$feeLine = FeeLine::first();
if (!$feeLine) {
    echo "No fee line found.\n";
    exit(1);
}

echo "Testing AccountingController@overrideFee directly...\n";
echo "Fee Line: {$feeLine->description} (ID: {$feeLine->id})\n";
echo "Current Amount: {$feeLine->total_amount}\n";

// 3. Mock the request
$request = Request::create('/accounting/fee-override', 'POST', [
    'fee_line_id' => $feeLine->id,
    'new_total' => 1234.56,
    'reason' => 'Test Override Simulation'
]);

// 4. Call the controller method
$controller = app(\App\Http\Controllers\AccountingController::class);
try {
    $response = $controller->overrideFee($request);
    
    // 5. Check if updated
    $feeLine->refresh();
    echo "New Amount: {$feeLine->total_amount}\n";
    echo "Is Override: " . ($feeLine->is_override ? 'YES' : 'NO') . "\n";
    
    if ($feeLine->total_amount == 1234.56 && $feeLine->is_override) {
        echo "SUCCESS: Controller logic is working.\n";
    } else {
        echo "FAILURE: Controller or Model logic is broken.\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
