<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Distribuidores extends Model
{
    use HasFactory;
    protected $table = 'sis_distribuidores';
    protected $primaryKey = 'sis_distribuidoresid';
    public $timestamps = false;
    protected $guarded = [];

    protected static function booted()
    {
        function log($tipooperacion, $detalle)
        {
            $log = new Log();
            $log->usuario = Auth::user()->nombres;
            $log->pantalla = "Distribuidores";
            $log->tipooperacion = $tipooperacion;
            $log->fecha = now();
            $log->detalle = $detalle;
            $log->save();
        }

        static::created(function ($distribuidor) {
            log("Crear", $distribuidor);
        });

        static::updated(function ($distribuidor) {
            log("Modificar", $distribuidor);
        });

        static::deleted(function ($distribuidor) {
            log("Eliminar", $distribuidor);
        });
    }
}
