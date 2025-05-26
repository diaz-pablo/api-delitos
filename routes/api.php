<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DelitoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Login
Route::post('/auth/login', [AuthController::class, 'login']);

// Endpoints para el CRUD de Delitos
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('delitos', [DelitoController::class, 'index'])->name('delitos.index');
    Route::post('delitos', [DelitoController::class, 'store'])->name('delitos.store');
    Route::get('delitos/{delito}', [DelitoController::class, 'show'])->name('delitos.show');
    Route::put('delitos/{delito}', [DelitoController::class, 'update'])->name('delitos.update');
    Route::delete('delitos/{delito}', [DelitoController::class, 'destroy'])->name('delitos.destroy');
});
