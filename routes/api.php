<?php

use App\Http\Controllers\CuentasController;
use App\Http\Controllers\IngresosController;
use App\Http\Controllers\LoginController;
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

Route::prefix('catalogos')->middleware('auth:sanctum')->group(function () {
    Route::get('/bancos', function (){
        return response()->json(BancoModel::activos()->catalogo()->get(),200);
    } ); 
    Route::get('/clientes', function (){
        return response()->json(ClienteModel::catalogo()->get(),200);
    } ); 
    Route::get('/beneficiarios', function (){
        return response()->json(BeneficiarioModel::activos()->catalogo()->get(),200);
    } ); 
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

Route::prefix('especiales')->middleware('auth:sanctum')->group(function () {
    Route::controller(CentrexController::class)->group(function () {
        Route::get('/centrex', 'get');
    });
});





