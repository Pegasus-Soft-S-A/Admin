<?php

use App\Http\Controllers\adminController;
use App\Http\Controllers\clientesController;
use App\Http\Controllers\distribuidoresController;
use App\Http\Controllers\licenciasController;
use App\Http\Controllers\revendedoresController;
use App\Http\Controllers\usuariosController;
use Illuminate\Support\Facades\Route;

//Rutas login 
Route::get('/', [adminController::class, 'login']);
Route::get('/login', [adminController::class, 'login'])->name('login');
Route::post('/login', [adminController::class, 'post_login'])->name('post_login');

Route::group(['middleware' => 'auth'], function () {

    Route::post('/logout', [adminController::class, 'logout'])->name('logout');

    //Rutas Admin
    Route::post('/menu', [adminController::class, 'cambiarMenu'])->name('cambiarMenu');
    Route::get('/subcategorias', [adminController::class, 'subcategorias'])->name('subcategorias');
    Route::get('/productos/{tipo}', [adminController::class, 'productos'])->name('productos');

    /* Clientes */
    Route::get('/clientes', [clientesController::class, 'index'])->name('clientes.index');
    Route::post('/clientes/tabla', [clientesController::class, 'cargarTabla'])->name('clientes.tabla');
    Route::get('/clientes/crear', [clientesController::class, 'crear'])->name('clientes.crear');
    Route::post('/clientes', [clientesController::class, 'guardar'])->name('clientes.guardar');
    Route::get('/clientes/editar/{cliente}', [clientesController::class, 'editar'])->name('clientes.editar');
    Route::put('/clientes/{cliente}', [clientesController::class, 'actualizar'])->name('clientes.actualizar');
    Route::delete('/clientes/{cliente}', [clientesController::class, 'eliminar'])->name('clientes.eliminar');

    /* Licencias */
    Route::get('/licencias/{cliente}', [licenciasController::class, 'index'])->name('licencias.index');
    Route::get('/licencias/{cliente}/crearWeb', [licenciasController::class, 'crearWeb'])->name('licencias.web.crear');
    Route::get('/licencias/{cliente}/crearPC', [licenciasController::class, 'crearPC'])->name('licencias.pc.crear');
    Route::post('/licenciasPC', [licenciasController::class, 'guardarPC'])->name('licencias.pc.guardar');
    Route::post('/licenciasWeb', [licenciasController::class, 'guardarWeb'])->name('licencias.web.guardar');
    Route::get('/licencias/editarWeb/{cliente}/{licencia}', [licenciasController::class, 'editarWeb'])->name('licencias.web.editar');
    Route::get('/licencias/editarPC/{cliente}/{licencia}', [licenciasController::class, 'editarPC'])->name('licencias.pc.editar');
    Route::put('/licenciasPC/{licencia}', [licenciasController::class, 'actualizarPC'])->name('licencias.pc.actualizar');
    Route::put('/licenciasWeb/{licencia}', [licenciasController::class, 'actualizarWeb'])->name('licencias.web.actualizar');
    Route::delete('/licencias/eliminarPC/{licencia}', [licenciasController::class, 'eliminarPC'])->name('licencias.pc.eliminar');
    Route::delete('/licencias/eliminarWeb/{licencia}', [licenciasController::class, 'eliminarWeb'])->name('licencias.web.eliminar');
    Route::get('/email/{cliente}', [licenciasController::class, 'enviarEmail'])->name('licencias.web.enviarEmail');

    /* Distribuidores */
    Route::get('/distribuidores', [distribuidoresController::class, 'index'])->name('distribuidores.index');
    Route::get('/distribuidores/crear', [distribuidoresController::class, 'crear'])->name('distribuidores.crear');
    Route::post('/distribuidores', [distribuidoresController::class, 'guardar'])->name('distribuidores.guardar');
    Route::get('/distribuidores/editar/{distribuidor}', [distribuidoresController::class, 'editar'])->name('distribuidores.editar');
    Route::put('/distribuidores/{distribuidor}', [distribuidoresController::class, 'actualizar'])->name('distribuidores.actualizar');
    Route::delete('/distribuidores/{distribuidor}', [distribuidoresController::class, 'eliminar'])->name('distribuidores.eliminar');

    /* Revendedores */
    Route::get('/revendedores', [revendedoresController::class, 'index'])->name('revendedores.index');
    Route::get('/revendedoresDistribuidor/{distribuidor}/{tipo}', [revendedoresController::class, 'revendedoresDistribuidor'])->name('revendedoresDistribuidor');
    Route::get('/revendedoresCrear', [revendedoresController::class, 'crear'])->name('revendedores.crear');
    Route::post('/revendedoresGuardar', [revendedoresController::class, 'guardar'])->name('revendedores.guardar');
    Route::get('/revendedoresEditar/{revendedor}', [revendedoresController::class, 'editar'])->name('revendedores.editar');
    Route::put('/revendedoresActualizar/{revendedor}', [revendedoresController::class, 'actualizar'])->name('revendedores.actualizar');
    Route::delete('/revendedoresEliminar/{revendedor}', [revendedoresController::class, 'eliminar'])->name('revendedores.eliminar');

    /* Usuarios */
    Route::get('/usuarios', [usuariosController::class, 'index'])->name('usuarios.index');
    Route::get('/usuariosCrear', [usuariosController::class, 'crear'])->name('usuarios.crear');
    Route::post('/usuariosGuardar', [usuariosController::class, 'guardar'])->name('usuarios.guardar');
    Route::get('/usuariosEditar/{usuarios}', [usuariosController::class, 'editar'])->name('usuarios.editar');
    Route::put('/usuariosActualizar/{usuarios}', [usuariosController::class, 'actualizar'])->name('usuarios.actualizar');
    Route::delete('/usuariosEliminar/{usuarios}', [usuariosController::class, 'eliminar'])->name('usuarios.eliminar');
});
