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
use App\Models\Notificaciones;
use App\Models\Revendedores;
use App\Models\Servidores;
use App\Models\MovilVersion;
use App\Rules\ValidarCelular;
use App\Rules\ValidarCorreo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Services\ExternalServerService;


class IdentificacionesController extends Controller
{
    protected ExternalServerService $externalServerService;

    public function __construct(ExternalServerService $externalServerService)
    {
        $this->externalServerService = $externalServerService;
    }

    public function index(Request $request)
    {

        $identificacionIngresada = substr($request->identificacion, 0, 10);

        // Buscar en base de datos local
        $buscar = Identificaciones::whereIn('identificacion', [
            $identificacionIngresada,
            $request->identificacion,
            $request->identificacion . '001'
        ])->first();

        if ($buscar) {
            $buscar->parametros_json = json_decode($buscar->parametros_json);
            return response()->json($buscar);
        }

        // Si no existe localmente, consultar webservice externo
        try {
            // Configuración del servicio web
            $soapBody = '<?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <nombreCedulaRegistroCivilRequest>
                        <arg0>' . htmlspecialchars($identificacionIngresada) . '</arg0>
                        <arg1>212</arg1>
                        <arg2>1001</arg2>
                        <arg3>1001</arg3>
                        <arg4>perseo</arg4>
                        <arg5>perseo</arg5>
                        <arg6>IGESeec92e31032ab99345a4d4f3ecea</arg6>
                    </nombreCedulaRegistroCivilRequest>
                </soap:Body>
            </soap:Envelope>';

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'http://merlyna.com/merlyna/abc/webserviceSRI-RegistroCivil.php',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30, // Timeout más largo pero razonable
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $soapBody,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: text/xml; charset=utf-8',
                    'SOAPAction: http://merlyna.com//abc/webserviceSRI-RegistroCivil.php'
                ],
                CURLOPT_SSL_VERIFYPEER => false, // Solo si es necesario
                CURLOPT_SSL_VERIFYHOST => false, // Solo si es necesario
            ]);

            $response = curl_exec($curl);
            curl_close($curl);

            // Procesar respuesta XML
            libxml_use_internal_errors(true); // Habilitar manejo de errores XML
            $xml = simplexml_load_string($response);

            // Extraer datos del XML con validación
            $data = $xml->children('SOAP-ENV', true)
                ->Body
                ->children('ns1', true)
                ->nombreCedulaRegistroCivilResponse
                ->children()
                ->return ?? null;

            $arrOutput = json_decode(json_encode($data), true);

            // Crear objeto de respuesta con datos por defecto
            $datosIdentificacion = new \stdClass();
            $datosIdentificacion->identificacion = $identificacionIngresada;
            $datosIdentificacion->razon_social = $arrOutput[0] ?? 'Sin nombre';
            $datosIdentificacion->nombre_comercial = '';
            $datosIdentificacion->direccion = '';
            $datosIdentificacion->correo = '';
            $datosIdentificacion->provinciasid = '17';
            $datosIdentificacion->ciudadesid = '1701';
            $datosIdentificacion->parroquiasid = '170150';
            $datosIdentificacion->telefono1 = '';
            $datosIdentificacion->telefono2 = '';
            $datosIdentificacion->telefono3 = '';
            $datosIdentificacion->tipo_contribuyente = '0';
            $datosIdentificacion->obligado = '0';

            // Crear nueva identificación en base de datos
            $nuevaIdentificacion = $this->crearIdentificacion((object)$datosIdentificacion);

            return response()->json($nuevaIdentificacion);
        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Error interno del servidor. Por favor, intente nuevamente.'
            ], 500);
        }
    }

    public function actualiza(Request $request)
    {
        $identificacionIngresada = substr($request->identificacion, 0, 10);
        $buscar = Identificaciones::whereIn('identificacion', [$identificacionIngresada, $request->identificacion, $request->identificacion . '001'])->first();
        if ($buscar) {

            $buscar->direccion = $request->direccion == null ? "" : $request->direccion;
            $buscar->correo = $request->correo == null ? "" : $request->correo;
            $buscar->provinciasid = $request->provinciasid == null ? "" : $request->provinciasid;
            $buscar->ciudadesid = $request->ciudadesid == null ? "" : $request->ciudadesid;
            $buscar->parroquiasid = $request->parroquiasid == null ? "" : $request->parroquiasid;
            $buscar->telefono1 = $request->telefono1 == null ? "" : $request->telefono1;
            $buscar->telefono2 = $request->telefono2 == null ? "" : $request->telefono2;
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
            $buscar->tipo_identificacion = 'C';
        } elseif (strlen($request->identificacion) == 13) {
            $buscar->tipo_identificacion = 'R';
        }

        $buscar->identificacion = $request->identificacion == null ? "" : $request->identificacion;
        $buscar->razon_social = $request->razon_social == null ? "" : $request->razon_social;
        $buscar->nombre_comercial = $request->nombre_comercial == null ? "" : $request->nombre_comercial;
        $buscar->direccion = $request->direccion == null ? "" : $request->direccion;
        $buscar->correo = $request->correo == null ? "" : $request->correo;
        $buscar->provinciasid = $request->provinciasid == null ? "" : $request->provinciasid;
        $buscar->ciudadesid = $request->ciudadesid == null ? "" : $request->ciudadesid;
        $buscar->parroquiasid = $request->parroquiasid == null ? "" : $request->parroquiasid;
        $buscar->telefono1 = $request->telefono1 == null ? "" : $request->telefono1;
        $buscar->telefono2 = $request->telefono2 == null ? "" : $request->telefono2;
        $buscar->telefono3 = $request->telefono3 == null ? "" : $request->telefono3;
        $buscar->tipo_contribuyente = $request->tipo_contribuyente == null ? "" : $request->tipo_contribuyente;
        $buscar->obligado = $request->obligado == null ? "" : $request->obligado;
        $buscar->save();
        $buscar->parametros_json = json_decode($buscar->parametros_json);

        return $buscar;
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
        $request->validate([
            'correos' => ['required', 'email', new ValidarCorreo],
            'celular' => ['required', 'size:10', new ValidarCelular],
        ], [
            'correos.required' => 'Ingrese un Correo',
            'correos.email' => 'Ingrese un Correo válido',
            'celular.required' => 'Ingrese un Número Celular',
            'celular.size' => 'Ingrese 10 dígitos',
        ]);

        try {
            $cliente = Clientes::where('sis_clientesid', $request->sis_clientesid)->firstOrFail();
            $cliente->correos = $request->correos;
            $cliente->telefono2 = $request->celular;
            $cliente->validado = 1;

            // Sincronizar con servidores externos usando el servicio
            $servidores = Servidores::where('estado', 1)->get();
            $resultado = $this->externalServerService->batchOperation(
                $servidores,
                'update_client',
                $cliente->toArray()
            );

            if (!$resultado['success']) {
                return response()->json(['resultado' => 'Ocurrió un error: ' . $resultado['error']], 500);
            }

            // Guardar localmente si la sincronización fue exitosa
            $cliente->save();

            return response()->json(['resultado' => 'Ok']);

        } catch (\Exception $e) {
            return response()->json(['resultado' => 'Ocurrió un error, vuelta a intentarlo'], 500);
        }
    }

    public function generarContrato()
    {
        do {
            $numeroContrato = (string)random_int(1000000000, 9999999999);

            $existe = \App\Models\Licencias::where('numerocontrato', $numeroContrato)->exists() ||
                \App\Models\Licenciasweb::where('numerocontrato', $numeroContrato)->exists() ||
                \App\Models\Licenciasvps::where('numerocontrato', $numeroContrato)->exists();
        } while ($existe);

        return $numeroContrato;
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
            if ($producto == "") $producto = "Sin Asignar";
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


}
