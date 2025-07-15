<?php

use App\Http\Controllers\adminController;
use App\Http\Controllers\agrupadosController;
use App\Http\Controllers\clientesController;
use App\Http\Controllers\distribuidoresController;

use App\Http\Controllers\Licencias\LicenciasController;
use App\Http\Controllers\Licencias\LicenciasVpsController;
use App\Http\Controllers\Licencias\LicenciasWebController;
use App\Http\Controllers\Licencias\LicenciasPcController;

use App\Http\Controllers\AdicionalController;
use App\Http\Controllers\LinksController;
use App\Http\Controllers\revendedoresController;
use App\Http\Controllers\servidoresController;
use App\Http\Controllers\usuariosController;
use App\Http\Controllers\notificacionesController;
use App\Http\Controllers\publicidadesController;
use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;

//Rutas Inicio
Route::get('/', [adminController::class, 'loginRedireccion'])->name('loginredireccion');
Route::get('/inicio', [adminController::class, 'loginRedireccion'])->name('loginredireccion');
Route::post('/inicio', [adminController::class, 'post_loginRedireccion'])->name('post_loginredireccion');


//Rutas Registro
Route::get('/registro', [adminController::class, 'registro'])->name('registro');
Route::post('/registro', [adminController::class, 'post_registro'])->name('post_registro');
Route::post('/registro/ciudades', [adminController::class, 'recuperarciudades'])->name('registro.recuperarciudades');

//Rutas Soporte
Route::get('/soporte', [adminController::class, 'soporte'])->name('ver.soporte');
Route::post('/soporte', [adminController::class, 'post_soporte'])->name('soporte');

Route::get('/sistema', function () {
    return redirect()->route('loginredireccion');
});

