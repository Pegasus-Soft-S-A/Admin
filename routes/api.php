<?php

use App\Http\Controllers\IdentificacionesController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'datos', 'middleware' => 'authAPI'], function () {
    Route::post('/datos_consulta', [IdentificacionesController::class, 'index'])->name('identificaciones.index');
    Route::post('/datos_actualiza', [IdentificacionesController::class, 'actualiza'])->name('identificaciones.actualizar');
    Route::post('/datos_servidores', [IdentificacionesController::class, 'servidores'])->name('identificaciones.servidores');
    Route::post('/servidores', [IdentificacionesController::class, 'servidores_activos'])->name('identificaciones.servidores.activos');
    Route::post('/consultar_validado', [IdentificacionesController::class, 'consultar_validado'])->name('identificaciones.validado');
    Route::post('/validar_datos', [IdentificacionesController::class, 'validar_datos'])->name('identificaciones.validar');
});

Route::group(['middleware' => 'authAPILicencia'], function () {
    Route::post('/licencia_actualiza', [IdentificacionesController::class, 'licencia_actualiza'])->name('licencia.actualiza');
    Route::post('/licencia_consulta', [IdentificacionesController::class, 'licencia_consulta'])->name('licencia.consulta');
});
