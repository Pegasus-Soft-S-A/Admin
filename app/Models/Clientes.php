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

    /**
     * Scope para obtener clientes con sus licencias (web, vps y regulares)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $distribuidor ID del distribuidor (0 para todos)
     * @param string $busqueda Término de búsqueda
     * @return \Illuminate\Support\Collection
     */
    public function scopeClientes($query, $distribuidor = 0, $busqueda = '')
    {
        // Consulta unificada usando Query Builder en lugar de SQL directo
        $result = DB::query()
            ->fromSub(function ($query) {
                // Primera subconsulta: clientes con licencias regulares
                $pcLicenses = $this->getClientsWithPCLicenses();

                // Segunda subconsulta: clientes con licencias VPS
                $vpsLicenses = $this->getClientsWithVpsLicenses();

                // Tercera subconsulta: clientes con licencias web
                $webLicenses = $this->getClientsWithWebLicenses();

                // Cuarta subconsulta: clientes sin licencias
                $noLicenses = $this->getClientsWithoutLicenses();

                // Combinar todas las subconsultas con UNION
                return $query->fromSub($pcLicenses, 'reg')
                    ->union($vpsLicenses)
                    ->union($webLicenses)
                    ->union($noLicenses);
            }, 'U');

        // Filtrar por distribuidor si se especifica
        if ($distribuidor != 0) {
            $result->where('U.sis_distribuidoresid', $distribuidor);
        }

        // Filtrar por términos de búsqueda si se proporcionan
        if (!empty($busqueda)) {
            $keywords = explode(' ', trim($busqueda));

            $result->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('U.identificacion', 'like', "%{$keyword}%")
                        ->orWhere('U.numerocontrato', 'like', "%{$keyword}%")
                        ->orWhere('U.nombres', 'like', "%{$keyword}%")
                        ->orWhere('U.telefono2', 'like', "%{$keyword}%")
                        ->orWhere('U.Identificador', 'like', "%{$keyword}%")
                        ->orWhere('U.correos', 'like', "%{$keyword}%");
                }
            });
        }

        // Ordenar por ID de cliente en orden descendente
        $result->orderBy('U.sis_clientesid', 'desc');

        return $result->get();
    }

    /**
     * Obtiene los clientes con licencias PC
     * 
     * @return \Illuminate\Database\Query\Builder
     */
    private function getClientsWithPCLicenses()
    {
        return DB::table('sis_licencias')
            ->join('sis_clientes', 'sis_licencias.sis_clientesid', '=', 'sis_clientes.sis_clientesid')
            ->select([
                'sis_clientes.sis_clientesid',
                'sis_clientes.identificacion',
                'sis_clientes.nombres',
                'sis_clientes.telefono1',
                'sis_clientes.telefono2',
                'sis_clientes.correos',
                'sis_clientes.grupo',
                'sis_licencias.tipo_licencia',
                DB::raw('UNIX_TIMESTAMP(sis_licencias.fechainicia) AS fechainicia'),
                DB::raw('UNIX_TIMESTAMP(sis_licencias.fechacaduca) AS fechacaduca'),
                DB::raw('UNIX_TIMESTAMP(sis_licencias.fechaactulizaciones) AS fechaactulizaciones'),
                DB::raw('UNIX_TIMESTAMP(sis_licencias.fechaultimopago) AS fechaultimopago'),
                DB::raw('DATEDIFF(sis_licencias.fechacaduca, NOW()) AS diasvencer'),
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
                'sis_licencias.modulocontable',
                'sis_licencias.modulonube',
                'sis_licencias.tipo_nube',
                'sis_licencias.nivel_nube',
                'sis_licencias.cantidadempresas',
                'sis_clientes.validado',
                'sis_licencias.Identificador',
                'sis_licencias.usuarios_activos'
            ]);
    }

    /**
     * Obtiene los clientes con licencias VPS
     * 
     * @return \Illuminate\Database\Query\Builder
     */
    private function getClientsWithVpsLicenses()
    {
        return DB::table('sis_licencias_vps')
            ->join('sis_clientes', 'sis_licencias_vps.sis_clientesid', '=', 'sis_clientes.sis_clientesid')
            ->select([
                'sis_clientes.sis_clientesid',
                'sis_clientes.identificacion',
                'sis_clientes.nombres',
                'sis_clientes.telefono1',
                'sis_clientes.telefono2',
                'sis_clientes.correos',
                'sis_clientes.grupo',
                'sis_licencias_vps.tipo_licencia',
                DB::raw("'' AS fechainicia"),
                DB::raw('UNIX_TIMESTAMP(sis_licencias_vps.fecha_corte_cliente) AS fechacaduca'),
                DB::raw("'' AS fechaactulizaciones"),
                DB::raw("'' AS fechaultimopago"),
                DB::raw("'' AS diasvencer"),
                'sis_licencias_vps.numerocontrato',
                DB::raw("'' AS precio"),
                DB::raw("'' AS periodo"),
                DB::raw("'' AS producto"),
                'sis_clientes.red_origen',
                'sis_clientes.sis_distribuidoresid',
                'sis_clientes.sis_vendedoresid',
                'sis_clientes.sis_revendedoresid',
                'sis_clientes.provinciasid',
                'sis_clientes.ciudadesid',
                DB::raw("'' AS empresas"),
                DB::raw("'' AS usuarios"),
                DB::raw("'' AS numeroequipos"),
                DB::raw("'' AS numeromoviles"),
                'sis_clientes.usuariocreacion',
                'sis_clientes.usuariomodificacion',
                'sis_clientes.fechacreacion',
                'sis_clientes.fechamodificacion',
                DB::raw("'' AS modulopractico"),
                DB::raw("'' AS modulocontrol"),
                DB::raw("'' AS modulocontable"),
                DB::raw("'' AS modulonube"),
                DB::raw("'' AS tipo_nube"),
                DB::raw("'' AS nivel_nube"),
                DB::raw("'' AS cantidadempresas"),
                'sis_clientes.validado',
                DB::raw("'' AS Identificador"),
                DB::raw("'' AS usuarios_activos")
            ]);
    }

    /**
     * Obtiene los clientes con licencias web
     * 
     * @return \Illuminate\Database\Query\Builder
     */
    private function getClientsWithWebLicenses()
    {
        return DB::table('sis_clientes')
            ->join('sis_licencias_web', 'sis_licencias_web.sis_clientesid', '=', 'sis_clientes.sis_clientesid')
            ->select([
                'sis_clientes.sis_clientesid',
                'sis_clientes.identificacion',
                'sis_clientes.nombres',
                'sis_clientes.telefono1',
                'sis_clientes.telefono2',
                'sis_clientes.correos',
                'sis_clientes.grupo',
                'sis_licencias_web.tipo_licencia',
                DB::raw('UNIX_TIMESTAMP(sis_licencias_web.fechainicia) AS fechainicia'),
                DB::raw('UNIX_TIMESTAMP(sis_licencias_web.fechacaduca) AS fechacaduca'),
                DB::raw('UNIX_TIMESTAMP(sis_licencias_web.fechaactulizaciones) AS fechaactulizaciones'),
                DB::raw('UNIX_TIMESTAMP(sis_licencias_web.fechaultimopago) AS fechaultimopago'),
                DB::raw('DATEDIFF(sis_licencias_web.fechacaduca, NOW()) AS diasvencer'),
                'sis_licencias_web.numerocontrato',
                'sis_licencias_web.precio',
                'sis_licencias_web.periodo',
                'sis_licencias_web.producto',
                'sis_clientes.red_origen',
                'sis_clientes.sis_distribuidoresid',
                'sis_clientes.sis_vendedoresid',
                'sis_clientes.sis_revendedoresid',
                'sis_clientes.provinciasid',
                'sis_clientes.ciudadesid',
                'sis_licencias_web.empresas',
                'sis_licencias_web.usuarios',
                'sis_licencias_web.numeroequipos',
                'sis_licencias_web.numeromoviles',
                'sis_clientes.usuariocreacion',
                'sis_clientes.usuariomodificacion',
                'sis_clientes.fechacreacion',
                'sis_clientes.fechamodificacion',
                'sis_licencias_web.modulopractico',
                'sis_licencias_web.modulocontrol',
                'sis_licencias_web.modulocontable',
                DB::raw("'' AS modulonube"),
                DB::raw("'' AS tipo_nube"),
                DB::raw("'' AS nivel_nube"),
                DB::raw("'' AS cantidadempresas"),
                'sis_clientes.validado',
                DB::raw("'' AS Identificador"),
                DB::raw("'' AS usuarios_activos")
            ]);
    }

    /**
     * Obtiene los clientes sin licencias
     * 
     * @return \Illuminate\Database\Query\Builder
     */
    private function getClientsWithoutLicenses()
    {
        return DB::table('sis_clientes')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('sis_licencias')
                    ->whereColumn('sis_licencias.sis_clientesid', 'sis_clientes.sis_clientesid');
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('sis_licencias_web')
                    ->whereColumn('sis_licencias_web.sis_clientesid', 'sis_clientes.sis_clientesid');
            })
            ->select([
                'sis_clientes.sis_clientesid',
                'sis_clientes.identificacion',
                'sis_clientes.nombres',
                'sis_clientes.telefono1',
                'sis_clientes.telefono2',
                'sis_clientes.correos',
                'sis_clientes.grupo',
                DB::raw("'' AS tipo_licencia"),
                DB::raw("'' AS fechainicia"),
                DB::raw("'' AS fechacaduca"),
                DB::raw("'' AS fechaactulizaciones"),
                DB::raw("'' AS fechaultimopago"),
                DB::raw("'' AS diasvencer"),
                DB::raw("'' AS numerocontrato"),
                DB::raw("'' AS precio"),
                DB::raw("'' AS periodo"),
                DB::raw("'' AS producto"),
                'sis_clientes.red_origen',
                'sis_clientes.sis_distribuidoresid',
                'sis_clientes.sis_vendedoresid',
                'sis_clientes.sis_revendedoresid',
                'sis_clientes.provinciasid',
                'sis_clientes.ciudadesid',
                DB::raw("'' AS empresas"),
                DB::raw("'' AS usuarios"),
                DB::raw("'' AS numeroequipos"),
                DB::raw("'' AS numeromoviles"),
                'sis_clientes.usuariocreacion',
                'sis_clientes.usuariomodificacion',
                'sis_clientes.fechacreacion',
                'sis_clientes.fechamodificacion',
                DB::raw("'' AS modulopractico"),
                DB::raw("'' AS modulocontrol"),
                DB::raw("'' AS modulocontable"),
                DB::raw("'' AS modulonube"),
                DB::raw("'' AS tipo_nube"),
                DB::raw("'' AS nivel_nube"),
                DB::raw("'' AS cantidadempresas"),
                'sis_clientes.validado',
                DB::raw("'' AS Identificador"),
                DB::raw("'' AS usuarios_activos")
            ]);
    }
}
