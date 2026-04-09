<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\File;
use App\Models\FileStatusHistory;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

// 1. Get an Admin to perform action
$user = User::whereHas('roles', function($q){ $q->where('name', 'Admin'); })->first();
Auth::login($user);

// 2. Get a file in any status and move it to ACCOUNTING_APPROVED
$file = File::first();
if (!$file) {
    echo "No files found.\n";
    exit;
}

$oldStatus = $file->current_status;
$file->update(['current_status' => config('constants.file_statuses.ACCOUNTING_APPROVED')]);

echo "Moved File #{$file->file_no} from {$oldStatus} to ACCOUNTING_APPROVED.\n";
echo "Check the Shipping Dashboard now at /shipping\n";
