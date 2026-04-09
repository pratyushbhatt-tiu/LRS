<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\File;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// 1. Get a test user (Admin or Operations)
$user = User::whereHas('roles', function($q) {
    $q->whereIn('name', ['Admin', 'Operations']);
})->first();

if (!$user) {
    echo "No valid user found.\n";
    exit(1);
}

Auth::login($user);

// 2. Get a file in a shippable status (from previous script)
$file = File::where('current_status', config('constants.file_statuses.ACCOUNTING_APPROVED'))->first();
if (!$file) {
    echo "No shippable file found. Run prepare_shipping_test.php first.\n";
    exit(1);
}

echo "Testing ShippingController@ship logic...\n";
echo "File ID: {$file->id} (File No: {$file->file_no})\n";
echo "Current Status: {$file->current_status}\n";

// 3. Mock the request
$request = Request::create("/shipping/{$file->id}/ship", 'POST', [
    'courier' => 'FedEx Test',
    'tracking_number' => 'VERIFY-123456',
    'shipped_at' => date('Y-m-d'),
    'shipping_notes' => 'Automated Verification Step'
]);

// 4. Call the controller method directly
$controller = app(\App\Http\Controllers\ShippingController::class);
try {
    $response = $controller->ship($request, $file);
    
    // 5. Refresh and Check
    $file->refresh();
    echo "New Status: {$file->current_status}\n";
    echo "Courier Saved: {$file->courier}\n";
    echo "Tracking Saved: {$file->tracking_number}\n";
    
    $expectedStatus = config('constants.file_statuses.RECORDING');
    if ($file->current_status === $expectedStatus && $file->courier === 'FedEx Test') {
        echo "SUCCESS: Shipping logic is fully functional.\n";
    } else {
        echo "FAILURE: Status or data mismatch.\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
