<?php

use App\Http\Controllers\{
    IdentificacionesController,
    ApiController,
    ServidoresController,
    revendedoresController,
    ClientesController,
    ReporteController,
    notificacionesController,
    AdicionalController
};
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Licencias\LicenciasAPIController;


Route::group(['prefix' => 'datos', 'middleware' => 'authAPI'], function () {

    Route::post('/datos_consulta', [IdentificacionesController::class, 'index'])
        ->name('identificaciones.index');
    Route::post('/datos_actualiza', [IdentificacionesController::class, 'actualiza'])
        ->name('identificaciones.actualizar');
    Route::post('/consultar_validado', [IdentificacionesController::class, 'consultar_validado'])
        ->name('identificaciones.validado');
    Route::post('/validar_datos', [IdentificacionesController::class, 'validar_datos'])
        ->name('identificaciones.validar');
    Route::post('/consulta_notificaciones', [notificacionesController::class, 'consulta_notificaciones'])
        ->name('identificaciones.consulta_notificaciones');
    Route::post('/plan_soporte', [ApiController::class, 'plan_soporte'])
        ->name('licencia.plan_soporte');
    Route::post('/datos_servidores', [ServidoresController::class, 'servidores'])
        ->name('identificaciones.servidores');
    Route::post('/servidores', [ServidoresController::class, 'servidores_activos'])
        ->name('identificaciones.servidores.activos');
    Route::post('/servidores_activos', [ServidoresController::class, 'servidores_activos1'])
        ->name('identificaciones.servidores.activos1');
});

Route::group(['middleware' => 'authAPILicencia'], function () {
    // Licencias
    Route::post('/licencia_actualiza', [LicenciasAPIController::class, 'licencia_actualiza'])
        ->name('licencia.actualiza');
    Route::post('/licencia_consulta', [LicenciasAPIController::class, 'licencia_consulta'])
        ->name('licencia.consulta');
    Route::post('/registrar_licencia', [LicenciasAPIController::class, 'registrar_licencia'])
        ->name('licencia.registrar');
    Route::post('/consultar_licencia', [LicenciasAPIController::class, 'consultar_licencia'])
        ->name('licencia.consultar');
    Route::post('/consultar_licencia_web', [LicenciasAPIController::class, 'consultar_licencia_web'])
        ->name('licencia.consultar_web');
    Route::post('/consultar_licencia_web_jumilo', [LicenciasAPIController::class, 'consultar_licencia_web_jumilo'])
        ->name('licencia.consultar_web_jumilo');
    Route::post('/renovar_web', [LicenciasAPIController::class, 'renovar_web'])
        ->name('licencia.renovar_web');
    Route::post('/proximas_caducar/{distribuidor?}', [LicenciasAPIController::class, 'proximas_caducar'])
        ->name('licencia.proximas_caducar');
    Route::post('/informacion_licencia', [LicenciasAPIController::class, 'informacion_licencia'])
        ->name('licencia.informacion_licencia');
    Route::post('/update_licencia', [LicenciasAPIController::class, 'update_licencia'])
        ->name('licencia.update_licencia');
    Route::post('/correos_licencia', [LicenciasAPIController::class, 'correos_licencia'])
        ->name('licencia.correos_licencia');
    Route::post('/actualizar_identificador', [LicenciasAPIController::class, 'actualizar_identificador'])
        ->name('licencia.actualizar_identificador');
    Route::post('/jumilo', [LicenciasAPIController::class, 'jumilo'])
        ->name('licencia.jumilo');

    // Vendedores
    Route::post('/vendedores_consulta', [revendedoresController::class, 'vendedores_consulta'])
        ->name('vendedores.consulta');

    // Clientes
    Route::post('/consulta_clientes', [ClientesController::class, 'consulta_clientes'])
        ->name('licencia.consulta_clientes');

    // Versiones
    Route::post('/movil_versiones', [ApiController::class, 'movil_versiones'])
        ->name('licencia.movil_versiones');
    Route::post('/update_versiones', [ApiController::class, 'update_versiones'])
        ->name('licencia.update_versiones');

    // Adicionales
    Route::post('/adicionales/procesar', [AdicionalController::class, 'procesarAdicionalSimple']);
});

// Reportes públicos
Route::get('/datos_powerbi', [ReporteController::class, 'datos_powerbi'])
    ->name('licencia.datos_powerbi');
Route::get('/datos_facebook/{inicio}/{fin}', [ReporteController::class, 'gastosFacebook'])
    ->name('licencia.gastosFacebook');

