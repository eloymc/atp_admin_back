<?php

use App\Http\Controllers\CuentasController;
use App\Http\Controllers\IngresosController;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\tabla;


Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
});
Route::middleware('auth:sanctum')->controller(LoginController::class)->group(function () {
    Route::get('/usuario', 'Usuario');
});

 
Route::middleware('auth:sanctum')->controller(tabla::class)->group(function () {
    Route::get('/tabla', 'ConsultaTabla');
    Route::get('/tabla2', 'ConsultaTabla2');
});

Route::middleware('auth:sanctum')->controller(IngresosController::class)->group(function () {
    Route::get('/ingresos', 'get');
});
Route::middleware('auth:sanctum')->controller(CuentasController::class)->group(function () {
    Route::get('/cuentas', 'get');
});





