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
    Route::get('/', function () {
        return view('files.index');
    })->name('index');
    Route::get('/create', function () {
        return view('files.create');
    })->middleware('permission:files.create')->name('create');
    Route::get('/import', function () {
        return view('files.import');
    })->middleware('role:Operations,Admin')->name('import');
    // More file routes will be added in Phase 4
});

// QC Module - QC role only
Route::middleware(['auth', 'role:QC,Admin'])->prefix('qc')->name('qc.')->group(function () {
    Route::get('/', function () {
        return view('qc.index');
    })->name('index');
    Route::get('/pending', function () {
        return view('qc.pending');
    })->name('pending');
    // More QC routes will be added in Phase 6
});

// Accounting Module - Accounting role
Route::middleware(['auth', 'role:Accounting,Admin'])->prefix('accounting')->name('accounting.')->group(function () {
    Route::get('/', function () {
        return view('accounting.index');
    })->name('index');
    // More accounting routes will be added in Phase 7
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

require __DIR__ . '/auth.php';
