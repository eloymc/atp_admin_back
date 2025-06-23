<?php

use App\Http\Controllers\CuentasController;
use App\Http\Controllers\IngresosController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\trafico\TraficoController;
use App\Http\Controllers\catalogos\ClienteController;
use App\Http\Controllers\catalogos\BancosController;
use App\Http\Controllers\catalogos\BeneficiarioController;
use App\Http\Controllers\catalogos\TiposMovimientosBancarioController;
use App\Http\Controllers\especial\CentrexController;
use App\Models\BancoModel;
use App\Models\ClienteModel;
use App\Models\BeneficiarioModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\tabla;




Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
});
Route::middleware('auth:sanctum')->controller(LoginController::class)->group(function () {
    Route::get('/usuario', 'Usuario');
});

/********* CATALOGOS *********************/
Route::middleware('auth:sanctum')->controller(ClienteController::class)->group(function () {
    Route::get('catalogos/cat0001', 'get');
});
Route::middleware('auth:sanctum')->controller(BancosController::class)->group(function () {
    Route::get('catalogos/cat0002', 'get');
});
Route::middleware('auth:sanctum')->controller(BeneficiarioController::class)->group(function () {
    Route::get('catalogos/cat0003', 'get');
});
Route::middleware('auth:sanctum')->controller(TiposMovimientosBancarioController::class)->group(function () {
    Route::get('catalogos/cat0004', 'get');
});

/********** INGRESOS **************/
Route::middleware('auth:sanctum')->apiResource('ingresos', IngresosController::class)->only(['index', 'show', 'store', 'update']);

/********** TRAFICO **************/
Route::middleware('auth:sanctum')->apiResource('trafico', TraficoController::class)->only(['index'/*, 'show', 'store', 'update'*/]);


Route::middleware('auth:sanctum')->controller(CuentasController::class)->group(function () {
    Route::get('/cuentas', 'get');
});


/********** ESPECIALES ************/
Route::prefix('especiales')->middleware('auth:sanctum')->group(function () {
    Route::controller(CentrexController::class)->group(function () {
        Route::get('/centrex', 'get');
    });
});




