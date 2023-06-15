<?php

use App\Http\Controllers\adminController;
use App\Http\Controllers\agrupadosController;
use App\Http\Controllers\clientesController;
use App\Http\Controllers\distribuidoresController;
use App\Http\Controllers\licenciasController;
use App\Http\Controllers\revendedoresController;
use App\Http\Controllers\servidoresController;
use App\Http\Controllers\usuariosController;
use App\Http\Controllers\notificacionesController;
use App\Http\Controllers\publicidadesController;
use Illuminate\Support\Facades\Route;

//Rutas Inicio
Route::get('/', [adminController::class, 'loginRedireccion'])->name('loginredireccion');
Route::get('/inicio', [adminController::class, 'loginRedireccion'])->name('loginredireccion');
Route::post('/inicio', [adminController::class, 'post_loginRedireccion'])->name('post_loginredireccion');


//Rutas Registro
Route::get('/registro', [adminController::class, 'registro'])->name('registro');
Route::post('/registro', [adminController::class, 'post_registro'])->name('post_registro');
Route::post('/registro/ciudades', [adminController::class, 'recuperarciudades'])->name('registro.recuperarciudades');

Route::get('/sistema', function () {
    return redirect()->route('loginredireccion');
});

Route::group(['prefix' => 'admin'], function () {

    Route::get('/', [adminController::class, 'login']);
    Route::get('/login', [adminController::class, 'login'])->name('login');
    Route::post('/login', [adminController::class, 'post_login'])->name('post_login');

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

        /* Licencias */
        Route::get('/licencias/actividad/{servidor}/{licencia}', [licenciasController::class, 'actividad'])->name('licencias.actividad');
        Route::get('/licencias/{cliente}', [licenciasController::class, 'index'])->name('licencias.index');
        Route::get('/licencias/{cliente}/crearweb', [licenciasController::class, 'crearWeb'])->name('licencias.Web.crear');
        Route::get('/licencias/{cliente}/crearpc', [licenciasController::class, 'crearPC'])->name('licencias.Pc.crear');
        Route::get('/licencias/{cliente}/crearvps', [licenciasController::class, 'crearVPS'])->name('licencias.Vps.crear');
        Route::post('/licenciaspc', [licenciasController::class, 'guardarPC'])->name('licencias.Pc.guardar');
        Route::post('/licenciasweb', [licenciasController::class, 'guardarWeb'])->name('licencias.Web.guardar');
        Route::post('/licenciasvps', [licenciasController::class, 'guardarVps'])->name('licencias.Vps.guardar');
        Route::get('/licencias/editarweb/{cliente}/{servidor}/{licencia}', [licenciasController::class, 'editarWeb'])->name('licencias.Web.editar');
        Route::get('/licencias/editarpc/{cliente}/{licencia}', [licenciasController::class, 'editarPC'])->name('licencias.Pc.editar');
        Route::get('/licencias/editarvps/{cliente}/{licencia}', [licenciasController::class, 'editarVPS'])->name('licencias.Vps.editar');
        Route::put('/licenciaspc/{licencia}', [licenciasController::class, 'actualizarPC'])->name('licencias.Pc.actualizar');
        Route::put('/licenciasvps/{licencia}', [licenciasController::class, 'actualizarVPS'])->name('licencias.Vps.actualizar');
        Route::put('/licenciasweb/{servidor}/{licencia}', [licenciasController::class, 'actualizarWeb'])->name('licencias.Web.actualizar');
        Route::delete('/licencias/eliminarpc/{licencia}', [licenciasController::class, 'eliminarPC'])->name('licencias.Pc.eliminar');
        Route::delete('/licencias/eliminarvps/{licencia}', [licenciasController::class, 'eliminarVps'])->name('licencias.Vps.eliminar');
        Route::delete('/licencias/eliminarweb/{servidor}/{licencia}', [licenciasController::class, 'eliminarWeb'])->name('licencias.Web.eliminar');
        Route::get('/email/{cliente}/{producto}', [licenciasController::class, 'enviarEmail'])->name('licencias.Web.enviaremail');
        Route::get('/licencias/editarclave/{cliente}/{servidor}/{licencia}', [licenciasController::class, 'editarClave'])->name('editar_clave');

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

        /* Publicidad */
        Route::get('/publicidades', [publicidadesController::class, 'index'])->name('publicidades.index');
        Route::get('/publicidadescrear', [publicidadesController::class, 'crear'])->name('publicidades.crear');
        Route::post('/publicidadesguardar', [publicidadesController::class, 'guardar'])->name('publicidades.guardar');
        Route::get('/publicidadeseditar/{publicidades}', [publicidadesController::class, 'editar'])->name('publicidades.editar');
        Route::put('/publicidadesactualizar/{publicidades}', [publicidadesController::class, 'actualizar'])->name('publicidades.actualizar');
        Route::delete('/publicidadeseliminar/{publicidades}', [publicidadesController::class, 'eliminar'])->name('publicidades.eliminar');
    });
});
