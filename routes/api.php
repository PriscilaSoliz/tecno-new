<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas de PagoFácil (sin CSRF, sin autenticación)
Route::prefix('pagofacil')->group(function () {
    Route::post('/generar-qr', [\App\Http\Controllers\PagoFacilController::class, 'generarQR'])->name('pagofacil.generar-qr');
    Route::post('/consultar-estado', [\App\Http\Controllers\PagoFacilController::class, 'consultarEstado'])->name('pagofacil.consultar-estado');
    Route::post('/callback', [\App\Http\Controllers\PagoFacilController::class, 'callback'])->name('pagofacil.callback');
    Route::get('/return', [\App\Http\Controllers\PagoFacilController::class, 'return'])->name('pagofacil.return');
});
