<?php

use App\Http\Controllers\IdentificacionesController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'datos', 'middleware' => 'authAPI'], function () {
    Route::post('/datos_consulta', [IdentificacionesController::class, 'index'])->name('identificaciones.index');
    Route::post('/datos_actualiza', [IdentificacionesController::class, 'actualiza'])->name('identificaciones.actualizar');
    Route::post('/datos_servidores', [IdentificacionesController::class, 'servidores'])->name('identificaciones.servidores');
    Route::post('/servidores', [IdentificacionesController::class, 'servidores_activos'])->name('identificaciones.servidores.activos');
    Route::post('/servidores_activos', [IdentificacionesController::class, 'servidores_activos1'])->name('identificaciones.servidores.activos1');
    Route::post('/consultar_validado', [IdentificacionesController::class, 'consultar_validado'])->name('identificaciones.validado');
    Route::post('/validar_datos', [IdentificacionesController::class, 'validar_datos'])->name('identificaciones.validar');
    Route::post('/consulta_notificaciones', [IdentificacionesController::class, 'consulta_notificaciones'])->name('identificaciones.consulta_notificaciones');
});

Route::group(['middleware' => 'authAPILicencia'], function () {
    Route::post('/licencia_actualiza', [IdentificacionesController::class, 'licencia_actualiza'])->name('licencia.actualiza');
    Route::post('/licencia_consulta', [IdentificacionesController::class, 'licencia_consulta'])->name('licencia.consulta');
    Route::post('/vendedores_consulta', [IdentificacionesController::class, 'vendedores_consulta'])->name('vendedores.consulta');
    Route::post('/registrar_licencia', [IdentificacionesController::class, 'registrar_licencia'])->name('licencia.registrar');
    Route::post('/consultar_licencia', [IdentificacionesController::class, 'consultar_licencia'])->name('licencia.consultar');
});
