<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Files Module - Operations, QC, Accounting roles
Route::middleware(['auth', 'permission:files.view'])->prefix('files')->name('files.')->group(function () {
    Route::get('/', [App\Http\Controllers\FileController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\FileController::class, 'create'])->middleware('permission:files.create')->name('create');
    Route::post('/', [App\Http\Controllers\FileController::class, 'store'])->middleware('permission:files.create')->name('store');
    Route::get('/import', [App\Http\Controllers\ImportController::class, 'index'])->middleware('permission:files.create')->name('import');
    Route::get('/import/template', [App\Http\Controllers\ImportController::class, 'downloadTemplate'])->middleware('permission:files.create')->name('import.template');
    Route::post('/import', [App\Http\Controllers\ImportController::class, 'upload'])->middleware('permission:files.create')->name('import.upload');
    Route::get('/import/{importLog}', [App\Http\Controllers\ImportController::class, 'show'])->name('import.show');
    Route::get('/import/{importLog}/errors', [App\Http\Controllers\ImportController::class, 'downloadErrors'])->name('import.errors');
    Route::get('/{file}', [App\Http\Controllers\FileController::class, 'show'])->name('show');
    Route::get('/{file}/edit', [App\Http\Controllers\FileController::class, 'edit'])->middleware('permission:files.edit')->name('edit');
    Route::put('/{file}', [App\Http\Controllers\FileController::class, 'update'])->middleware('permission:files.edit')->name('update');
    Route::delete('/{file}', [App\Http\Controllers\FileController::class, 'destroy'])->middleware('role:Admin')->name('destroy');
    Route::post('/{file}/transition', [App\Http\Controllers\FileController::class, 'transition'])->name('transition');
});

// QC Module - QC role only
Route::middleware(['auth', 'role:QC,Admin'])->prefix('qc')->name('qc.')->group(function () {
    Route::get('/', [App\Http\Controllers\QCController::class, 'index'])->name('index');
    Route::get('/pending', [App\Http\Controllers\QCController::class, 'pending'])->name('pending');
    Route::get('/{file}/show', [App\Http\Controllers\QCController::class, 'show'])->name('show');
    Route::post('/{file}/pass', [App\Http\Controllers\QCController::class, 'pass'])->name('pass');
    Route::post('/{file}/fail', [App\Http\Controllers\QCController::class, 'fail'])->name('fail');
});

// Accounting Module - Accounting/Admin roles
Route::middleware(['auth', 'role:Accounting,Admin'])->prefix('accounting')->name('accounting.')->group(function () {
    Route::get('/', [App\Http\Controllers\AccountingController::class, 'index'])->name('index');
    Route::get('/pending', [App\Http\Controllers\AccountingController::class, 'index'])->name('pending');
    Route::get('/{file}', [App\Http\Controllers\AccountingController::class, 'show'])->name('show');
    Route::post('/{file}/approve', [App\Http\Controllers\AccountingController::class, 'approve'])->name('approve');
    Route::post('/fee-override', [App\Http\Controllers\AccountingController::class, 'overrideFee'])->name('fee-lines.override-static');
    Route::post('/fee-lines/{feeLine}/override', [App\Http\Controllers\AccountingController::class, 'overrideFee'])->name('fee-lines.override');
});

// Shipping Module - Admin/Operations (files.ship permission)
Route::middleware(['auth', 'can:files.ship'])->prefix('shipping')->name('shipping.')->group(function () {
    Route::get('/', [App\Http\Controllers\ShippingController::class, 'index'])->name('index');
    Route::get('/{file}', [App\Http\Controllers\ShippingController::class, 'show'])->name('show');
    Route::post('/{file}/ship', [App\Http\Controllers\ShippingController::class, 'ship'])->name('ship');
});

// Masters Module - Admin only
Route::middleware(['auth', 'role:Admin'])->prefix('masters')->name('masters.')->group(function () {
    // Main Masters Dashboard
    Route::get('/', function () {
        return view('masters.dashboard');
    })->name('index');

    // Clients
    Route::resource('clients', App\Http\Controllers\Masters\ClientController::class);
    Route::post('clients/{id}/restore', [App\Http\Controllers\Masters\ClientController::class, 'restore'])->name('clients.restore');
    Route::post('clients/{client}/toggle-active', [App\Http\Controllers\Masters\ClientController::class, 'toggleActive'])->name('clients.toggle-active');

    // Doc Types
    Route::resource('doc-types', App\Http\Controllers\Masters\DocTypeController::class);
    Route::post('doc-types/{id}/restore', [App\Http\Controllers\Masters\DocTypeController::class, 'restore'])->name('doc-types.restore');
    Route::post('doc-types/{docType}/toggle-active', [App\Http\Controllers\Masters\DocTypeController::class, 'toggleActive'])->name('doc-types.toggle-active');

    // Recording Purposes
    Route::resource('recording-purposes', App\Http\Controllers\Masters\RecordingPurposeController::class);
    Route::post('recording-purposes/{id}/restore', [App\Http\Controllers\Masters\RecordingPurposeController::class, 'restore'])->name('recording-purposes.restore');
    Route::post('recording-purposes/{recordingPurpose}/toggle-active', [App\Http\Controllers\Masters\RecordingPurposeController::class, 'toggleActive'])->name('recording-purposes.toggle-active');

    // States
    Route::resource('states', App\Http\Controllers\Masters\StateController::class);
    Route::post('states/{id}/restore', [App\Http\Controllers\Masters\StateController::class, 'restore'])->name('states.restore');
    Route::post('states/{state}/toggle-active', [App\Http\Controllers\Masters\StateController::class, 'toggleActive'])->name('states.toggle-active');

    // Counties
    Route::resource('counties', App\Http\Controllers\Masters\CountyController::class);
    Route::post('counties/{id}/restore', [App\Http\Controllers\Masters\CountyController::class, 'restore'])->name('counties.restore');
    Route::post('counties/{county}/toggle-active', [App\Http\Controllers\Masters\CountyController::class, 'toggleActive'])->name('counties.toggle-active');

    // Cities
    Route::resource('cities', App\Http\Controllers\Masters\CityController::class);
    Route::post('cities/{id}/restore', [App\Http\Controllers\Masters\CityController::class, 'restore'])->name('cities.restore');
    Route::post('cities/{city}/toggle-active', [App\Http\Controllers\Masters\CityController::class, 'toggleActive'])->name('cities.toggle-active');

    // Fee Rules
    Route::resource('fee-rules', App\Http\Controllers\Masters\FeeRuleController::class);
    Route::post('fee-rules/{id}/restore', [App\Http\Controllers\Masters\FeeRuleController::class, 'restore'])->name('fee-rules.restore');
    Route::post('fee-rules/{feeRule}/toggle-active', [App\Http\Controllers\Masters\FeeRuleController::class, 'toggleActive'])->name('fee-rules.toggle-active');
});

// Reports Module - All authenticated users with reports.view permission
Route::middleware(['auth', 'permission:reports.view'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', function () {
        return view('reports.index');
    })->name('index');
    // More report routes will be added in Phase 9
});

// Audit Logs - Admin only (audit-logs.view permission)
Route::middleware(['auth', 'permission:audit-logs.view'])->prefix('audit-logs')->name('audit-logs.')->group(function () {
    Route::get('/', [App\Http\Controllers\AuditLogController::class, 'index'])->name('index');
});

require __DIR__ . '/auth.php';
