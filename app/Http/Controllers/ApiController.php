<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\MovilVersion;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function plan_soporte(Request $request)
    {
        $web = Clientes::select('sis_clientes.identificacion', 'sis_clientes.nombres', 'sis_licencias_web.numerocontrato', 'sis_licencias_web.producto', 'sis_licencias_web.tipo_licencia', 'sis_clientes.sis_distribuidoresid', 'sis_servidores.dominio')
            ->join('sis_licencias_web', 'sis_licencias_web.sis_clientesid', 'sis_clientes.sis_clientesid')
            ->join('sis_servidores', 'sis_servidores.sis_servidoresid', 'sis_licencias_web.sis_servidoresid')
            ->where('sis_licencias_web.numerocontrato', $request->numerocontrato)
            ->first();

        if ($web) {
            $web->producto = $web->producto == 12 ? "Facturito" : "Web";
            $web->tipo_licencia = "Web";
            return response()->json($web);
        }

        $pc = Clientes::select('sis_clientes.identificacion', 'sis_clientes.nombres', 'sis_licencias.numerocontrato', 'sis_licencias.tipo_licencia', 'sis_licencias.plan_soporte', 'sis_licencias.fechacaduca_soporte', 'sis_clientes.sis_distribuidoresid')
            ->join('sis_licencias', 'sis_licencias.sis_clientesid', 'sis_clientes.sis_clientesid')
            ->where('sis_licencias.numerocontrato', $request->numerocontrato)
            ->first();

        if ($pc) {
            $pc->tipo_licencia = "PC";
            return response()->json($pc);
        }

        return response()->json(['error' => 'No se encontraron resultados']);
    }

    public function movil_versiones(Request $request)
    {
        $versiones = MovilVersion::where('movil_versionesid', $request->movil_versionesid)
            ->first();

        return response()->json($versiones);
    }

    public function update_versiones(Request $request)
    {
        $version = MovilVersion::where('movil_versionesid', $request->movil_versionesid)
            ->first();

        if ($version) {
            $version->version_ios = $request->version_ios;
            $version->version_android = $request->version_android;
            $version->save();
        }

        return response()->json($version);
    }
}
