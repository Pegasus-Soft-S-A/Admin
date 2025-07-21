<?php

namespace App\Http\Controllers;

use App\Exports\LicenciasExport;
use App\Exports\RespaldosExport;
use App\Models\Clientes;
use App\Models\Distribuidores;
use App\Models\Grupos;
use App\Models\Licencias;
use App\Models\Revendedores;
use App\Models\Servidores;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public function index()
    {

        // Fecha de hoy y ayer para comparaciones
        $fechaHoy = Carbon::today()->toDateString();
        $fechaAyer = Carbon::yesterday()->toDateString();

        $licencias = Licencias::selectRaw('version_ejecutable, count(*) as total')
            ->groupBy('version_ejecutable')
            ->get();

        $respaldo = Licencias::selectRaw("
        CASE
            WHEN DATE(fecha_respaldo) = '{$fechaHoy}' OR DATE(fecha_respaldo) = '{$fechaAyer}' THEN 'Ayer y Hoy'
            WHEN DATE(fecha_respaldo) < '{$fechaAyer}' THEN 'Mas de 2 dias'
            ELSE 'Fecha por Defecto'
        END AS grupo,
        COUNT(*) as total")
            ->groupBy('Grupo')
            ->get();

        $data = [
            "ejecutable" => [
                "labels" => [],
                "values" => [],
            ],
            "respaldo" => [
                "labels" => [],
                "values" => [],
            ]
        ];

        foreach ($licencias as $item) {
            $texto = $item->version_ejecutable == "" ? "Sin version" : $item->version_ejecutable;
            array_push($data["ejecutable"]["labels"], strtoupper($texto));
            array_push($data["ejecutable"]["values"], $item->total);
        }

        foreach ($respaldo as $item) {
            array_push($data["respaldo"]["labels"], $item->grupo);
            array_push($data["respaldo"]["values"], $item->total);
        }

        return view('admin.reportes.reportes', ['data' => $data]);
    }

    public function reporte()
    {
        $licencias = Licencias::selectRaw('version_ejecutable', 'count(*) as total')
            ->groupBy('version')
            ->get();

        $data = [
            "ejecutable" => [
                "labels" => [],
                "values" => [],
            ]
        ];

        foreach ($licencias as $item) {
            $texto = $item->version_ejecutable == "" ? "Sin versión" : $item->version_ejecutable;
            array_push($data["ejecutable"]["labels"], strtoupper($$item->version_ejecutable));
            array_push($data["ejecutable"]["values"], $item->total);
        }

        return $data;
    }

    public function export_versiones()
    {
        return Excel::download(new LicenciasExport, 'Licencias-versiones-antiguas.xlsx');
    }

    public function export_respaldos()
    {
        return Excel::download(new RespaldosExport, 'Respaldos-antiguos.xlsx');
    }

    //API
    public function datos_powerbi()
    {
        $servidores = Servidores::where('estado', 1)->get();
        $web = [];
        $distribuidores = Distribuidores::pluck('sis_distribuidoresid', 'razonsocial')->toArray();
        $grupos = Grupos::all()->toArray();
        $vendedores = Revendedores::all()->toArray();

        $licencias = collect(Clientes::Clientes(0, ''));

        $licencias->map(function ($licencia) use ($distribuidores, $vendedores, $grupos) {
            $licencia->sis_distribuidoresid = array_search($licencia->sis_distribuidoresid, $distribuidores);
            $licencia->sis_vendedoresid = $vendedores[array_search($licencia->sis_vendedoresid, array_column($vendedores, 'sis_revendedoresid'))]['razonsocial'];
            $licencia->sis_revendedoresid = $vendedores[array_search($licencia->sis_revendedoresid, array_column($vendedores, 'sis_revendedoresid'))]['razonsocial'];
            $licencia->grupo = array_search($licencia->grupo, array_column($grupos, 'gruposid')) ? $grupos[array_search($licencia->grupo, array_column($grupos, 'gruposid'))]['descripcion'] : 'Ninguno';
            $licencia->fechainicia = $licencia->fechainicia == null ? date('d-m-Y', strtotime(now())) : date('d-m-Y', $licencia->fechainicia);
            $licencia->fechacaduca = $licencia->fechacaduca == null ? date('d-m-Y', strtotime(now())) : date('d-m-Y', $licencia->fechacaduca);
            $licencia->fechaultimopago = $licencia->fechaultimopago == null ? date('d-m-Y', strtotime(now())) : date('d-m-Y', $licencia->fechaultimopago);
            $licencia->fechaactulizaciones = $licencia->fechaactulizaciones == null ? date('d-m-Y', strtotime(now())) : date('d-m-Y', $licencia->fechaactulizaciones);
            $licencia->periodo = $licencia->periodo == 1 ? "Mensual" : "Anual";
            $licencia->producto = $this->producto($licencia);
            $licencia->tipo_licencia = $licencia->tipo_licencia == 1 ? "Web" : "PC";
            $licencia->red_origen = $this->origen($licencia);
            $licencia->provinciasid = $this->provincias($licencia);
            $licencia->precio = number_format(floatval($licencia->precio), 2, ',', '.');
            unset($licencia->ciudadesid);
            return $licencia;
        });

        $licencias = $licencias->toArray();
        return response()->json(["ventas" => $licencias]);
    }

    public function gastosFacebook($inicio, $fin)
    {
        $resultado = Http::withHeaders([
            'Authorization' => 'Bearer ' . 'EAAMNIHFYKQwBAKmUAGKPFLqlZCsu6IVbGRF7WZCfkFe7HrPpGFGzwd7O5PgYkqlVROl2rFlY9GHKKdFS7jREsrwRwMXOZCHPS1e9G421xHAAzAhZBgijt2MQ7LxPCzblXIZBTTr0KZAinQya0sW2dreWFyJIC1BuW9My7ebx6ZBsBARpBS16SATsyh2Pme3WvZA04ptrNA684gZDZD',
            'Facebook-App-Id' => '858857921849612',
            'Facebook-App-Secret' => 'a05faf55b6e2b9dc787620a35f0418cb',
        ])
            ->withOptions(["verify" => false])
            ->get("https://graph.facebook.com/v16.0/act_347213498749913/insights?level=campaign&fields=campaign_name,adset_name,ad_name,spend,actions&time_range={since:'$inicio',until:'$fin'}")
            ->json();

        $data = collect(json_decode(json_encode($resultado['data']), true));

        return response()->json([$data]);
    }
}
