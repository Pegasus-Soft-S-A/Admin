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
    Route::post('/plan_soporte', [IdentificacionesController::class, 'plan_soporte'])->name('licencia.plan_soporte');
});

Route::group(['middleware' => 'authAPILicencia'], function () {
    Route::post('/licencia_actualiza', [IdentificacionesController::class, 'licencia_actualiza'])->name('licencia.actualiza');
    Route::post('/licencia_consulta', [IdentificacionesController::class, 'licencia_consulta'])->name('licencia.consulta');
    Route::post('/vendedores_consulta', [IdentificacionesController::class, 'vendedores_consulta'])->name('vendedores.consulta');
    Route::post('/registrar_licencia', [IdentificacionesController::class, 'registrar_licencia'])->name('licencia.registrar');
    Route::post('/consultar_licencia', [IdentificacionesController::class, 'consultar_licencia'])->name('licencia.consultar');
    Route::post('/consultar_licencia_web', [IdentificacionesController::class, 'consultar_licencia_web'])->name('licencia.consultar_web');
    Route::post('/renovar_web', [IdentificacionesController::class, 'renovar_web'])->name('licencia.renovar_web');
    Route::post('/proximas_caducar/{distribuidor?}', [IdentificacionesController::class, 'proximas_caducar'])->name('licencia.proximas_caducar');
    Route::post('/informacion_licencia', [IdentificacionesController::class, 'informacion_licencia'])->name('licencia.informacion_licencia');
    Route::post('/update_licencia', [IdentificacionesController::class, 'update_licencia'])->name('licencia.update_licencia');
    Route::post('/correos_licencia', [IdentificacionesController::class, 'correos_licencia'])->name('licencia.correos_licencia');
    Route::post('/movil_versiones', [IdentificacionesController::class, 'movil_versiones'])->name('licencia.movil_versiones');
    Route::post('/update_versiones', [IdentificacionesController::class, 'update_versiones'])->name('licencia.update_versiones');
    Route::post('/jumilo', [IdentificacionesController::class, 'jumilo'])->name('licencia.jumilo');
});

Route::get('/datos_powerbi', [IdentificacionesController::class, 'datos_powerbi'])->name('licencia.datos_powerbi');
Route::get('/datos_facebook/{inicio}/{fin}', [IdentificacionesController::class, 'gastosFacebook'])->name('licencia.gastosFacebook');
