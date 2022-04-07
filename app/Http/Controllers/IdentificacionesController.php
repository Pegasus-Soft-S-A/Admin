<?php

namespace App\Http\Controllers;

use App\Models\Identificaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IdentificacionesController extends Controller
{
    public function index(Request $request)
    {
        $identificacionIngresada = substr($request->identificacion, 0, 10);
        $buscar = Identificaciones::where(DB::raw('substr(identificacion, 1, 10)'), $identificacionIngresada)->first();
        if (!$buscar) {
            $buscar = array("razon_social" => "");
        }
        return json_encode($buscar);
    }

    public function actualiza(Request $request)
    {
        $identificacionIngresada = substr($request->identificacion, 0, 10);
        $buscar = Identificaciones::whereIn('identificacion', [$identificacionIngresada, $request->identificacion, $request->identificacion . '001'])->first();
        if ($buscar) {

            $buscar->direccion =  $request->direccion == null ? "" : $request->direccion;
            $buscar->correo = $request->correo == null ? "" : $request->correo;
            $buscar->provinciasid = $request->provinciasid == null ? "" : $request->provinciasid;
            $buscar->ciudadesid =  $request->ciudadesid == null ? "" : $request->ciudadesid;
            $buscar->parroquiasid =  $request->parroquiasid == null ? "" : $request->parroquiasid;
            $buscar->telefono1 = $request->telefono1 == null ? "" : $request->telefono1;
            $buscar->telefono2 =  $request->telefono2 == null ? "" : $request->telefono2;
            $buscar->telefono3 = $request->telefono3 == null ? "" : $request->telefono3;

            $buscar->save();
            return json_encode($buscar);
        } else {
            $buscar = new Identificaciones();

            if (strlen($request->identificacion) == 10) {
                $buscar->tipo_identificacion  = 'C';
            } elseif (strlen($request->identificacion) == 13) {
                $buscar->tipo_identificacion = 'R';
            }

            $buscar->identificacion =  $request->identificacion == null ? "" : $request->identificacion;
            $buscar->razon_social =  $request->razon_social == null ? "" : $request->razon_social;
            $buscar->nombre_comercial =  $request->nombre_comercial == null ? "" : $request->nombre_comercial;
            $buscar->direccion =  $request->direccion == null ? "" : $request->direccion;
            $buscar->correo = $request->correo == null ? "" : $request->correo;
            $buscar->provinciasid = $request->provinciasid == null ? "" : $request->provinciasid;
            $buscar->ciudadesid =  $request->ciudadesid == null ? "" : $request->ciudadesid;
            $buscar->parroquiasid =  $request->parroquiasid == null ? "" : $request->parroquiasid;
            $buscar->telefono1 = $request->telefono1 == null ? "" : $request->telefono1;
            $buscar->telefono2 =  $request->telefono2 == null ? "" : $request->telefono2;
            $buscar->telefono3 = $request->telefono3 == null ? "" : $request->telefono3;
            $buscar->tipo_contribuyente = $request->tipo_contribuyente == null ? "" : $request->tipo_contribuyente;
            $buscar->obligado = $request->obligado == null ? "" : $request->obligado;

            $buscar->save();
            return json_encode($buscar);
        }
    }
}
