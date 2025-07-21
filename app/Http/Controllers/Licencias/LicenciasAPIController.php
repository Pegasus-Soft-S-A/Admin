<?php

namespace App\Http\Controllers\Licencias;

use App\Mail\enviarlicencia;
use App\Mail\vendedor;
use App\Models\Clientes;
use App\Models\Licencias;
use App\Models\Licenciasvps;
use App\Models\Licenciasweb;
use App\Models\Log;
use App\Models\Revendedores;
use App\Models\Servidores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class LicenciasAPIController extends LicenciasBaseController
{
    public function licencia_actualiza(Request $request)
    {
        $licencia = Licencias::where('numerocontrato', $request->numerocontrato)->first();

        // Solo actualizar si el campo fue enviado y tiene valor
        if ($request->filled('token')) {
            $licencia->tokenrespaldo = $request->token;
        }

        if ($request->filled('usuarios_activos')) {
            $licencia->usuarios_activos = $request->usuarios_activos;
        }

        // Para cantidadempresas, verificar si al menos uno de los campos fue enviado
        if ($request->filled('empresas_activas') || $request->filled('empresas_inactivas')) {
            $cantidadempresas = [];

            if ($request->filled('empresas_activas')) {
                $cantidadempresas['empresas_activas'] = $request->empresas_activas;
            }

            if ($request->filled('empresas_inactivas')) {
                $cantidadempresas['empresas_inactivas'] = $request->empresas_inactivas;
            }

            // Si ya existe cantidadempresas, hacer merge con los nuevos datos
            $cantidadempresasExistente = json_decode($licencia->cantidadempresas, true) ?? [];
            $cantidadempresas = array_merge($cantidadempresasExistente, $cantidadempresas);

            $licencia->cantidadempresas = json_encode($cantidadempresas);
        }

        $licencia->save();
        return json_encode(["licencia" => [$licencia]]);
    }

    public function licencia_consulta(Request $request)
    {
        $licencia = Licencias::select('sis_licencias.*', "sis_clientes.nombres", "sis_clientes.identificacion", "sis_clientes.correos", "sis_clientes.telefono2")
            ->where('numerocontrato', $request->numerocontrato)
            ->join('sis_clientes', 'sis_clientes.sis_clientesid', 'sis_licencias.sis_clientesid')
            ->first();

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

            switch ($request["cliente"]['promocion']) {
                //Un mes de promocion
                case 1:
                    $fecha = date("Ymd", strtotime($nuevo->fechainicia . "+ 13 months"));
                    break;
                //Dos meses de promocion
                case 2:
                    $fecha = date("Ymd", strtotime($nuevo->fechainicia . "+ 14 months"));
                    break;
                //Tres meses de promocion
                case 3:
                    $fecha = date("Ymd", strtotime($nuevo->fechainicia . "+ 15 months"));
                    break;
                default:
                    $fecha = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 year"));
            }


            switch ($licencia['producto_id']) {
                //Facturacion Mensual
                case '61':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio = 9.50;
                    $nuevo->periodo = 1;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 month"));
                    $nuevo->producto = 2;
                    $nuevo->usuarios = 6;
                    $nuevo->numeromoviles = 1;
                    $nuevo->modulos = $this->modulos(0, 0, 1, 1, 0, 0, 0);
                    $nuevo->sis_servidoresid = 4;
                    $nuevo->tipo_licencia = 1;
                    $nuevo->empresas = 1;
                    break;
                //Facturacion Anual
                case '60':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio = 72;
                    $nuevo->periodo = 2;
                    $nuevo->fechacaduca = $fecha;
                    $nuevo->producto = 2;
                    $nuevo->usuarios = 6;
                    $nuevo->numeromoviles = 1;
                    $nuevo->modulos = $this->modulos(0, 0, 1, 1, 0, 0, 0);
                    $nuevo->sis_servidoresid = 4;
                    $nuevo->tipo_licencia = 1;
                    $nuevo->empresas = 1;
                    break;
                //Servicios Mensual
                case '59':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio = 17;
                    $nuevo->periodo = 1;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 month"));
                    $nuevo->producto = 3;
                    $nuevo->usuarios = 6;
                    $nuevo->numeromoviles = 2;
                    $nuevo->modulos = $this->modulos(1, 1, 0, 0, 0, 0, 0);
                    $nuevo->sis_servidoresid = 4;
                    $nuevo->tipo_licencia = 1;
                    $nuevo->empresas = 1;
                    break;
                //Servicios Anual
                case '58':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio = 150;
                    $nuevo->periodo = 2;
                    $nuevo->fechacaduca = $fecha;
                    $nuevo->producto = 3;
                    $nuevo->usuarios = 6;
                    $nuevo->numeromoviles = 2;
                    $nuevo->modulos = $this->modulos(1, 1, 0, 0, 0, 0, 0);
                    $nuevo->sis_servidoresid = 4;
                    $nuevo->tipo_licencia = 1;
                    $nuevo->empresas = 1;
                    break;
                //Comercial Mensual
                case '57':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio = 24;
                    $nuevo->periodo = 1;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 month"));
                    $nuevo->producto = 4;
                    $nuevo->usuarios = 6;
                    $nuevo->numeromoviles = 2;
                    $nuevo->modulos = $this->modulos(1, 1, 1, 0, 1, 1, 1);
                    $nuevo->sis_servidoresid = 4;
                    $nuevo->tipo_licencia = 1;
                    $nuevo->empresas = 1;
                    break;
                //Comercial Anual
                case '56':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio = 190;
                    $nuevo->periodo = 2;
                    $nuevo->fechacaduca = $fecha;
                    $nuevo->producto = 4;
                    $nuevo->usuarios = 6;
                    $nuevo->numeromoviles = 2;
                    $nuevo->modulos = $this->modulos(1, 1, 1, 0, 1, 1, 1);
                    $nuevo->sis_servidoresid = 4;
                    $nuevo->tipo_licencia = 1;
                    $nuevo->empresas = 1;
                    break;
                //Soy Contador Comercial Mensual
                case '63':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio = 13;
                    $nuevo->periodo = 1;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 month"));
                    $nuevo->producto = 5;
                    $nuevo->usuarios = 6;
                    $nuevo->numeromoviles = 0;
                    $nuevo->modulos = $this->modulos(1, 1, 1, 1, 0, 0, 0);
                    $nuevo->sis_servidoresid = 4;
                    $nuevo->tipo_licencia = 1;
                    $nuevo->empresas = 1;
                    break;
                //Soy Contador Comercial Anual
                case '62':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio = 108;
                    $nuevo->periodo = 2;
                    $nuevo->fechacaduca = $fecha;
                    $nuevo->producto = 5;
                    $nuevo->usuarios = 6;
                    $nuevo->numeromoviles = 0;
                    $nuevo->modulos = $this->modulos(1, 1, 1, 1, 0, 0, 0);
                    $nuevo->sis_servidoresid = 4;
                    $nuevo->tipo_licencia = 1;
                    $nuevo->empresas = 1;
                    break;
                //Soy Contador Servicios Mensual
                case '65':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio = 9.80;
                    $nuevo->periodo = 1;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 month"));
                    $nuevo->producto = 8;
                    $nuevo->usuarios = 6;
                    $nuevo->numeromoviles = 0;
                    $nuevo->modulos = $this->modulos(0, 0, 0, 0, 0, 0, 0);
                    $nuevo->sis_servidoresid = 4;
                    $nuevo->tipo_licencia = 1;
                    $nuevo->empresas = 1;
                    break;
                //Soy Contador Servicios Anual
                case '64':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio = 90;
                    $nuevo->periodo = 2;
                    $nuevo->fechacaduca = $fecha;
                    $nuevo->producto = 8;
                    $nuevo->usuarios = 6;
                    $nuevo->numeromoviles = 0;
                    $nuevo->modulos = $this->modulos(0, 0, 0, 0, 0, 0, 0);
                    $nuevo->sis_servidoresid = 4;
                    $nuevo->tipo_licencia = 1;
                    $nuevo->empresas = 1;
                    break;
                //Socio Perseo Mensual
                case '67':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio = 7;
                    $nuevo->periodo = 1;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 month"));
                    $nuevo->producto = 11;
                    $nuevo->usuarios = 1;
                    $nuevo->numeromoviles = 1;
                    $nuevo->modulos = $this->modulos(1, 1, 1, 1, 1, 1, 1);
                    $nuevo->sis_servidoresid = 4;
                    $nuevo->tipo_licencia = 1;
                    $nuevo->empresas = 1;
                    break;
                //Socio Perseo Anual
                case '66':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio = 87.50;
                    $nuevo->periodo = 2;
                    $nuevo->fechacaduca = $fecha;
                    $nuevo->producto = 11;
                    $nuevo->usuarios = 1;
                    $nuevo->numeromoviles = 1;
                    $nuevo->modulos = $this->modulos(1, 1, 1, 1, 1, 1, 1);
                    $nuevo->sis_servidoresid = 4;
                    $nuevo->tipo_licencia = 1;
                    $nuevo->empresas = 1;
                    break;
                //Facturito Inicial
                case '1':
                    $nuevo->Identificador = $contrato;
                    $nuevo->precio = 5.40;
                    $nuevo->periodo = 1;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 year"));
                    $nuevo->producto = 12;
                    $nuevo->usuarios = 6;
                    $nuevo->numeromoviles = 1;
                    $nuevo->modulos = $this->modulos(0, 0, 0, 0, 0, 0, 0);
                    $nuevo->sis_servidoresid = 2;
                    $nuevo->tipo_licencia = 1;
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
                    $nuevo->precio = 8.99;
                    $nuevo->periodo = 2;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 year"));
                    $nuevo->producto = 12;
                    $nuevo->usuarios = 6;
                    $nuevo->numeromoviles = 1;
                    $nuevo->modulos = $this->modulos(0, 0, 0, 0, 0, 0, 0);
                    $nuevo->sis_servidoresid = 2;
                    $nuevo->tipo_licencia = 1;
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
                    $nuevo->precio = 17.99;
                    $nuevo->periodo = 3;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 year"));
                    $nuevo->producto = 12;
                    $nuevo->usuarios = 6;
                    $nuevo->numeromoviles = 1;
                    $nuevo->modulos = $this->modulos(0, 0, 0, 0, 0, 0, 0);
                    $nuevo->sis_servidoresid = 2;
                    $nuevo->tipo_licencia = 1;
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
                    $nuevo->precio = 4;
                    $nuevo->periodo = 4;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 year"));
                    $nuevo->producto = 12;
                    $nuevo->usuarios = 6;
                    $nuevo->numeromoviles = 1;
                    $nuevo->modulos = $this->modulos(0, 0, 0, 0, 0, 0, 0);
                    $nuevo->sis_servidoresid = 2;
                    $nuevo->tipo_licencia = 1;
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
                    $nuevo->precio = 0;
                    $nuevo->periodo = 1;
                    $nuevo->fechacaduca = date("Ymd", strtotime($nuevo->fechainicia . "+ 1 month"));
                    $nuevo->producto = 9;
                    $nuevo->usuarios = 6;
                    $nuevo->numeromoviles = 1;
                    $nuevo->modulos = $this->modulos(1, 1, 1, 1, 1, 1, 1);
                    $nuevo->sis_servidoresid = 3;
                    $nuevo->tipo_licencia = 1;
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
                $array['fechacaduca'] = date("d-m-Y", strtotime($nuevo['fechacaduca']));
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

                $emails = array_merge($emails, [
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

            $web = Licenciasweb::where('sis_clientesid', $cliente->sis_clientesid)->get();

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

            $web = Licenciasweb::where('sis_clientesid', $cliente->sis_clientesid)->get()->toArray();

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
                    "liberar" => true,
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

    public function consultar_licencia_web_jumilo(Request $request)
    {
        // ruc hace referencia al número de contrato (habían full variables para cambiar así que se le dejó)
        $licencia = Licenciasweb::whereIn('numerocontrato', [$request->identificacion])->first();

        if ($licencia) {
            if ($licencia->producto == 12) {
                return json_encode([
                    "liberar" => true,
                    "accion" => "renovar",
                    "facturito" => true,
                    "id_licencia" => $licencia->sis_licenciasid,
                    "id_producto" => $licencia->producto,
                    "numerocontrato" => $licencia->numerocontrato,
                    "id_servidor" => $licencia->sis_servidoresid,
                ]);
            } else {
                return json_encode([
                    "liberar" => true,
                    "accion" => "renovar",
                    "facturito" => false,
                    "id_licencia" => $licencia->sis_licenciasid,
                    "id_producto" => $licencia->producto,
                    "numerocontrato" => $licencia->numerocontrato,
                    "id_servidor" => $licencia->sis_servidoresid,
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
            $array['fechacaduca'] = date("d-m-Y", strtotime($datos['fechacaduca']));
            $array['empresas'] = $datos['empresas'];
            $array['numeromoviles'] = $datos['numeromoviles'];
            $array['usuarios'] = $datos['usuarios'];
            $array['distribuidor'] = $correos->nombredistribuidor;
            $transformar = simplexml_load_string($datos['modulos']);
            $json = json_encode($transformar);
            $array['modulos'] = json_decode($json);
            $array['usuario'] = $vendedor->razonsocial;
            $array['fecha'] = date("Y-m-d H:i:s", strtotime($datos['fechamodificacion']));

            if ($datos['producto'] == 12) {
                $array['tipo'] = 8;
            } else {
                $array['tipo'] = 3;
            }

            $emails = explode(", ", $correos->distribuidor);

            $emails = array_merge($emails, [
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
                sis_licencias.modulocontable,
                sis_licencias.modulonube,
                sis_licencias.numerocontrato
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
                sis_licencias_web.modulocontable,
                 '' as modulonube,
                 sis_licencias_web.numerocontrato
            FROM
                sis_clientes
                INNER JOIN sis_licencias_web ON sis_licencias_web.sis_clientesid = sis_clientes.sis_clientesid
                INNER JOIN sis_revendedores AS vendedor ON vendedor.sis_revendedoresid = sis_clientes.sis_vendedoresid
                INNER JOIN sis_revendedores AS contador ON contador.sis_revendedoresid = sis_clientes.sis_revendedoresid
            WHERE
            sis_licencias_web.periodo<>4 AND
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
            $licencia->version_ejecutable = $request->version_ejecutable;
            $licencia->fecha_actualizacion_ejecutable = date('Y-m-d', strtotime($request->fecha_actualizacion_ejecutable));
            $licencia->fecha_respaldo = date('Y-m-d', strtotime($request->fecha_respaldo));
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

    public function actualizar_identificador(Request $request)
    {
        $licencia = Licencias::where('numerocontrato', $request->numerocontrato)->first();

        $licencia->Identificador = $request->identificador;

        // Solo actualiza ipservidor si se ha enviado en la solicitud
        if ($request->has('ipservidor')) {
            $licencia->ipservidor = $request->ipservidor;
        }

        try {
            $servidor = Servidores::where('sis_servidoresid', 4)->firstOrFail();

            $resultado = $this->externalServerService->generateLicense($servidor, $licencia->toArray());
            if (!$resultado['success']) {
                return response()->json(['success' => false, 'message' => $resultado['error']]);
            }

            $licencia->key = $resultado['license_key'];

            $licenciaActualizada = $this->ejecutarActualizacionConTransaccion(function () use ($licencia) {
                $licencia->update($licencia->toArray());
                return $licencia->fresh();
            }, 'Actualizar Identificacion API');

            return response()->json($resultado['license_key']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function jumilo()
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
        sis_licencias.modulonube,
        sis_licencias.tipo_nube,
        sis_licencias.nivel_nube,
        sis_licencias.cantidadempresas,
        sis_clientes.validado,
        sis_licencias.Identificador
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
        '' AS modulonube,
        '' AS tipo_nube,
        '' AS nivel_nube,
        '' AS cantidadempresas,
        sis_clientes.validado,
        '' AS Identificador
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
        '' AS modulonube,
        '' AS tipo_nube,
        '' AS nivel_nube,
        '' AS cantidadempresas,
        sis_clientes.validado,
        '' AS Identificador
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
        '' AS modulonube,
        '' AS tipo_nube,
        '' AS nivel_nube,
        '' AS cantidadempresas,
        sis_clientes.validado,
        '' AS Identificador
    FROM
        sis_clientes
    WHERE
        NOT EXISTS ( SELECT 1 FROM sis_licencias WHERE sis_licencias.sis_clientesid = sis_clientes.sis_clientesid )
        AND NOT EXISTS ( SELECT 1 FROM sis_licencias_web WHERE sis_licencias_web.sis_clientesid = sis_clientes.sis_clientesid )) as U
        WHERE
            u.identificacion = '0604173732'";

        return json_encode(DB::select($query));
    }
}
