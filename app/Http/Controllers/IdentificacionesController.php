<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Identificaciones;
use App\Models\Licencias;
use App\Models\Servidores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class IdentificacionesController extends Controller
{
    public function index(Request $request)
    {
        $identificacionIngresada = substr($request->identificacion, 0, 10);
        $buscar = Identificaciones::where(DB::raw('substr(identificacion, 1, 10)'), $identificacionIngresada)->first();
        if (!$buscar) {
            $buscar = array("razon_social" => "");
        } else {
            $buscar->parametros_json = json_decode($buscar->parametros_json);
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
            $buscar->parametros_json = json_decode($buscar->parametros_json);
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
            $buscar->parametros_json = json_decode($buscar->parametros_json);
            return json_encode($buscar);
        }
    }

    public function servidores(Request $request)
    {
        $identificacionIngresada = substr($request->identificacion, 0, 10);
        $usuario = Clientes::select('sis_clientesid')->where(DB::raw('substr(identificacion, 1, 10)'), $identificacionIngresada)->first();
        $servidores = Servidores::where('estado', 1)->get();
        $array = [];
        if ($usuario) {
            foreach ($servidores as  $servidor) {
                $url = $servidor->dominio . '/registros/consulta_licencia';
                $resultado = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                    ->withOptions(["verify" => false])
                    ->post($url, ['sis_clientesid' => $usuario->sis_clientesid])
                    ->json();

                if (isset($resultado['licencias'])) {

                    $array[] = ["sis_servidoresid" => $servidor->sis_servidoresid, "descripcion" => $servidor->descripcion, "dominio" => $servidor->dominio];
                }
            }

            if (count($array) > 0) {

                $servidoresJson = json_encode(["servidor" => $array]);
                return $servidoresJson;
            } else {
                return json_encode(["servidor" => 0]);
            }
        } else {
            return json_encode(["servidor" => 0]);
        }
    }

    public function servidores_activos()
    {
        $servidores = Servidores::where('estado', 1)
            ->where('sis_servidoresid', '!=', 3)
            ->get();
        return json_encode($servidores);
    }

    public function consultar_validado(Request $request)
    {
        $identificacionIngresada = substr($request->identificacion, 0, 10);
        $cliente = Clientes::where(DB::raw('substr(identificacion, 1, 10)'), $identificacionIngresada)
            ->first();
        return json_encode($cliente);
    }

    public function validar_datos(Request $request)
    {
        $respuesta = [];

        $url = 'https://emailvalidation.abstractapi.com/v1/?api_key=fae435e4569b4c93ac34e0701100778c&email=' . $request->correo;
        $correo = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->get($url)
            ->json();
        if ($correo['deliverability'] != "DELIVERABLE") {
            if ($correo['is_valid_format']['value'] == true &&  (substr($request->correo, strpos($request->correo, '@') + 1, strlen($request->correo)) == 'hotmail.com') || substr($request->correo, strpos($request->correo, '@') + 1, strlen($request->correo)) == 'outlook.com') {
                //consultar api2 si es hotmail
                $url = 'https://api.debounce.io/v1/?email=' . rawurlencode($request->correo) . '&api=6269b53f06aeb';
                $correo = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                    ->withOptions(["verify" => false])
                    ->get($url)
                    ->json();
                if ($correo['debounce']['result'] != "Safe to Send") {
                    $respuesta = ["resultado" => "El correo ingresado no es válido"];
                    return json_encode($respuesta);
                }
            } else {
                $respuesta = ["resultado" => "El correo ingresado no es válido"];
                return json_encode($respuesta);
            }
        }

        //consultar api1
        $url = 'https://phonevalidation.abstractapi.com/v1/?api_key=7678748c57244785bc99109520e35d5f&phone=593' . $request->celular;
        $celular = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->get($url)
            ->json();

        if (!isset($celular['resultado'])) {
            if ($celular['valid'] != true) {
                $respuesta = ["resultado" => "El celular ingresado no es válido"];
                return json_encode($respuesta);
            }
        }

        //Actualizar 
        DB::beginTransaction();
        try {
            $servidores = Servidores::where('estado', 1)->get();
            $cliente = Clientes::where('sis_clientesid', $request->sis_clientesid)->first();
            $cliente->correos = $request->correo;
            $cliente->telefono2 = $request->celular;
            $cliente->validado = 1;
            $cliente->save();

            foreach ($servidores as $servidor) {

                $urlEditar = $servidor->dominio . '/registros/editar_clientes';
                $clienteEditar = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                    ->withOptions(["verify" => false])
                    ->post($urlEditar, $cliente->toArray())
                    ->json();

                if (!isset($clienteEditar['sis_clientes'])) {
                    DB::rollBack();
                    $respuesta = ["resultado" => "Ocurrió un error, vuelta a intentarlo"];
                    return json_encode($respuesta);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $respuesta = ["resultado" => "Ocurrió un error, vuelta a intentarlo"];
            return json_encode($respuesta);
        };

        $respuesta = ["resultado" => "Ok"];
        return json_encode($respuesta);
    }

    public function licencia_consulta(Request $request)
    {
        $licencia = Licencias::where('numerocontrato', $request->numerocontrato)->first();
        return json_encode(["licencia" => [$licencia]]);
    }

    public function licencia_actualiza(Request $request)
    {
        $licencia = Licencias::where('numerocontrato', $request->numerocontrato)->first();
        $licencia->tokenrespaldo = $request->token;
        $licencia->save();
        return json_encode(["licencia" => [$licencia]]);
    }
}