Route::group(['prefix' => 'admin'], function () {

    Route::get('/', [adminController::class, 'login']);
    Route::get('/login', [adminController::class, 'login'])->name('login');
    Route::post('/login', [adminController::class, 'post_login'])->name('post_login');
    Route::get('/cambiarclave', [adminController::class, 'cambiar_clave'])->name('usuarios.cambiar_clave');
    Route::post('/cambiarclave', [adminController::class, 'updatePassword'])->name('usuarios.update_password');

    Route::group(['middleware' => 'auth'], function () {

        Route::post('/logout', [adminController::class, 'logout'])->name('logout');

        //Rutas Admin
        Route::post('/menu', [adminController::class, 'cambiarMenu'])->name('cambiarMenu');
        Route::get('/subcategorias', [adminController::class, 'subcategorias'])->name('subcategorias');
        Route::get('/productos/{tipo}', [adminController::class, 'productos'])->name('productos');
        Route::get('/migrar', [adminController::class, 'migrar'])->name('migrar');
        Route::get('/licencia/{servidor}/{cliente}', [adminController::class, 'licencia'])->name('licencia');

        /* Clientes */
        Route::get('/clientes', [clientesController::class, 'index'])->name('clientes.index');
        Route::post('/clientes/tabla', [clientesController::class, 'cargarTabla'])->name('clientes.tabla');
        Route::get('/clientes/crear', [clientesController::class, 'crear'])->name('clientes.crear');
        Route::post('/clientes', [clientesController::class, 'guardar'])->name('clientes.guardar');
        Route::get('/clientes/editar/{cliente}', [clientesController::class, 'editar'])->name('clientes.editar');
        Route::put('/clientes/{cliente}', [clientesController::class, 'actualizar'])->name('clientes.actualizar');
        Route::delete('/clientes/{cliente}', [clientesController::class, 'eliminar'])->name('clientes.eliminar');

        // ==========================================
        // RUTAS DE LICENCIAS ORGANIZADAS
        // ==========================================

        Route::prefix('licencias')->name('licencias.')->group(function () {

            // ==========================================
            // VPS
            // ==========================================
            Route::prefix('vps')->name('Vps.')->group(function () {
                Route::get('/crear/{cliente}', [LicenciasVpsController::class, 'crear'])->name('crear');
                Route::post('/guardar', [LicenciasVpsController::class, 'guardar'])->name('guardar');
                Route::get('/editar/{cliente}/{licencia}', [LicenciasVpsController::class, 'editar'])->name('editar');
                Route::put('/actualizar/{licencia}', [LicenciasVpsController::class, 'actualizar'])->name('actualizar');
                Route::delete('/eliminar/{licencia}', [LicenciasVpsController::class, 'eliminar'])->name('eliminar');
            });

            // ==========================================
            // WEB
            // ==========================================
            Route::prefix('web')->name('Web.')->group(function () {
                Route::get('/{cliente}', [LicenciasWebController::class, 'index'])->name('index');
                Route::get('/crear/{cliente}', [LicenciasWebController::class, 'crear'])->name('crear');
                Route::post('/guardar', [LicenciasWebController::class, 'guardar'])->name('guardar');
                Route::get('/editar/{cliente}/{servidor}/{licencia}', [LicenciasWebController::class, 'editar'])->name('editar');
                Route::put('/actualizar/{servidor}/{licencia}', [LicenciasWebController::class, 'actualizar'])->name('actualizar');
                Route::delete('/eliminar/{servidorid}/{licenciaid}', [LicenciasWebController::class, 'eliminar'])->name('eliminar');
                Route::get('/email/{cliente}/{producto}', [LicenciasWebController::class, 'enviarEmail'])->name('enviarEmail');
                Route::get('/editarclave/{cliente}/{servidor}/{licencia}', [LicenciasWebController::class, 'editarClave'])->name('editarClave');
                Route::get('/actividad/{servidor}/{licencia}', [LicenciasWebController::class, 'actividad'])->name('actividad');
            });

            // ==========================================
            // PC
            // ==========================================
            Route::prefix('pc')->name('Pc.')->group(function () {
                Route::get('/crear/{cliente}', [LicenciasPcController::class, 'crear'])->name('crear');
                Route::post('/guardar', [LicenciasPcController::class, 'guardar'])->name('guardar');
                Route::get('/editar/{cliente}/{licencia}', [LicenciasPcController::class, 'editar'])->name('editar');
                Route::put('/actualizar/{licencia}', [LicenciasPcController::class, 'actualizar'])->name('actualizar');
                Route::delete('/eliminar/{licencia}', [LicenciasPcController::class, 'eliminar'])->name('eliminar');
            });

            // ==========================================
            // RUTAS COMPARTIDAS
            // ==========================================
            Route::get('/licencias/{cliente}', [LicenciasController::class, 'index'])->name('index');
        });

        // ==========================================
        // RUTAS PARA ADICIONALES (INDEPENDIENTES)
        // ==========================================
        Route::prefix('adicionales')->name('licencias.')->middleware('auth')->group(function () {
            Route::get('/obtener-adicionales', [AdicionalController::class, 'obtenerAdicionales'])
                ->name('obtener-adicionales');
            Route::post('/agregar-adicional', [AdicionalController::class, 'agregarAdicional'])
                ->name('agregar-adicional');
        });

        /* Distribuidores */
        Route::get('/distribuidores', [distribuidoresController::class, 'index'])->name('distribuidores.index');
        Route::get('/distribuidores/crear', [distribuidoresController::class, 'crear'])->name('distribuidores.crear');
        Route::post('/distribuidores', [distribuidoresController::class, 'guardar'])->name('distribuidores.guardar');
        Route::get('/distribuidores/editar/{distribuidor}', [distribuidoresController::class, 'editar'])->name('distribuidores.editar');
        Route::put('/distribuidores/{distribuidor}', [distribuidoresController::class, 'actualizar'])->name('distribuidores.actualizar');
        Route::delete('/distribuidores/{distribuidor}', [distribuidoresController::class, 'eliminar'])->name('distribuidores.eliminar');

        /* Revendedores */
        Route::get('/revendedores', [revendedoresController::class, 'index'])->name('revendedores.index');
        Route::get('/revendedoresdistribuidor/{distribuidor}/{tipo}', [revendedoresController::class, 'revendedoresDistribuidor'])->name('revendedoresDistribuidor');
        Route::get('/revendedorescrear', [revendedoresController::class, 'crear'])->name('revendedores.crear');
        Route::post('/revendedoresguardar', [revendedoresController::class, 'guardar'])->name('revendedores.guardar');
        Route::get('/revendedoreseditar/{revendedor}', [revendedoresController::class, 'editar'])->name('revendedores.editar');
        Route::put('/revendedoresactualizar/{revendedor}', [revendedoresController::class, 'actualizar'])->name('revendedores.actualizar');
        Route::delete('/revendedoreseliminar/{revendedor}', [revendedoresController::class, 'eliminar'])->name('revendedores.eliminar');

        /* Usuarios */
        Route::get('/usuarios', [usuariosController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarioscrear', [usuariosController::class, 'crear'])->name('usuarios.crear');
        Route::post('/usuariosguardar', [usuariosController::class, 'guardar'])->name('usuarios.guardar');
        Route::get('/usuarioseditar/{usuarios}', [usuariosController::class, 'editar'])->name('usuarios.editar');
        Route::put('/usuariosactualizar/{usuarios}', [usuariosController::class, 'actualizar'])->name('usuarios.actualizar');
        Route::delete('/usuarioseliminar/{usuarios}', [usuariosController::class, 'eliminar'])->name('usuarios.eliminar');

        /* Servidores */
        Route::get('/servidores', [servidoresController::class, 'index'])->name('servidores.index');
        Route::get('/servidorescrear', [servidoresController::class, 'crear'])->name('servidores.crear');
        Route::post('/servidoresguardar', [servidoresController::class, 'guardar'])->name('servidores.guardar');
        Route::get('/servidoreseditar/{servidores}', [servidoresController::class, 'editar'])->name('servidores.editar');
        Route::put('/servidoresactualizar/{servidores}', [servidoresController::class, 'actualizar'])->name('servidores.actualizar');
        Route::delete('/servidoreseliminar/{servidores}', [servidoresController::class, 'eliminar'])->name('servidores.eliminar');
        Route::post('/servidoresmigrar', [servidoresController::class, 'migrar'])->name('servidores.migrar');

        /* Agrupados */

        Route::get('/agrupados', [agrupadosController::class, 'index'])->name('agrupados.index');
        Route::get('/agrupadoscrear', [agrupadosController::class, 'crear'])->name('agrupados.crear');
        Route::post('/agrupadosguardar', [agrupadosController::class, 'guardar'])->name('agrupados.guardar');
        Route::get('/agrupadoseditar/{agrupados}', [agrupadosController::class, 'editar'])->name('agrupados.editar');
        Route::put('/agrupadosactualizar/{agrupados}', [agrupadosController::class, 'actualizar'])->name('agrupados.actualizar');
        Route::delete('/agrupadoseliminar/{agrupados}', [agrupadosController::class, 'eliminar'])->name('agrupados.eliminar');

        /* Notificaciones */
        Route::get('/notificaciones', [notificacionesController::class, 'index'])->name('notificaciones.index');
        Route::get('/notificacionescrear', [notificacionesController::class, 'crear'])->name('notificaciones.crear');
        Route::post('/notificacionesguardar', [notificacionesController::class, 'guardar'])->name('notificaciones.guardar');
        Route::get('/notificacioneseditar/{notificaciones}', [notificacionesController::class, 'editar'])->name('notificaciones.editar');
        Route::put('/notificacionesactualizar/{notificaciones}', [notificacionesController::class, 'actualizar'])->name('notificaciones.actualizar');
        Route::delete('/notificacioneseliminar/{notificaciones}', [notificacionesController::class, 'eliminar'])->name('notificaciones.eliminar');

        /* Links */
        Route::get('/links', [LinksController::class, 'index'])->name('links.index');
        Route::get('/linkscrear', [LinksController::class, 'crear'])->name('links.crear');
        Route::post('/linksguardar', [LinksController::class, 'guardar'])->name('links.guardar');
        Route::get('/linkseditar/{links}', [LinksController::class, 'editar'])->name('links.editar');
        Route::put('/linksactualizar/{links}', [LinksController::class, 'actualizar'])->name('links.actualizar');
        Route::delete('/linkseliminar/{links}', [LinksController::class, 'eliminar'])->name('links.eliminar');

        /* Reportes */
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/download/versiones', [ReporteController::class, 'export_versiones'])->name('reportes.export_versiones');
        Route::get('/reportes/download/respaldos', [ReporteController::class, 'export_respaldos'])->name('reportes.export_respaldos');
    });
});
