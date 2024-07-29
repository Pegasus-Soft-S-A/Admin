<?php

namespace App\Http\Controllers;

use App\Mail\enviarlicencia;
use App\Mail\vendedor;
use App\Models\Clientes;
use App\Models\Distribuidores;
use App\Models\Grupos;
use App\Models\Identificaciones;
use App\Models\Licencias;
use App\Models\Licenciasweb;
use App\Models\Log;
use App\Models\MovilVersion;
use App\Models\Notificaciones;
use App\Models\Revendedores;
use App\Models\Servidores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use SimpleXMLElement;
use SoapClient;

class IdentificacionesController extends Controller
{
    public function index(Request $request)
    {
        $identificacionIngresada = substr($request->identificacion, 0, 10);
        $buscar = Identificaciones::whereIn('identificacion', [$identificacionIngresada, $request->identificacion, $request->identificacion . '001'])->first();

        if (!$buscar) {
            try {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'http://merlyna.com/merlyna/abc/webserviceSRI-RegistroCivil.php',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => '<?xml version="1.0" encoding="utf-8"?>
                        <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                        <soap:Body>
                            <nombreCedulaRegistroCivilRequest>
                            <arg0>' . $identificacionIngresada . '</arg0>
                            <arg1>212</arg1>
                            <arg2>1001</arg2>
                            <arg3>1001</arg3>
                            <arg4>perseo</arg4>
                            <arg5>software</arg5>
                            <arg6>IGESeec92e31032ab99345a4d4f3ecea</arg6>
                            </nombreCedulaRegistroCivilRequest>
                        </soap:Body>
                        </soap:Envelope>
        ',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: text/xml; charset=utf-8',
                        'SOAPAction: http://merlyna.com//abc/webserviceSRI-RegistroCivil.php'
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);

                $xml = simplexml_load_string($response);
                $data = $xml->children('SOAP-ENV', true)->Body->children('ns1', true)->nombreCedulaRegistroCivilResponse->children()->return;
                $arrOutput = json_decode(json_encode($data), TRUE);
                //$buscar = array("razon_social" => $arrOutput['0']);

                $request->razon_social =  $arrOutput['0'];
                $request->nombre_comercial =  '';
                $request->direccion =  '';
                $request->correo = '';
                $request->provinciasid = '17';
                $request->ciudadesid =  '1701';
                $request->parroquiasid =  '170150';
                $request->telefono1 = '';
                $request->telefono2 =  '';
                $request->telefono3 = '';
                $request->tipo_contribuyente = '0';
                $request->obligado = '0';

                return json_encode($this->crearIdentificacion($request));
            } catch (\Exception $e) {
            }
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
            return json_encode($this->crearIdentificacion($request));
        }
    }

    public function crearIdentificacion($request)
    {
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

        return $buscar;
    }

    public function servidores(Request $request)
    {
        $identificacionIngresada = substr($request->identificacion, 0, 10);
        $cliente = Clientes::select('sis_clientesid')->where(DB::raw('substr(identificacion, 1, 10)'), $identificacionIngresada)->get();
        $servidores = Servidores::where('estado', 1)->get();
        $array = [];

        foreach ($cliente as $usuario) {
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
        }

        if (count($array) > 0) {
            $servidoresJson = json_encode(["servidor" => $array]);
            return $servidoresJson;
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

    public function servidores_activos1()
    {
        $servidores = Servidores::where('estado', 1)
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
        $url = 'https://emailvalidation.abstractapi.com/v1/?api_key=fae435e4569b4c93ac34e0701100778c&email=' . $request->correo;
        $correo = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->get($url)
            ->json();

        $respuesta = [];

        if ($correo['deliverability'] != "DELIVERABLE") {
            if ($correo['is_valid_format']['value'] == true) {
                $url = 'https://api.debounce.io/v1/?email=' . rawurlencode($request->correo) . '&api=6269b53f06aeb';
                $correo = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                    ->withOptions(["verify" => false])
                    ->get($url)
                    ->json();

                if (!in_array($correo['debounce']['reason'], ["Deliverable", "Deliverable, Role", "Accept All, Role"])) {
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
        $licencia = Licencias::select('sis_licencias.*', "sis_clientes.nombres", "sis_clientes.identificacion", "sis_clientes.correos", "sis_clientes.telefono2")
            ->where('numerocontrato', $request->numerocontrato)
            ->join('sis_clientes', 'sis_clientes.sis_clientesid', 'sis_licencias.sis_clientesid')
            ->first();

        return json_encode(["licencia" => [$licencia]]);
    }

    public function consultar_licencia(Request $request)
    {
        $identificacionIngresada = substr($request->identificacion, 0, 10);
        $cliente = Clientes::whereIn('identificacion', [$identificacionIngresada, $request->identificacion, $request->identificacion . '001'])->first();

        if ($cliente) {

            $data = Licencias::select('sis_licenciasid', 'numerocontrato', 'tipo_licencia', 'fechacaduca', 'sis_clientesid', 'sis_servidoresid')
                ->where('sis_clientesid', $cliente->sis_clientesid)
                ->get();

            if ($data) {
                return json_encode(["licencia" => true, "cliente" => $cliente->nombres]);
            }

            $servidores = Servidores::where('estado', 1)->get();
            $web = [];

            foreach ($servidores as  $servidor) {
                $url = $servidor->dominio . '/registros/consulta_licencia';
                $resultado = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                    ->withOptions(["verify" => false])
                    ->post($url, ['sis_clientesid' => $cliente->sis_clientesid])
                    ->json();
                if (isset($resultado['licencias'])) {
                    $web = array_merge($web, $resultado['licencias']);
                }
            }

            if ($web) {
                return json_encode(["licencia" => true, "cliente" => $cliente->nombres]);
            }
            return json_encode(["licencia" => false]);
        } else {
            return json_encode(["licencia" => false]);
        }
    }

    public function consultar_licencia_web(Request $request)
    {
        $identificacionIngresada = substr($request->identificacion, 0, 10);
        $cliente = Clientes::whereIn('identificacion', [$identificacionIngresada, $request->identificacion, $request->identificacion . '001'])->first();

        if ($cliente) {
            $pc = Licencias::select('sis_licenciasid', 'numerocontrato', 'tipo_licencia', 'fechacaduca', 'sis_clientesid', 'sis_servidoresid')
                ->where('sis_clientesid', $cliente->sis_clientesid)
                ->first();

            if ($pc) {
                return json_encode([
                    "liberar" => false,
                    "accion" => "renovar",
                    "facturito" => false,
                    "id_licencia" => 0,
                    "id_producto" => 0,
                    "numerocontrato" => 0,
                    "id_servidor" => 0,
                ]);
            }

            $servidores = Servidores::where('estado', 1)->get();
            $web = [];

            foreach ($servidores as  $servidor) {
                $url = $servidor->dominio . '/registros/consulta_licencia';
                $resultado = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                    ->withOptions(["verify" => false])
                    ->post($url, ['sis_clientesid' => $cliente->sis_clientesid])
                    ->json();
                if (isset($resultado['licencias'])) {
                    $web = array_merge($web, $resultado['licencias']);
                }
            }

            if (count($web) == 0) {
                return json_encode([
                    "liberar" => true,
                    "accion" => "nuevo",
                    "facturito" => false,
                    "id_licencia" => 0,
                    "id_producto" => 0,
                    "numerocontrato" => 0,
                ]);
            }

            if (count($web) == 1) {
                return json_encode([
                    "liberar" =>   true,
                    "accion" => ($web[0]['producto'] == 9 || $web[0]['producto'] == 6) ? "nuevo" : "renovar",
                    "facturito" => $web[0]['producto'] == 12 ? true : false,
                    "id_licencia" => $web[0]['sis_licenciasid'],
                    "id_producto" => $web[0]['producto'],
                    "numerocontrato" => $web[0]['numerocontrato'],
                    "id_servidor" => $web[0]['sis_servidoresid'],
                ]);
            }

            if (count($web) > 1) {
                return json_encode([
                    "liberar" => false,
                    "accion" => "renovar",
                    "facturito" => false,
                    "id_licencia" => 0,
                    "id_producto" => 0,
                    "numerocontrato" => 0,
                    "id_servidor" => 0,
                ]);
            }
        } else {
            return json_encode([
                "liberar" => true,
                "accion" => "nuevo",
                "facturito" => false,
                "id_licencia" => 0,
                "id_producto" => 0,
                "numerocontrato" => 0,
                "id_servidor" => 0,
            ]);
        }
    }

    public function renovar_web(Request $request)
    {
        $servidor = Servidores::where('sis_servidoresid', $request->id_servidor)->first();
        $url = $servidor->dominio . '/registros/consulta_licencia';
        $licenciaConsulta = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->post($url, ['sis_licenciasid' => $request->id_licencia])
            ->json();
        $licenciaEnviar = $licenciaConsulta['licencias'][0];
        $licenciaArray = json_encode($licenciaEnviar);
        $licencia = json_decode($licenciaArray);

        $cliente = Clientes::where('sis_clientesid', $licencia->sis_clientesid)->first();

        $vendedor = Revendedores::where('sis_revendedoresid', $cliente->sis_vendedoresid)->first();
        $vendedor_tienda = Revendedores::where('sis_revendedoresid', $request->sis_vendedoresid)->first();
        //Si el vendedor no corresponde al vendedor del cliente
        if ($request->sis_vendedoresid != 0 && $request->sis_vendedoresid != $cliente->sis_vendedoresid) {
            try {
                $array['from'] = env('MAIL_FROM_ADDRESS');
                $array['subject'] = 'URGENTE';
                $array['cliente'] = $cliente->nombres;

                //Notificar al vendedor incorrecto
                $array['view'] = 'emails.vendedor_incorrecto';
                $emails = $vendedor_tienda->correo;
                $array['tipo'] = 1;
                Mail::to($emails)->queue(new vendedor($array));

                //Notificar al vendendor correcto
                $array['view'] = 'emails.vendedor_correcto';
                $emails = $vendedor->correo;
                $array['tipo'] = 2;
                $array['vendedor'] = $vendedor_tienda->razonsocial;
                Mail::to($emails)->queue(new vendedor($array));
            } catch (\Exception $e) {
            }
            return json_encode(["licencia" => ['El vendedor no corresponde al registrado en la licencia']]);
        }
        //En caso de renovar mensual, anual o actualizar
        switch ($request->renovar) {
            case '1':
                $datos['fechacaduca'] = date("Ymd", strtotime($licencia->fechacaduca . "+ 1 month"));
                $datos['fecha_renovacion'] = date('YmdHis', strtotime(now()));
                $asunto = 'Renovacion Mensual Licencia Web';
                $datos['periodo'] = 1;
                break;
            case '2':
                $datos['fechacaduca'] = date("Ymd", strtotime($licencia->fechacaduca . "+ 1 year"));
                $datos['fecha_renovacion'] = date('YmdHis', strtotime(now()));
                $asunto = 'Renovacion Anual Licencia Web';
                if ($licencia->producto != 12) {
                    $datos['periodo'] = 2;
                }
                break;
        }

        $datos['periodo'] = $licencia->periodo;
        $datos['sis_clientesid'] = $licencia->sis_clientesid;
        $datos['sis_servidoresid'] = $licencia->sis_servidoresid;
        $datos['sis_distribuidoresid'] = $licencia->sis_distribuidoresid;
        $datos['sis_agrupadosid'] = $licencia->sis_agrupadosid;
        $datos['numerocontrato'] = $licencia->numerocontrato;
        $datos['producto'] = $licencia->producto;
        $datos['fechainicia'] = $licencia->fechainicia;
        $datos['empresas'] = $licencia->empresas;
        $datos['numeromoviles'] = $licencia->numeromoviles;
        $datos['precio'] = $licencia->precio;
        $datos['tipo_licencia'] = $licencia->tipo_licencia;
        $datos['fechacrecion'] = $licencia->fechacreacion;
        $datos['fechamodificacion'] = date('YmdHis', strtotime(now()));
        $datos['modulos'] = $licencia->modulos;
        $datos['usuarios'] = $licencia->usuarios;
        $datos['numerosucursales'] = $licencia->numerosucursales;
        $parametros_json = json_decode($licencia->parametros_json);
        //si es facturito sumar los documentos
        if ($licencia->producto == 12) {
            $datos['fechacaduca'] = date("Ymd", strtotime($licencia->fechacaduca . "+ 1 year"));
            $asunto = 'Renovacion Anual Facturito';
            switch ($licencia->periodo) {
                case '1':
                    $parametros_json->Documentos = $parametros_json->Documentos + 60;
                    break;
                case '2':
                    $parametros_json->Documentos = $parametros_json->Documentos + 150;
                    break;
                case '3':
                    $parametros_json->Documentos = 100000;
                    break;
                case '4':
                    $parametros_json->Documentos = 30;
                    break;
            }
        }
        $datos['parametros_json'] = json_encode($parametros_json);
        $datos['usuariocreacion'] = $licencia->usuariocreacion;
        $datos['usuariomodificacion'] = $vendedor->razonsocial;
        $datos['sis_licenciasid'] = $request->id_licencia;

        $urlEditar = $servidor->dominio . '/registros/editar_licencia';
        $licenciaEditar = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->post($urlEditar, $datos)
            ->json();

        if (isset($licenciaEditar['licencias'])) {

            $licenciaweb = Licenciasweb::where('sis_licenciasid', $request->id_licencia)
                ->where('sis_servidoresid', $request->id_servidor)
                ->where('sis_clientesid', $licencia->sis_clientesid)
                ->first();

            unset($datos['fechacrecion']);

            $licenciaweb->update($datos);

            $log = new Log();
            $log->usuario = $vendedor->razonsocial;
            $log->pantalla = "Licencia Web";
            $log->tipooperacion = "Modificar";
            $log->fecha = now();
            $log->detalle = json_encode($datos);
            $log->save();

            //Enviar correo al cliente
            $correos = Clientes::select('sis_clientes.correos', 'sis_clientes.nombres', 'sis_clientes.identificacion', 'sis_distribuidores.correos AS distribuidor', 'sis_revendedores.correo AS vendedor', 'revendedor.correo AS revendedor', 'sis_revendedores.razonsocial', 'sis_distribuidores.razonsocial AS nombredistribuidor')
                ->join('sis_distribuidores', 'sis_distribuidores.sis_distribuidoresid', 'sis_clientes.sis_distribuidoresid')
                ->join('sis_revendedores', 'sis_revendedores.sis_revendedoresid', 'sis_clientes.sis_vendedoresid')
                ->join('sis_revendedores as revendedor', 'revendedor.sis_revendedoresid', 'sis_clientes.sis_vendedoresid')
                ->where('sis_clientesid', $cliente->sis_clientesid)
                ->first();

            $array['view'] = 'emails.licenciaweb';
            $array['from'] = env('MAIL_FROM_ADDRESS');
            $array['subject'] = $asunto;
            $array['cliente'] = $correos->nombres;
            $array['vendedor'] = $correos->razonsocial;
            $array['identificacion'] = $correos->identificacion;
            $array['correos'] = $correos->correos;
            $array['numerocontrato'] = $datos['numerocontrato'];
            $array['producto'] = $datos['producto'];
            if ($datos['producto'] == 12) {
                switch ($datos['periodo']) {
                    case '1':
                        $array['periodo'] = "Inicial";
                        break;
                    case '2':
                        $array['periodo'] = "Básico";
                        break;
                    case '3':
                        $array['periodo'] = "Premium";
                        break;
                }
            } else {
                $array['periodo'] = $datos['periodo'] == 1 ? 'Mensual' : 'Anual';
            }
            $array['fechainicia'] = date("d-m-Y", strtotime($datos['fechainicia']));
            $array['fechacaduca'] =  date("d-m-Y", strtotime($datos['fechacaduca']));
            $array['empresas'] = $datos['empresas'];
            $array['numeromoviles'] = $datos['numeromoviles'];
            $array['usuarios'] = $datos['usuarios'];
            $array['distribuidor'] = $correos->nombredistribuidor;
            $transformar = simplexml_load_string($datos['modulos']);
            $json = json_encode($transformar);
            $array['modulos'] = json_decode($json);
            $array['usuario'] = $vendedor->razonsocial;
            $array['fecha'] =  date("Y-m-d H:i:s", strtotime($datos['fechamodificacion']));

            if ($datos['producto'] == 12) {
                $array['tipo'] = 8;
            } else {
                $array['tipo'] = 3;
            }

            $emails = explode(", ", $correos->distribuidor);

            $emails = array_merge($emails,  [
                "facturacion@perseo.ec",
                $correos->vendedor,
                $correos->revendedor,
                $correos->correos,
            ]);

            $emails = array_diff($emails, array(" ", 0, null));

            try {
                Mail::to($emails)->queue(new enviarlicencia($array));
            } catch (\Exception $e) {
            }
            return json_encode(["renovar" => true]);
        } else {
            return json_encode(["renovar" => false]);
        }
    }

    public function vendedores_consulta(Request $request)
    {
        $identificacionIngresada = substr($request->identificacion, 0, 10);
        $vendedor = Revendedores::whereIn('identificacion', [$identificacionIngresada, $request->identificacion, $request->identificacion . '001'])->first();

        return json_encode(["vendedor" => [$vendedor]]);
    }

    public function licencia_actualiza(Request $request)
    {
        $licencia = Licencias::where('numerocontrato', $request->numerocontrato)->first();
        $licencia->tokenrespaldo = $request->token;
        $licencia->tokenrespaldo = $request->token;
        $cantidadempresas = [];
        $cantidadempresas = [
            'empresas_activas' => $request->empresas_activas,
            'empresas_inactivas' => $request->empresas_inactivas,
        ];
        $licencia->cantidadempresas = json_encode($cantidadempresas);
        $licencia->save();
        return json_encode(["licencia" => [$licencia]]);
    }

    public function registrar_licencia(Request $request)
    {
        $servidores = Servidores::where('estado', 1)->get();
        $identificacionCliente = substr($request["cliente"]['identificacion'], 0, 10);
        $cliente = Clientes::whereIn('identificacion', [$identificacionCliente, $request["cliente"]['identificacion'], $request["cliente"]['identificacion'] . '001'])->first();
        $vendedor = Revendedores::where('sis_revendedoresid', $request["cliente"]['sis_vendedoresid'])->first();
        $sis_clientesid = 0;
        //si no existe cliente se lo registra
        if (!isset($cliente->sis_clientesid)) {
            $nuevo = new Clientes();
            $nuevo->identificacion = $request["cliente"]['identificacion'];
            $nuevo->nombres = $request["cliente"]['nombres'];
            $nuevo->direccion = $request["cliente"]['direccion'];
            $nuevo->correos = $request["cliente"]['correos'];
            $nuevo->telefono2 = $request["cliente"]['telefono2'];
            $nuevo->sis_distribuidoresid = $request["cliente"]['sis_distribuidoresid'];
            $nuevo->sis_vendedoresid = $request["cliente"]['sis_vendedoresid'];

            if ($request["cliente"]['contador'] == 0) {
                $nuevo->sis_revendedoresid = 1;
            } else {
                $identificacionIngresada = substr($request["cliente"]['contador'], 0, 10);
                $contador = Revendedores::whereIn('identificacion', [$identificacionIngresada, $request["cliente"]['contador'], $request["cliente"]['contador'] . '001'])
                    ->where('sis_distribuidoresid', $request["cliente"]['sis_distribuidoresid'])
                    ->where('tipo', 1)
                    ->first();
                if ($contador) {
                    $nuevo->sis_revendedoresid = $contador->sis_revendedoresid;
                } else {
                    return json_encode(["licencia" => ['El contador no existe para este distribuidor']]);
                }
            }
            $nuevo->grupo = 1;
            $nuevo->red_origen = 17;
            $nuevo->ciudadesid = 1701;
            $nuevo->provinciasid = 17;
            $nuevo->fechacreacion = now();
            $nuevo->usuariocreacion = $vendedor->razonsocial;

            DB::beginTransaction();
            try {
                $nuevo->save();
                $log = new Log();
                $log->usuario = $vendedor->razonsocial;
                $log->pantalla = "Clientes";
                $log->tipooperacion = "Crear";
                $log->fecha = now();
                $log->detalle = $nuevo;
                $log->save();

                foreach ($servidores as $servidor) {
                    $url = $servidor->dominio . '/registros/crear_clientes';
                    $crearCliente = Http::withHeaders(['Content-Type' => 'application/json; ', 'verify' => false])
                        ->withOptions(["verify" => false])
                        ->post($url, $nuevo->toArray())
                        ->json();
                    if (!isset($crearCliente['sis_clientes'])) {
                        DB::rollBack();
                        return json_encode(["licencia" => ['Ocurrió un error al crear el cliente']]);
                    }
                }
                DB::commit();
                $sis_clientesid = $nuevo->sis_clientesid;
                //return json_encode(["licencia" => ['Registrado Correctamente']]);
            } catch (\Exception $e) {
                DB::rollBack();
                return json_encode(["licencia" => ['Ocurrió un error al crear el cliente']]);
            }
        } else {
            $sis_clientesid = $cliente->sis_clientesid;
            $revendedor_correcto = Revendedores::where('sis_revendedoresid', $cliente->sis_revendedoresid)->first();
            //Si el vendedor no corresponde al vendedor del cliente
            if ($request["cliente"]['sis_vendedoresid'] != $cliente->sis_vendedoresid) {
                $vendedor_correcto = Revendedores::where('sis_revendedoresid', $cliente->sis_vendedoresid)->first();
                try {
                    $array['from'] = env('MAIL_FROM_ADDRESS');
                    $array['subject'] = 'URGENTE';
                    $array['cliente'] = $cliente->nombres;

                    //Notificar al vendedor incorrecto
                    $array['view'] = 'emails.vendedor_incorrecto';
                    $emails = $vendedor->correo;
                    $array['tipo'] = 1;
                    Mail::to($emails)->queue(new vendedor($array));

                    //Notificar al vendendor correcto
                    $array['view'] = 'emails.vendedor_correcto';
                    $emails = $vendedor_correcto->correo;
                    $array['tipo'] = 2;
                    $array['vendedor'] = $vendedor->razonsocial;
                    Mail::to($emails)->queue(new vendedor($array));
                } catch (\Exception $e) {
                }
                return json_encode(["licencia" => ['El vendedor no corresponde al registrado en la licencia']]);
            }

            //Si el contador no corresponde al contador del cliente
            if ($request["cliente"]['contador'] != 0) {
                $identificacionIngresada = substr($request["cliente"]['contador'], 0, 10);
                $contador = Revendedores::whereIn('identificacion', [$identificacionIngresada, $request["cliente"]['contador'], $request["cliente"]['contador'] . '001'])
                    ->where('tipo', 1)
                    ->first();
                if (!$contador) {
                    return json_encode(["licencia" => ['El contador no existe']]);
                } else {
                    if ($revendedor_correcto->identificacion != $request["cliente"]['contador']) {
                        return json_encode(["licencia" => ['El contador no corresponde al registrado para el cliente']]);
                    }
                }
            }

            //Buscar las licencias
            $web = [];

            $pc = Licencias::select('sis_licenciasid', 'numerocontrato', 'producto', 'tipo_licencia', 'fechacaduca', 'sis_clientesid', 'sis_servidoresid')
                ->where('sis_clientesid', $cliente->sis_clientesid)
                ->get();

            $web = Licenciasweb::select('sis_licenciasid', 'numerocontrato', 'producto', 'tipo_licencia', 'fechacaduca', 'sis_clientesid', 'sis_servidoresid')
                ->where('sis_clientesid', $cliente->sis_clientesid)
                ->get();

            $unir = array_merge($web->toArray(), $pc->toArray());

            //Si solo existe una licencia y si la licencia es lite
            if (count($unir) == 1) {
                if ($unir[0]['producto'] == 6 || $unir[0]['producto'] == 9) {
                    $cliente->update([
                        'sis_distribuidoresid' => $request["cliente"]['sis_distribuidoresid'],
                        'sis_vendedoresid' => $request["cliente"]['sis_vendedoresid']
                    ]);
                    DB::commit();
                }
            }
        }

        //registrar licencia
        $contrato = $this->generarContrato();

        foreach ($request["licencia"] as $licencia) {
            //if ($licencia['producto_id'] == 3 || $licencia['producto_id'] == 4 || $licencia['producto_id'] == 5 || $licencia['producto_id'] == 6 || $licencia['producto_id'] == 7 || $licencia['producto_id'] == 8 || $licencia['producto_id'] == 9 || $licencia['producto_id'] == 10 || $licencia['producto_id'] == 11 || $licencia['producto_id'] == 12) {
            //licencia web
            $nuevo = new Licencias();

            $nuevo->sis_clientesid = $sis_clientesid;
            $nuevo->numerocontrato = $contrato;
            $nuevo->fechacreacion = date('Y-m-d H:i:s', strtotime(now()));
            $nuevo->usuariocreacion = $vendedor->razonsocial;
            $nuevo->fechainicia = date('Ymd', strtotime(now()));
            $nuevo->sis_distribuidoresid = $request["cliente"]['sis_distribuidoresid'];
            $nuevo->fechaultimopago = $nuevo->fechainicia;

            switch ($licencia['producto_id']) {
                    //Facturacion Mensual
                case '61':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio =  9.50;
                    $nuevo->periodo =  1;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 month"));
                    $nuevo->producto =  2;
                    $nuevo->usuarios =  6;
                    $nuevo->numeromoviles =  1;
                    $nuevo->modulos = $this->modulos(0, 0, 1, 1, 0, 0, 0);
                    $nuevo->sis_servidoresid =  4;
                    $nuevo->tipo_licencia =  1;
                    $nuevo->empresas = 1;
                    break;
                    //Facturacion Anual
                case '60':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio =  72;
                    $nuevo->periodo =  2;
                    $nuevo->fechacaduca = $request["cliente"]['promocion'] == 1 ? date("Ymd", strtotime($nuevo->fechainicia . "+ 15 months")) : date("Ymd", strtotime($nuevo->fechainicia . "+ 1 year"));
                    $nuevo->producto =  2;
                    $nuevo->usuarios =  6;
                    $nuevo->numeromoviles =  1;
                    $nuevo->modulos = $this->modulos(0, 0, 1, 1, 0, 0, 0);
                    $nuevo->sis_servidoresid =  4;
                    $nuevo->tipo_licencia =  1;
                    $nuevo->empresas = 1;
                    break;
                    //Servicios Mensual
                case '59':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio =  17;
                    $nuevo->periodo =  1;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 month"));
                    $nuevo->producto =  3;
                    $nuevo->usuarios =  6;
                    $nuevo->numeromoviles =  2;
                    $nuevo->modulos = $this->modulos(1, 1, 0, 0, 0, 0, 0);
                    $nuevo->sis_servidoresid =  4;
                    $nuevo->tipo_licencia =  1;
                    $nuevo->empresas = 1;
                    break;
                    //Servicios Anual
                case '58':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio =  150;
                    $nuevo->periodo =  2;
                    $nuevo->fechacaduca = $request["cliente"]['promocion'] == 1 ? date("Ymd", strtotime($nuevo->fechainicia . "+ 15 months")) : date("Ymd", strtotime($nuevo->fechainicia . "+ 1 year"));
                    $nuevo->producto =  3;
                    $nuevo->usuarios =  6;
                    $nuevo->numeromoviles =  2;
                    $nuevo->modulos = $this->modulos(1, 1, 0, 0, 0, 0, 0);
                    $nuevo->sis_servidoresid =  4;
                    $nuevo->tipo_licencia =  1;
                    $nuevo->empresas = 1;
                    break;
                    //Comercial Mensual
                case '57':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio =  24;
                    $nuevo->periodo =  1;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 month"));
                    $nuevo->producto =  4;
                    $nuevo->usuarios =  6;
                    $nuevo->numeromoviles =  2;
                    $nuevo->modulos = $this->modulos(1, 1, 1, 0, 1, 1, 1);
                    $nuevo->sis_servidoresid =  4;
                    $nuevo->tipo_licencia =  1;
                    $nuevo->empresas = 1;
                    break;
                    //Comercial Anual
                case '56':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio =  190;
                    $nuevo->periodo =  2;
                    $nuevo->fechacaduca = $request["cliente"]['promocion'] == 1 ? date("Ymd", strtotime($nuevo->fechainicia . "+ 15 months")) : date("Ymd", strtotime($nuevo->fechainicia . "+ 1 year"));
                    $nuevo->producto =  4;
                    $nuevo->usuarios =  6;
                    $nuevo->numeromoviles =  2;
                    $nuevo->modulos = $this->modulos(1, 1, 1, 0, 1, 1, 1);
                    $nuevo->sis_servidoresid =  4;
                    $nuevo->tipo_licencia =  1;
                    $nuevo->empresas = 1;
                    break;
                    //Soy Contador Comercial Mensual
                case '63':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio =  13;
                    $nuevo->periodo =  1;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 month"));
                    $nuevo->producto =  5;
                    $nuevo->usuarios =  6;
                    $nuevo->numeromoviles =  0;
                    $nuevo->modulos = $this->modulos(1, 1, 1, 1, 0, 0, 0);
                    $nuevo->sis_servidoresid =  4;
                    $nuevo->tipo_licencia =  1;
                    $nuevo->empresas = 1;
                    break;
                    //Soy Contador Comercial Anual
                case '62':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio =  108;
                    $nuevo->periodo =  2;
                    $nuevo->fechacaduca = $request["cliente"]['promocion'] == 1 ? date("Ymd", strtotime($nuevo->fechainicia . "+ 15 months")) : date("Ymd", strtotime($nuevo->fechainicia . "+ 1 year"));
                    $nuevo->producto =  5;
                    $nuevo->usuarios =  6;
                    $nuevo->numeromoviles =  0;
                    $nuevo->modulos = $this->modulos(1, 1, 1, 1, 0, 0, 0);
                    $nuevo->sis_servidoresid =  4;
                    $nuevo->tipo_licencia =  1;
                    $nuevo->empresas = 1;
                    break;
                    //Soy Contador Servicios Mensual
                case '65':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio =  9.80;
                    $nuevo->periodo =  1;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 month"));
                    $nuevo->producto =  8;
                    $nuevo->usuarios =  6;
                    $nuevo->numeromoviles =  0;
                    $nuevo->modulos = $this->modulos(0, 0, 0, 0, 0, 0, 0);
                    $nuevo->sis_servidoresid =  4;
                    $nuevo->tipo_licencia =  1;
                    $nuevo->empresas = 1;
                    break;
                    //Soy Contador Servicios Anual
                case '64':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio =  90;
                    $nuevo->periodo =  2;
                    $nuevo->fechacaduca = $request["cliente"]['promocion'] == 1 ? date("Ymd", strtotime($nuevo->fechainicia . "+ 15 months")) : date("Ymd", strtotime($nuevo->fechainicia . "+ 1 year"));
                    $nuevo->producto =  8;
                    $nuevo->usuarios =  6;
                    $nuevo->numeromoviles =  0;
                    $nuevo->modulos = $this->modulos(0, 0, 0, 0, 0, 0, 0);
                    $nuevo->sis_servidoresid =  4;
                    $nuevo->tipo_licencia =  1;
                    $nuevo->empresas = 1;
                    break;
                    //Socio Perseo Mensual
                case '67':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio =  7;
                    $nuevo->periodo =  1;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 month"));
                    $nuevo->producto =  11;
                    $nuevo->usuarios =  1;
                    $nuevo->numeromoviles =  1;
                    $nuevo->modulos = $this->modulos(1, 1, 1, 1, 1, 1, 1);
                    $nuevo->sis_servidoresid =  4;
                    $nuevo->tipo_licencia =  1;
                    $nuevo->empresas = 1;
                    break;
                    //Socio Perseo Anual
                case '66':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio =  87.50;
                    $nuevo->periodo =  2;
                    $nuevo->fechacaduca = $request["cliente"]['promocion'] == 1 ? date("Ymd", strtotime($nuevo->fechainicia . "+ 15 months")) : date("Ymd", strtotime($nuevo->fechainicia . "+ 1 year"));
                    $nuevo->producto =  11;
                    $nuevo->usuarios =  1;
                    $nuevo->numeromoviles =  1;
                    $nuevo->modulos = $this->modulos(1, 1, 1, 1, 1, 1, 1);
                    $nuevo->sis_servidoresid =  4;
                    $nuevo->tipo_licencia =  1;
                    $nuevo->empresas = 1;
                    break;
                    //Facturito Inicial
                case '1':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio =  5.40;
                    $nuevo->periodo =  1;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 year"));
                    $nuevo->producto =  12;
                    $nuevo->usuarios =  6;
                    $nuevo->numeromoviles =  1;
                    $nuevo->modulos = $this->modulos(0, 0, 0, 0, 0, 0, 0);
                    $nuevo->sis_servidoresid =  2;
                    $nuevo->tipo_licencia =  1;
                    $nuevo->empresas = 1;
                    $parametros_json = [];
                    $parametros_json = [
                        'Documentos' => "60",
                        'Productos' => "0",
                        'Almacenes' => "0",
                        'Nomina' => "0",
                        'Produccion' => "0",
                        'Activos' => "0",
                        'Talleres' => "0",
                        'Garantias' => "0",
                    ];
                    $nuevo->parametros_json = json_encode($parametros_json);
                    break;
                    //Facturito Basico
                case '2':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio =  8.99;
                    $nuevo->periodo =  2;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 year"));
                    $nuevo->producto =  12;
                    $nuevo->usuarios =  6;
                    $nuevo->numeromoviles =  1;
                    $nuevo->modulos = $this->modulos(0, 0, 0, 0, 0, 0, 0);
                    $nuevo->sis_servidoresid =  2;
                    $nuevo->tipo_licencia =  1;
                    $nuevo->empresas = 1;
                    $parametros_json = [];
                    $parametros_json = [
                        'Documentos' => "150",
                        'Productos' => "0",
                        'Almacenes' => "0",
                        'Nomina' => "0",
                        'Produccion' => "0",
                        'Activos' => "0",
                        'Talleres' => "0",
                        'Garantias' => "0",
                    ];
                    $nuevo->parametros_json = json_encode($parametros_json);
                    break;
                    //Facturito Pro
                case '3':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio =  17.99;
                    $nuevo->periodo =  3;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 year"));
                    $nuevo->producto =  12;
                    $nuevo->usuarios =  6;
                    $nuevo->numeromoviles =  1;
                    $nuevo->modulos = $this->modulos(0, 0, 0, 0, 0, 0, 0);
                    $nuevo->sis_servidoresid =  2;
                    $nuevo->tipo_licencia =  1;
                    $nuevo->empresas = 1;
                    $parametros_json = [];
                    $parametros_json = [
                        'Documentos' => "100000",
                        'Productos' => "0",
                        'Almacenes' => "0",
                        'Nomina' => "0",
                        'Produccion' => "0",
                        'Activos' => "0",
                        'Talleres' => "0",
                        'Garantias' => "0",
                    ];
                    $nuevo->parametros_json = json_encode($parametros_json);
                    break;
                    //Facturito Gratis
                case '81':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio =  4;
                    $nuevo->periodo =  4;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 year"));
                    $nuevo->producto =  12;
                    $nuevo->usuarios =  6;
                    $nuevo->numeromoviles =  1;
                    $nuevo->modulos = $this->modulos(0, 0, 0, 0, 0, 0, 0);
                    $nuevo->sis_servidoresid =  2;
                    $nuevo->tipo_licencia =  1;
                    $nuevo->empresas = 1;
                    $parametros_json = [];
                    $parametros_json = [
                        'Documentos' => "30",
                        'Productos' => "0",
                        'Almacenes' => "0",
                        'Nomina' => "0",
                        'Produccion' => "0",
                        'Activos' => "0",
                        'Talleres' => "0",
                        'Garantias' => "0",
                    ];
                    $nuevo->parametros_json = json_encode($parametros_json);
                    break;
                    //Perseo Lite
                case '1000':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio =  0;
                    $nuevo->periodo =  1;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 month"));
                    $nuevo->producto =  9;
                    $nuevo->usuarios =  6;
                    $nuevo->numeromoviles =  1;
                    $nuevo->modulos = $this->modulos(1, 1, 1, 1, 1, 1, 1);
                    $nuevo->sis_servidoresid =  3;
                    $nuevo->tipo_licencia =  1;
                    $nuevo->empresas = 1;
                    $parametros_json = [];
                    $parametros_json = [
                        'Documentos' => "100000",
                        'Productos' => "100000",
                        'Almacenes' => "1",
                        'Nomina' => "3",
                        'Produccion' => "3",
                        'Activos' => "3",
                        'Talleres' => "3",
                        'Garantias' => "3",
                    ];
                    $nuevo->parametros_json = json_encode($parametros_json);
                    break;
            }

            $servidor = Servidores::where('sis_servidoresid', $nuevo->sis_servidoresid)->first();
            $url = $servidor->dominio . '/registros/crear_licencias';
            $crearLicenciaWeb = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                ->withOptions(["verify" => false])
                ->post($url, $nuevo->toArray())
                ->json();

            if (isset($crearLicenciaWeb["licencias"])) {

                $nuevo->sis_licenciasid = $crearLicenciaWeb["licencias"][0]['sis_licenciasid'];
                Licenciasweb::create($nuevo->toArray());

                $log = new Log();
                $log->usuario = 'Tienda';
                $log->pantalla = "Licencia Web";
                $log->tipooperacion = "Crear";
                $log->fecha = now();
                $log->detalle = json_encode($nuevo);
                $log->save();

                $cliente = Clientes::select('sis_clientes.correos', 'sis_clientes.nombres', 'sis_clientes.identificacion', 'sis_distribuidores.correos AS distribuidor', 'sis_revendedores.correo AS vendedor', 'revendedor.correo AS revendedor', 'sis_revendedores.razonsocial', 'sis_distribuidores.razonsocial AS nombredistribuidor')
                    ->join('sis_distribuidores', 'sis_distribuidores.sis_distribuidoresid', 'sis_clientes.sis_distribuidoresid')
                    ->join('sis_revendedores', 'sis_revendedores.sis_revendedoresid', 'sis_clientes.sis_vendedoresid')
                    ->join('sis_revendedores as revendedor', 'revendedor.sis_revendedoresid', 'sis_clientes.sis_vendedoresid')
                    ->where('sis_clientesid', $sis_clientesid)
                    ->first();

                $array['view'] = 'emails.licenciaweb';
                $array['from'] = env('MAIL_FROM_ADDRESS');
                $array['subject'] = 'Nuevo Registro Licencia Web';
                $array['cliente'] = $cliente->nombres;
                $array['vendedor'] = $cliente->razonsocial;
                $array['identificacion'] = $cliente->identificacion;
                $array['correos'] = $cliente->correos;
                $array['numerocontrato'] = $nuevo['numerocontrato'];
                $array['producto'] = $nuevo['producto'];
                $array['distribuidor'] = $cliente->nombredistribuidor;

                if ($nuevo['producto'] == 12) {
                    switch ($nuevo['periodo']) {
                        case '1':
                            $array['periodo'] = "Inicial";
                            break;
                        case '2':
                            $array['periodo'] = "Básico";
                            break;
                        case '3':
                            $array['periodo'] = "Premium";
                            break;
                        case '4':
                            $array['periodo'] = "Gratis";
                            break;
                    }
                } else {
                    $array['periodo'] = $nuevo['periodo'] == 1 ? 'Mensual' : 'Anual';
                }

                $array['fechainicia'] = date("d-m-Y", strtotime($nuevo['fechainicia']));
                $array['fechacaduca'] =  date("d-m-Y", strtotime($nuevo['fechacaduca']));
                $array['empresas'] = $nuevo['empresas'];
                $array['numeromoviles'] = $nuevo['numeromoviles'];
                $array['usuarios'] = $nuevo['usuarios'];
                $transformar = simplexml_load_string($nuevo['modulos']);
                $json = json_encode($transformar);
                $array['modulos'] = json_decode($json);
                $array['usuario'] = $vendedor->razonsocial;
                $array['fecha'] = $nuevo['fechacreacion'];

                if ($nuevo['producto'] == 12) {
                    $array['tipo'] = 7;
                } else {
                    $array['tipo'] = 1;
                }

                $emails = explode(", ", $cliente->distribuidor);

                $emails = array_merge($emails,  [
                    "facturacion@perseo.ec",
                    $cliente->vendedor,
                    $cliente->revendedor,
                    $cliente->correos
                ]);

                $emails = array_diff($emails, array(" ", 0, null));
                try {
                    Mail::to($emails)->queue(new enviarlicencia($array));
                } catch (\Exception $e) {
                }
            } else {
                return json_encode(["licencia" => ['Ocurrió un error al crear la licencia']]);
            }
        }
        //}
        return json_encode(["licencia" => ['Creado correctamente']]);
    }

    public function consulta_notificaciones(Request $request)
    {
        $notificaciones = Notificaciones::whereBetween('fechapublicacion', [$request->inicio, $request->fin])
            ->whereIn('tipo', [0, $request->tipo])
            ->whereIn('sis_distribuidoresid', [0, $request->distribuidor])
            ->get();

        return json_encode($notificaciones);
    }

    public function generarContrato()
    {
        $randomString = "";
        while (strlen($randomString) < 10) {
            $numero = rand(1, 9);
            $randomString = $randomString . $numero;
        }

        $pc = Licencias::where('numerocontrato', $randomString)->first();
        $web = Licenciasweb::where('numerocontrato', $randomString)->first();

        if ($pc || $web) {
            $randomString = $this->generarContrato();
        }

        return $randomString;
    }

    public function modulos($nomina, $activos, $produccion, $restaurantes, $talleres, $garantias, $ecommerce)
    {
        $xw = xmlwriter_open_memory();
        xmlwriter_start_document($xw, '1.0', 'UTF-8');
        xmlwriter_start_element($xw, 'modulos');
        xmlwriter_start_element($xw, 'nomina');
        xmlwriter_text($xw, $nomina);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'activos');
        xmlwriter_text($xw, $activos);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'produccion');
        xmlwriter_text($xw, $produccion);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'restaurantes');
        xmlwriter_text($xw, $restaurantes);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'talleres');
        xmlwriter_text($xw, $talleres);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'garantias');
        xmlwriter_text($xw, $garantias);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'ecommerce');
        xmlwriter_text($xw, $ecommerce);
        xmlwriter_end_element($xw);
        xmlwriter_end_document($xw);
        return xmlwriter_output_memory($xw);
    }

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
            $licencia->fechainicia = $licencia->fechainicia == null ? date('d-m-Y', strtotime(now()))  : date('d-m-Y', $licencia->fechainicia);
            $licencia->fechacaduca = $licencia->fechacaduca == null ? date('d-m-Y', strtotime(now()))  : date('d-m-Y', $licencia->fechacaduca);
            $licencia->fechaultimopago = $licencia->fechaultimopago == null ? date('d-m-Y', strtotime(now()))  : date('d-m-Y', $licencia->fechaultimopago);
            $licencia->fechaactulizaciones = $licencia->fechaactulizaciones == null ? date('d-m-Y', strtotime(now()))  : date('d-m-Y', $licencia->fechaactulizaciones);
            $licencia->periodo = $licencia->periodo == 1 ? "Mensual"  : "Anual";
            $licencia->producto = $this->producto($licencia);
            $licencia->tipo_licencia = $licencia->tipo_licencia == 1 ? "Web"  : "PC";
            $licencia->red_origen = $this->origen($licencia);
            $licencia->provinciasid = $this->provincias($licencia);
            $licencia->precio = number_format(floatval($licencia->precio), 2, ',', '.');
            unset($licencia->ciudadesid);
            return $licencia;
        });

        $licencias = $licencias->toArray();
        return response()->json(["ventas" => $licencias]);
    }

    private function producto($licencia)
    {
        $producto = "";
        if ($licencia->tipo_licencia == 1) {
            switch ($licencia->producto) {
                case '2':
                    $producto = "Facturación";
                    break;
                case '3':
                    $producto = "Servicios";
                    break;
                case '4':
                    $producto = "Comercial";
                    break;
                case '5':
                    $producto = "Soy Contador Comercial";
                    break;
                case '6':
                    $producto = "Perseo Lite Anterior";
                    break;
                case '7':
                    $producto = "Total";
                    break;
                case '8':
                    $producto = "Soy Contador Servicios";
                    break;
                case '9':
                    $producto = "Perseo Lite";
                    break;
                case '10':
                    $producto = "Emprendedor";
                    break;
                case '11':
                    $producto = "Socio Perseo";
                    break;
                case '12':
                    $producto = "Facturito";
                    break;
                default:
                    $producto = "Sin Asignar";
                    break;
            }
        } else {
            if ($licencia->modulopractico == 1) $producto = "Práctico";
            if ($licencia->modulocontrol == 1) $producto = "Control";
            if ($licencia->modulocontable == 1) $producto = "Contable";
            if ($licencia->modulocontable == 1) $producto = "Contable";
        }

        return $producto;
    }

    private function origen($licencia)
    {
        $origen = "";
        switch ($licencia->red_origen) {
            case '1':
                $origen = "PERSEO";
                break;
            case '2':
                $origen = "CONTAFACIL";
                break;
            case '3':
                $origen = "UIO-01";
                break;
            case '4':
                $origen = "GYE-01";
                break;
            case '5':
                $origen = "GYE-02";
                break;
            case '6':
                $origen = "CUE-01";
                break;
            case '7':
                $origen = "STO-01";
                break;
            case '8':
                $origen = "UIO-02";
                break;
            case '9':
                $origen = "GYE-03";
                break;
            case '10':
                $origen = "CNV-01";
                break;
            case '11':
                $origen = "MATRIZ";
                break;
        }
        return $origen;
    }

    private function provincias($licencia)
    {
        $provincia = "";
        switch ($licencia->provinciasid) {
            case '1':
                $provincia = "AZUAY";
                break;
            case '2':
                $provincia = "BOLIVAR";
                break;
            case '3':
                $provincia = "CAÑAR";
                break;
            case '4':
                $provincia = "CARCHI";
                break;
            case '5':
                $provincia = "CHIMBORAZO";
                break;
            case '6':
                $provincia = "COTOPAXI";
                break;
            case '7':
                $provincia = "EL ORO";
                break;
            case '8':
                $provincia = "ESMERALDAS";
                break;
            case '9':
                $provincia = "GUAYAS";
                break;
            case '10':
                $provincia = "IMBABURA";
                break;
            case '11':
                $provincia = "LOJA";
                break;
            case '12':
                $provincia = "LOS RIOS";
                break;
            case '13':
                $provincia = "MANABI";
                break;
            case '14':
                $provincia = "MORONA SANTIAGO";
                break;
            case '15':
                $provincia = "NAPO";
                break;
            case '16':
                $provincia = "PASTAZA";
                break;
            case '17':
                $provincia = "PICHINCHA";
                break;
            case '18':
                $provincia = "TUNGURAHUA";
                break;
            case '19':
                $provincia = "ZAMORA CHINCHIPE";
                break;
            case '20':
                $provincia = "GALAPAGOS";
                break;
            case '21':
                $provincia = "SUCUMBIOS";
                break;
            case '22':
                $provincia = "ORELLANA";
                break;
            case '23':
                $provincia = "SANTO DOMINGO DE LOS TSACHILAS";
                break;
            case '24':
                $provincia = "SANTA ELENA";
                break;
        }
        return $provincia;
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

    public function proximas_caducar($distribuidor = null)
    {
        $query = "SELECT * FROM
            (
            SELECT
                sis_clientes.identificacion,
                sis_clientes.nombres,
                sis_clientes.telefono2,
                sis_clientes.correos,
                sis_clientes.direccion,
                sis_licencias.tipo_licencia,
                sis_licencias.periodo,
                sis_licencias.producto,
                sis_clientes.sis_distribuidoresid,
                vendedor.identificacion AS vendedor,
                contador.identificacion AS contador_identificacion,
                contador.razonsocial AS contador_nombres,
                contador.correo AS contador_correo,
                contador.celular AS contador_celular,
                contador.direccion AS contador_direccion,
                sis_licencias.modulopractico,
                sis_licencias.modulocontrol,
                sis_licencias.modulocontable
            FROM
                sis_licencias
                INNER JOIN sis_clientes ON sis_licencias.sis_clientesid = sis_clientes.sis_clientesid
                INNER JOIN sis_revendedores AS vendedor ON vendedor.sis_revendedoresid = sis_clientes.sis_vendedoresid
                INNER JOIN sis_revendedores AS contador ON contador.sis_revendedoresid = sis_clientes.sis_revendedoresid
            WHERE
                sis_licencias.periodo <> 3
                AND DATEDIFF(
                    sis_licencias.fechacaduca,
                NOW()) = 5 UNION
            SELECT
                sis_clientes.identificacion,
                sis_clientes.nombres,
                sis_clientes.telefono2,
                sis_clientes.correos,
                sis_clientes.direccion,
                sis_licencias_web.tipo_licencia,
                sis_licencias_web.periodo,
                sis_licencias_web.producto,
                sis_clientes.sis_distribuidoresid,
                vendedor.identificacion AS vendedor,
                contador.identificacion AS contador_identificacion,
                contador.razonsocial AS contador_nombres,
                contador.correo AS contador_correo,
                contador.celular AS contador_celular,
                contador.direccion AS contador_direccion,
                sis_licencias_web.modulopractico,
                sis_licencias_web.modulocontrol,
                sis_licencias_web.modulocontable
            FROM
                sis_clientes
                INNER JOIN sis_licencias_web ON sis_licencias_web.sis_clientesid = sis_clientes.sis_clientesid
                INNER JOIN sis_revendedores AS vendedor ON vendedor.sis_revendedoresid = sis_clientes.sis_vendedoresid
                INNER JOIN sis_revendedores AS contador ON contador.sis_revendedoresid = sis_clientes.sis_revendedoresid
            WHERE
            sis_licencias_web.periodo <> 4 AND
                DATEDIFF(
                    sis_licencias_web.fechacaduca,
                NOW()) = 5
            ) AS U";
        //Si el distribuidor es diferente de cero se agrega la condicion
        if ($distribuidor) {
            $query .= " WHERE U.sis_distribuidoresid = $distribuidor";
        }

        return json_encode(DB::select($query));
    }

    public function plan_soporte(Request $request)
    {
        $web = Clientes::select('sis_clientes.identificacion', 'sis_clientes.nombres', 'sis_licencias_web.numerocontrato', 'sis_licencias_web.producto', 'sis_licencias_web.tipo_licencia', 'sis_licencias_web.sis_distribuidoresid', 'sis_servidores.dominio')
            ->join('sis_licencias_web', 'sis_licencias_web.sis_clientesid', 'sis_clientes.sis_clientesid')
            ->join('sis_servidores', 'sis_servidores.sis_servidoresid', 'sis_licencias_web.sis_servidoresid')
            ->where('sis_licencias_web.numerocontrato', $request->numerocontrato)
            ->first();

        if ($web) {
            $web->producto = $web->producto == 12 ? "Facturito" : "Web";
            $web->tipo_licencia = "Web";
            return response()->json($web);
        }

        $pc = Clientes::select('sis_clientes.identificacion', 'sis_clientes.nombres', 'sis_licencias.numerocontrato', 'sis_licencias.tipo_licencia', 'sis_licencias.plan_soporte', 'sis_licencias.fechacaduca_soporte', 'sis_licencias.sis_distribuidoresid')
            ->join('sis_licencias', 'sis_licencias.sis_clientesid', 'sis_clientes.sis_clientesid')
            ->where('sis_licencias.numerocontrato', $request->numerocontrato)
            ->where('sis_licencias.plan_soporte', 1)
            ->where('sis_licencias.fechacaduca_soporte', '>=', date('Y-m-d'))
            ->first();

        if ($pc) {
            $pc->tipo_licencia =  "PC";
            return response()->json($pc);
        }

        return response()->json(['error' => 'No se encontraron resultados']);
    }

    public function informacion_licencia(Request $request)
    {
        $licenciaid = $request->licenciaid;
        $servidorid = $request->servidorid;

        $query = "
            SELECT
                sis_clientes.identificacion,
                sis_clientes.nombres,
                sis_clientes.telefono2,
                sis_clientes.correos,
                sis_clientes.direccion,
                sis_licencias_web.tipo_licencia,
                sis_licencias_web.periodo,
                sis_licencias_web.producto,
                sis_clientes.sis_distribuidoresid,
                vendedor.identificacion AS vendedor,
                contador.identificacion AS contador_identificacion,
                contador.razonsocial AS contador_nombres,
                contador.correo AS contador_correo,
                contador.celular AS contador_celular,
                contador.direccion AS contador_direccion,
                sis_licencias_web.modulopractico,
                sis_licencias_web.modulocontrol,
                sis_licencias_web.modulocontable
            FROM
                sis_clientes
                INNER JOIN sis_licencias_web ON sis_licencias_web.sis_clientesid = sis_clientes.sis_clientesid
                INNER JOIN sis_revendedores AS vendedor ON vendedor.sis_revendedoresid = sis_clientes.sis_vendedoresid
                INNER JOIN sis_revendedores AS contador ON contador.sis_revendedoresid = sis_clientes.sis_revendedoresid
            WHERE
                sis_licencias_web.sis_licenciasid = $licenciaid
                AND sis_servidoresid = $servidorid";

        return json_encode(DB::selectOne($query));
    }

    public function update_licencia(Request $request)
    {
        $licencia = Licencias::where('numerocontrato', $request->numerocontrato)->first();

        if ($licencia) {

            if ($request->has('fecha_respaldo')) {
                $licencia->fecha_respaldo = date('Y-m-d', strtotime($request->fecha_respaldo));
            }

            if ($request->has('version_ejecutable')) {
                $licencia->version_ejecutable = $request->version_ejecutable;
            }

            if ($request->has('fecha_actualizacion_ejecutable')) {
                $licencia->fecha_actualizacion_ejecutable = date('Y-m-d', strtotime($request->fecha_actualizacion_ejecutable));
            }

            $licencia->save();
        }

        return response()->json($licencia);
    }

    public function correos_licencia(Request $request)
    {
        $licencia = Licencias::where('numerocontrato', $request->numerocontrato)->first();
        $correos = "";

        if ($licencia) {
            $correos = $licencia->correopropietario . '; ' . $licencia->correoadministrador . '; ' . $licencia->correocontador;
        }

        return response()->json(['correos' => $correos]);
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
