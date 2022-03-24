<?php

use App\Http\Controllers\adminController;
use App\Http\Controllers\clientesController;
use App\Http\Controllers\distribuidoresController;
use App\Http\Controllers\revendedoresController;
use App\Http\Controllers\usuariosController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Route::get('/', function () {
    return view('admin.layouts.app');
}); */

Route::post('/menu', [adminController::class, 'cambiarMenu'])->name('cambiarMenu');
Route::post('/recuperar-Post', [adminController::class, 'recuperarPost'])->name('recuperarInformacionPost');

/* Clientes */

Route::get('/', [clientesController::class, 'index'])->name('clientes.index');

/* Distribuidores */

Route::get('/distribuidores', [distribuidoresController::class, 'index'])->name('distribuidores.index');
Route::get('/distribuidores/crear', [distribuidoresController::class, 'crear'])->name('distribuidores.crear');
Route::post('/distribuidores', [distribuidoresController::class, 'guardar'])->name('distribuidores.guardar');
Route::get('/distribuidores/editar/{distribuidor}', [distribuidoresController::class, 'editar'])->name('distribuidores.editar');
Route::put('/distribuidores/{distribuidor}', [distribuidoresController::class, 'actualizar'])->name('distribuidores.actualizar');
Route::delete('/distribuidores/{distribuidor}', [distribuidoresController::class, 'eliminar'])->name('distribuidores.eliminar');

/* Revendedores */
Route::get('/revendedores', [revendedoresController::class, 'index'])->name('revendedores.index');
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
