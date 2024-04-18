<?php

namespace App\Http\Controllers;

use App\Exports\LicenciasExport;
use App\Exports\RespaldosExport;
use App\Models\Licencias;
use Carbon\Carbon;
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
}
