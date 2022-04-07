<?php

use App\Http\Controllers\IdentificacionesController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'datos', 'middleware' => 'authAPI'], function () {
    Route::post('/datos_consulta', [IdentificacionesController::class, 'index'])->name('identificaciones.index');
    Route::post('/datos_actualiza', [IdentificacionesController::class, 'actualiza'])->name('identificaciones.actualizar');
});
