<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Clientes extends Model
{
    use HasFactory;
    protected $table = 'sis_clientes';
    protected $primaryKey = 'sis_clientesid';
    public $timestamps = false;
    protected $guarded = [];

    //$tipo si es pc o todos los clientes, $distribuidor 0 para traer todos caso contrario trae solo los del distribuidor
    public function scopeClientes($query, $tipo, $distribuidor)
    {
        $query->select(
            'sis_clientes.sis_clientesid',
            'sis_clientes.identificacion',
            'sis_clientes.nombres',
            'sis_clientes.telefono1',
            'sis_clientes.telefono2',
            'sis_clientes.correos',
            'sis_clientes.grupo',
            'sis_licencias.tipo_licencia',
            DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechainicia) as fechainicia'),
            DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechacaduca) as fechacaduca'),
            DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechaactulizaciones) as fechaactulizaciones'),
            DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechaultimopago) as fechaultimopago'),
            DB::RAW('DATEDIFF(sis_licencias.fechacaduca,NOW()) as diasvencer'),
            'sis_licencias.numerocontrato',
            'sis_licencias.precio',
            'sis_licencias.periodo',
            'sis_licencias.producto',
            'sis_clientes.red_origen',
            'sis_clientes.sis_distribuidoresid',
            'sis_clientes.sis_vendedoresid',
            'sis_clientes.sis_revendedoresid',
            'sis_clientes.provinciasid',
            'sis_clientes.ciudadesid',
            'sis_licencias.empresas',
            'sis_licencias.usuarios',
            'sis_licencias.numeroequipos',
            'sis_licencias.numeromoviles',
            'sis_clientes.usuariocreacion',
            'sis_clientes.usuariomodificacion',
            'sis_clientes.fechacreacion',
            'sis_clientes.fechamodificacion',
            'sis_licencias.modulopractico',
            'sis_licencias.modulocontrol',
            'sis_licencias.modulocontable'
        );
        if ($tipo == "Todos") {
            $query->leftJoin('sis_licencias', 'sis_licencias.sis_clientesid', 'sis_clientes.sis_clientesid');
            if ($distribuidor > 0) {
                $query->where('sis_clientes.sis_distribuidoresid', $distribuidor);
            }
            $query->groupBy('sis_clientes.sis_clientesid');
        } else {
            $query->join('sis_licencias', 'sis_licencias.sis_clientesid', 'sis_clientes.sis_clientesid');
            if ($distribuidor > 0) {
                $query->where('sis_clientes.sis_distribuidoresid', $distribuidor);
            }
        }
        return $query;
    }
}
