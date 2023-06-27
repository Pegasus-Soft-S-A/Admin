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

    //$trae todas las licencias web y pc, $distribuidor 0 para traer todos caso contrario trae solo los del distribuidor
    public function scopeClientes($query, $distribuidor = 0, $busqueda = '')
    {
        $query = "SELECT * FROM (SELECT
        sis_clientes.sis_clientesid,
        sis_clientes.identificacion,
        sis_clientes.nombres,
        sis_clientes.telefono1,
        sis_clientes.telefono2,
        sis_clientes.correos,
        sis_clientes.grupo,
        sis_licencias.tipo_licencia,
        UNIX_TIMESTAMP( sis_licencias.fechainicia ) AS fechainicia,
        UNIX_TIMESTAMP( sis_licencias.fechacaduca ) AS fechacaduca,
        UNIX_TIMESTAMP( sis_licencias.fechaactulizaciones ) AS fechaactulizaciones,
        UNIX_TIMESTAMP( sis_licencias.fechaultimopago ) AS fechaultimopago,
        DATEDIFF(
            sis_licencias.fechacaduca,
        NOW()) AS diasvencer,
        sis_licencias.numerocontrato,
        sis_licencias.precio,
        sis_licencias.periodo,
        sis_licencias.producto,
        sis_clientes.red_origen,
        sis_clientes.sis_distribuidoresid,
        sis_clientes.sis_vendedoresid,
        sis_clientes.sis_revendedoresid,
        sis_clientes.provinciasid,
        sis_clientes.ciudadesid,
        sis_licencias.empresas,
        sis_licencias.usuarios,
        sis_licencias.numeroequipos,
        sis_licencias.numeromoviles,
        sis_clientes.usuariocreacion,
        sis_clientes.usuariomodificacion,
        sis_clientes.fechacreacion,
        sis_clientes.fechamodificacion,
        sis_licencias.modulopractico,
        sis_licencias.modulocontrol,
        sis_licencias.modulocontable,
        sis_licencias.cantidadempresas
    FROM
        sis_licencias
        INNER JOIN sis_clientes ON sis_licencias.sis_clientesid = sis_clientes.sis_clientesid UNION
    SELECT
        sis_clientes.sis_clientesid,
        sis_clientes.identificacion,
        sis_clientes.nombres,
        sis_clientes.telefono1,
        sis_clientes.telefono2,
        sis_clientes.correos,
        sis_clientes.grupo,
        sis_licencias_vps.tipo_licencia,
        '' AS fechainicia,
        UNIX_TIMESTAMP( sis_licencias_vps.fecha_corte_cliente ) AS fechacaduca,
        '' AS fechaactulizaciones,
        '' AS fechaultimopago,
        '' AS diasvencer,
        sis_licencias_vps.numerocontrato,
        '' AS precio,
        '' AS periodo,
        '' AS producto,
        sis_clientes.red_origen,
        sis_clientes.sis_distribuidoresid,
        sis_clientes.sis_vendedoresid,
        sis_clientes.sis_revendedoresid,
        sis_clientes.provinciasid,
        sis_clientes.ciudadesid,
        '' AS empresas,
        '' AS usuarios,
        '' AS numeroequipos,
        '' AS numeromoviles,
        sis_clientes.usuariocreacion,
        sis_clientes.usuariomodificacion,
        sis_clientes.fechacreacion,
        sis_clientes.fechamodificacion,
        '' AS modulopractico,
        '' AS modulocontrol,
        '' AS modulocontable,
        '' AS cantidadempresas
    FROM
        sis_licencias_vps
        INNER JOIN sis_clientes ON sis_licencias_vps.sis_clientesid = sis_clientes.sis_clientesid UNION
    SELECT
        sis_clientes.sis_clientesid,
        sis_clientes.identificacion,
        sis_clientes.nombres,
        sis_clientes.telefono1,
        sis_clientes.telefono2,
        sis_clientes.correos,
        sis_clientes.grupo,
        sis_licencias_web.tipo_licencia,
        UNIX_TIMESTAMP( sis_licencias_web.fechainicia ) AS fechainicia,
        UNIX_TIMESTAMP( sis_licencias_web.fechacaduca ) AS fechacaduca,
        UNIX_TIMESTAMP( sis_licencias_web.fechaactulizaciones ) AS fechaactulizaciones,
        UNIX_TIMESTAMP( sis_licencias_web.fechaultimopago ) AS fechaultimopago,
        DATEDIFF(
            sis_licencias_web.fechacaduca,
        NOW()) AS diasvencer,
        sis_licencias_web.numerocontrato,
        sis_licencias_web.precio,
        sis_licencias_web.periodo,
        sis_licencias_web.producto,
        sis_clientes.red_origen,
        sis_clientes.sis_distribuidoresid,
        sis_clientes.sis_vendedoresid,
        sis_clientes.sis_revendedoresid,
        sis_clientes.provinciasid,
        sis_clientes.ciudadesid,
        sis_licencias_web.empresas,
        sis_licencias_web.usuarios,
        sis_licencias_web.numeroequipos,
        sis_licencias_web.numeromoviles,
        sis_clientes.usuariocreacion,
        sis_clientes.usuariomodificacion,
        sis_clientes.fechacreacion,
        sis_clientes.fechamodificacion,
        sis_licencias_web.modulopractico,
        sis_licencias_web.modulocontrol,
        sis_licencias_web.modulocontable,
        '' AS cantidadempresas
    FROM
        sis_clientes
        INNER JOIN sis_licencias_web ON sis_licencias_web.sis_clientesid = sis_clientes.sis_clientesid UNION
    SELECT
        sis_clientes.sis_clientesid,
        sis_clientes.identificacion,
        sis_clientes.nombres,
        sis_clientes.telefono1,
        sis_clientes.telefono2,
        sis_clientes.correos,
        sis_clientes.grupo,
        '' AS tipo_licencia,
        '' AS fechainicia,
        '' AS fechacaduca,
        '' AS fechaactulizaciones,
        '' AS fechaultimopago,
        '' AS diasvencer,
        '' AS numerocontrato,
        '' AS precio,
        '' AS periodo,
        '' AS producto,
        sis_clientes.red_origen,
        sis_clientes.sis_distribuidoresid,
        sis_clientes.sis_vendedoresid,
        sis_clientes.sis_revendedoresid,
        sis_clientes.provinciasid,
        sis_clientes.ciudadesid,
        '' AS empresas,
        '' AS usuarios,
        '' AS numeroequipos,
        '' AS numeromoviles,
        sis_clientes.usuariocreacion,
        sis_clientes.usuariomodificacion,
        sis_clientes.fechacreacion,
        sis_clientes.fechamodificacion,
        '' AS modulopractico,
        '' AS modulocontrol,
        '' AS modulocontable,
        '' AS cantidadempresas
    FROM
        sis_clientes
    WHERE
        NOT EXISTS ( SELECT 1 FROM sis_licencias WHERE sis_licencias.sis_clientesid = sis_clientes.sis_clientesid )
        AND NOT EXISTS ( SELECT 1 FROM sis_licencias_web WHERE sis_licencias_web.sis_clientesid = sis_clientes.sis_clientesid )) as U";

        //Iniciar la variable para saber si ya se ha usado WHERE
        $usedWhere = false;

        //Si el distribuidor es diferente de cero se agrega la condicion
        if ($distribuidor <> 0) {
            $query .= " WHERE U.sis_distribuidoresid = $distribuidor";
            $usedWhere = true; //Marcamos que ya hemos usado WHERE
        }

        //Si existe una busqueda se agrega la condicion
        if ($busqueda <> "") {
            //Separar las palabras de la busqueda
            $keywords = explode(" ", $busqueda);
            //Si ya hemos usado WHERE, añadimos AND, si no, añadimos WHERE
            $query .= $usedWhere ? " AND (" : " WHERE ";
            $i = 0;
            foreach ($keywords as $keyword) {
                if ($i == 0) {
                    $query .= " U.identificacion LIKE '%$keyword%'  or U.numerocontrato LIKE '%$keyword%'  or U.nombres LIKE '%$keyword%'";
                } else {
                    $query .= " OR U.identificacion LIKE '%$keyword%'  or U.numerocontrato LIKE '%$keyword%'  or U.nombres LIKE '%$keyword%'";
                }
                $i++;
            }

            //Cerramos el paréntesis si hemos añadido AND al inicio
            if ($usedWhere) {
                $query .= " )";
            }
        }

        $query .= " ORDER BY U.sis_clientesid DESC";
        return  DB::select($query);
    }
}
