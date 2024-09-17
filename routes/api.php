<?php

use App\Http\Controllers\IngresosController;
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
