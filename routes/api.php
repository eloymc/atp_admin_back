<?php

use App\Http\Controllers\CuentasController;
use App\Http\Controllers\IngresosController;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\Http\Controllers\tabla;
 
Route::controller(tabla::class)->group(function () {
    Route::get('/tabla', 'ConsultaTabla');
    Route::get('/tabla2', 'ConsultaTabla2');
});

Route::controller(IngresosController::class)->group(function () {
    Route::get('/ingresos', 'get');
});
Route::controller(CuentasController::class)->group(function () {
    Route::get('/cuentas', 'get');
});

Route::controller(LoginController::class)->group(function () {
    Route::post('/login', 'login');
});
