<?php

namespace App\Http\Controllers;

use App\Mail\enviarlicencia;
use App\Models\Adicionales;
use App\Models\Agrupados;
use App\Models\Licencias;
use App\Models\Clientes;
use App\Models\Licenciasvps;
use App\Models\Licenciasweb;
use App\Models\Servidores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

use App\Services\LogService;

class licenciasController extends Controller
{

    public function index(Request $request, Clientes $cliente)
    {
        if ($request->ajax()) {
            $servidores = Servidores::where('estado', 1)->get();
            $web = [];

            foreach ($servidores as $servidor) {
                try {
                    $url = $servidor->dominio . '/registros/consulta_licencia';

                    // Configurar timeout más corto para evitar esperas largas
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json; charset=UTF-8',
                        'verify' => false,
                    ])
                        ->withOptions([
                            "verify" => false,
                            "timeout" => 10, // 10 segundos de timeout
                            "connect_timeout" => 5 // 5 segundos para conectar
                        ])
                        ->post($url, ['sis_clientesid' => $cliente->sis_clientesid]);

                    // Verificar si la respuesta fue exitosa
                    if ($response->successful()) {
                        $resultado = $response->json();
                        if (isset($resultado['licencias'])) {
                            $web = array_merge($web, $resultado['licencias']);
                        }
                    }
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    continue; // Continúa con el siguiente servidor
                } catch (\Illuminate\Http\Client\RequestException $e) {
                    continue;
                } catch (\Exception $e) {
                    continue;
                }
            }

            $data = Licencias::select('sis_licenciasid', 'numerocontrato', 'tipo_licencia', 'fechacaduca', 'sis_clientesid', 'sis_servidoresid')
                ->where('sis_clientesid', $cliente->sis_clientesid)
                ->get();

            $data2 = Licenciasvps::select('sis_licenciasid', 'numerocontrato', 'tipo_licencia', 'fecha_corte_cliente as fechacaduca', 'sis_clientesid', 'sis_servidoresid')
                ->where('sis_clientesid', $cliente->sis_clientesid)
                ->get();

            if ($web) {
                $unir = array_merge($web, $data->toArray(), $data2->toArray());
            } else {
                $unir = array_merge($data->toArray(), $data2->toArray());
            }

            return DataTables::of($unir)
                ->editColumn('numerocontrato', function ($data) {
                    switch ($data['tipo_licencia']) {
                        case '1':
                            return '<a class="text-primary" href="' . route('licencias.Web.editar', [$data['sis_clientesid'], $data['sis_servidoresid'], $data['sis_licenciasid']]) . '">' . $data['numerocontrato'] . ' </a>';
                            break;
                        case '2':
                            return '<a class="text-primary" href="' . route('licencias.Pc.editar', [$data['sis_clientesid'], $data['sis_licenciasid']]) . '">' . $data['numerocontrato'] . ' </a>';
                            break;
                        case '3':
                            return '<a class="text-primary" href="' . route('licencias.Vps.editar', [$data['sis_clientesid'], $data['sis_licenciasid']]) . '">' . $data['numerocontrato'] . ' </a>';
                            break;
                    }
                })
                ->editColumn('action', function ($data) {
                    // Determinar el tipo de licencia con nombre legible
                    $tipoLicencia = '';
                    switch ($data['tipo_licencia']) {
                        case '1':
                            $tipoLicencia = isset($data['producto']) && $data['producto'] == 12 ? 'Facturito' : 'Perseo Web';
                            break;
                        case '2':
                            $tipoLicencia = 'Perseo PC';
                            break;
                        case '3':
                            $tipoLicencia = 'Perseo VPS';
                            break;
                        default:
                            $tipoLicencia = 'Tipo Desconocido';
                    }

                    // Verificar permisos del usuario
                    $esAdmin = Auth::user()->tipo == 1;
                    $botones = [];

                    // Generar botones según tipo de licencia
                    switch ($data['tipo_licencia']) {
                        case '1': // Web/Facturito
                            // Botón Actividad
                            $botones[] = sprintf(
                                '<a class="btn btn-icon btn-light btn-hover-primary btn-sm mr-1 actividad"
                                       href="javascript:void(0)"
                                       data-href="%s"
                                       title="Ver Actividad"
                                       data-toggle="tooltip">
                                       <i class="fas fa-eye"></i>
                                    </a>',
                                route('licencias.actividad', [$data['sis_servidoresid'], $data['sis_licenciasid']])
                            );

                            // Botón Editar
                            $botones[] = sprintf(
                                '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-1"
                                           href="%s"
                                           title="Editar Licencia"
                                           data-toggle="tooltip">
                                           <i class="fas fa-edit"></i>
                                        </a>',
                                route('licencias.Web.editar', [$data['sis_clientesid'], $data['sis_servidoresid'], $data['sis_licenciasid']])
                            );

                            // Botón Eliminar (solo administradores)
                            if ($esAdmin) {
                                $botones[] = sprintf(
                                    '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-1 btn-eliminar-licencia"
                                               href="javascript:void(0)"
                                               data-href="%s"
                                               data-licencia-contrato="%s"
                                               data-licencia-tipo="%s"
                                               title="Eliminar Licencia"
                                               data-toggle="tooltip">
                                               <i class="fas fa-trash"></i>
                                            </a>',
                                    route('licencias.Web.eliminar', [$data['sis_servidoresid'], $data['sis_licenciasid']]),
                                    htmlspecialchars($data['numerocontrato']),
                                    htmlspecialchars($tipoLicencia)
                                );
                            }
                            break;

                        case '2': // PC
                            // Botón Editar
                            $botones[] = sprintf(
                                '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-1"
                                           href="%s"
                                           title="Editar Licencia"
                                           data-toggle="tooltip">
                                           <i class="fas fa-edit"></i>
                                        </a>',
                                route('licencias.Pc.editar', [$data['sis_clientesid'], $data['sis_licenciasid']])
                            );

                            // Botón Eliminar (solo administradores)
                            if ($esAdmin) {
                                $botones[] = sprintf(
                                    '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-1 btn-eliminar-licencia"
                                               href="javascript:void(0)"
                                               data-href="%s"
                                               data-licencia-contrato="%s"
                                               data-licencia-tipo="%s"
                                               title="Eliminar Licencia"
                                               data-toggle="tooltip">
                                               <i class="fas fa-trash"></i>
                                            </a>',
                                    route('licencias.Pc.eliminar', $data['sis_licenciasid']),
                                    htmlspecialchars($data['numerocontrato']),
                                    htmlspecialchars($tipoLicencia)
                                );
                            }
                            break;

                        case '3': // VPS
                            // Botón Editar
                            $botones[] = sprintf(
                                '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-1"
                                           href="%s"
                                           title="Editar Licencia"
                                           data-toggle="tooltip">
                                           <i class="fas fa-edit"></i>
                                        </a>',
                                route('licencias.Vps.editar', [$data['sis_clientesid'], $data['sis_licenciasid']])
                            );

                            // Botón Eliminar (solo administradores)
                            if ($esAdmin) {
                                $botones[] = sprintf(
                                    '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-1 btn-eliminar-licencia"
                                               href="javascript:void(0)"
                                               data-href="%s"
                                               data-licencia-contrato="%s"
                                               data-licencia-tipo="%s"
                                               title="Eliminar Licencia"
                                               data-toggle="tooltip">
                                               <i class="fas fa-trash"></i>
                                            </a>',
                                    route('licencias.Vps.eliminar', $data['sis_licenciasid']),
                                    htmlspecialchars($data['numerocontrato']),
                                    htmlspecialchars($tipoLicencia)
                                );
                            }
                            break;

                        default:
                            return '<span class="text-muted font-size-sm">Sin acciones</span>';
                    }

                    // Retornar botones agrupados
                    return '<div class="btn-group" role="group" aria-label="Acciones de licencia">' . implode('', $botones) . '</div>';
                })
                ->editColumn('tipo_licencia', function ($data) {
                    switch ($data['tipo_licencia']) {
                        case '1':
                            return $data['producto'] != 12 ? 'Perseo Web' : 'Facturito';
                            break;
                        case '2':
                            return 'Perseo PC';
                            break;
                        case '3':
                            return 'Perseo VPS';
                            break;
                    }
                })
                ->editColumn('fechacaduca', function ($data) {
                    return date('d-m-Y', strtotime($data['fechacaduca']));
                })
                ->rawColumns(['action', 'numerocontrato', 'tipo_licencia'])
                ->make(true);
        }
    }

    public function generarContrato()
    {
        do {
            $numeroContrato = (string)random_int(1000000000, 9999999999);

            $existe = Licencias::where('numerocontrato', $numeroContrato)->exists() ||
                Licenciasweb::where('numerocontrato', $numeroContrato)->exists() ||
                Licenciasvps::where('numerocontrato', $numeroContrato)->exists();
        } while ($existe);

        return $numeroContrato;
    }

    public function crearWeb(Clientes $cliente)
    {
        $licencia = new Licencias();
        $agrupados = Agrupados::select('sis_agrupados.sis_agrupadosid', 'sis_clientes.nombres', 'sis_agrupados.codigo')
            ->join('sis_clientes', 'sis_clientes.sis_clientesid', 'sis_agrupados.sis_clientesid')
            ->get();
        $servidores = Servidores::where('estado', 1)->get();

        $licencia->numerocontrato = $this->generarContrato();
        $licencia->numerosucursales = 0;
        $licencia->empresas = 1;
        $licencia->sis_distribuidoresid = Auth::user()->sis_distribuidoresid;

        $modulos = [
            'nomina' => false,
            'activos' => false,
            'produccion' => false,
            'restaurantes' => false,
            'talleres' => false,
            'garantias' => false,
            'ecommerce' => false,
        ];

        $modulos = json_encode($modulos);
        $modulos = json_decode($modulos);

        return view('admin.licencias.Web.crear', compact('cliente', 'licencia', 'modulos', 'servidores', 'agrupados'));
    }

    public function editarWeb(Clientes $cliente, $servidorid, $licenciaid)
    {
        $agrupados = Agrupados::select('sis_agrupados.sis_agrupadosid', 'sis_clientes.nombres', 'sis_agrupados.codigo')
            ->join('sis_clientes', 'sis_clientes.sis_clientesid', 'sis_agrupados.sis_clientesid')
            ->get();

        $servidor = Servidores::where('sis_servidoresid', $servidorid)->first();
        $url = $servidor->dominio . '/registros/consulta_licencia';

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8',
                'verify' => false,
            ])
                ->withOptions([
                    "verify" => false,
                    "timeout" => 10,
                    "connect_timeout" => 5
                ])
                ->post($url, ['sis_licenciasid' => $licenciaid]);

            // Verificar si la respuesta fue exitosa
            if (!$response->successful()) {
                flash('El servidor respondió con un error. Código: ' . $response->status())->error();
                return back();
            }

            $licenciaConsulta = $response->json();

            // Verificar que la respuesta tenga la estructura esperada
            if (!isset($licenciaConsulta['licencias'][0])) {
                flash('No se encontró la licencia en el servidor.')->error();
                return back();
            }

            $licenciaEnviar = $licenciaConsulta['licencias'][0];
            $licenciaEnviar['fechainicia'] = date("d-m-Y", strtotime($licenciaEnviar['fechainicia']));
            $licenciaEnviar['fechacaduca'] = date("d-m-Y", strtotime($licenciaEnviar['fechacaduca']));
            $licenciaEnviar['fechacreacion'] = date("Y-m-d H:i:s", strtotime($licenciaEnviar['fechacreacion']));

            if ($licenciaEnviar['fechamodificacion'] != "0000-00-00T00:00:00.000") {
                $licenciaEnviar['fechamodificacion'] = date("Y-m-d H:i:s", strtotime($licenciaEnviar['fechamodificacion']));
            } else {
                $licenciaEnviar['fechamodificacion'] = "";
            }

            $modulos = simplexml_load_string($licenciaEnviar['modulos']);
            $licenciaArray = json_encode($licenciaEnviar);
            $licencia = json_decode($licenciaArray);
            $servidores = Servidores::all();

            return view('admin.licencias.Web.editar', compact('cliente', 'licencia', 'modulos', 'servidores', 'agrupados'));
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            flash('No se pudo conectar al servidor. El servidor puede estar caído o inaccesible.')->error();
            return back();
        } catch (\Illuminate\Http\Client\RequestException $e) {
            flash('Error en la solicitud al servidor: ' . $e->getMessage())->error();
            return back();
        } catch (\Exception $e) {
            flash('Error inesperado al consultar la licencia: ' . $e->getMessage());
            return back();
        }
    }

    public function guardarWeb(Request $request)
    {
        // === PREPARACIÓN DE DATOS BÁSICOS ===
        $fechaActual = now();
        $usuarioActual = Auth::user();

        $request->merge([
            'fechacreacion' => $fechaActual,
            'usuariocreacion' => $usuarioActual->nombres,
            'fechainicia' => date('Ymd', strtotime($request->fechainicia)),
            'fechacaduca' => date('Ymd', strtotime($request->fechacaduca)),
            'fechaultimopago' => date('Ymd', strtotime($request->fechainicia)),
            'tipo_licencia' => 1,
            'Identificador' => $request->numerocontrato,
        ]);

        // === CONFIGURACIÓN DE PARÁMETROS JSON POR PRODUCTO ===
        $parametrosJson = $this->obtenerParametrosProducto($request->producto, $request->periodo ?? null);
        $request['parametros_json'] = json_encode($parametrosJson);

        // === PROCESAMIENTO DE MÓDULOS ===
        $modulosDisponibles = ['nomina', 'activos', 'produccion', 'restaurantes', 'talleres', 'garantias', 'ecommerce'];
        $modulosXml = $this->generarModulosXml($request, $modulosDisponibles);
        $request['modulos'] = $modulosXml;

        // Eliminar campos temporales del request
        $camposAEliminar = array_merge($modulosDisponibles, ['tipo']);
        foreach ($camposAEliminar as $campo) {
            unset($request[$campo]);
        }

        // === CREACIÓN DE LICENCIA EN SERVIDOR EXTERNO ===
        try {
            $servidor = Servidores::where('sis_servidoresid', $request->sis_servidoresid)->firstOrFail();
            $urlLicencia = $servidor->dominio . '/registros/crear_licencias';

            $respuestaLicencia = Http::withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8',
                'verify' => false
            ])
                ->withOptions(['verify' => false])
                ->timeout(30)
                ->post($urlLicencia, $request->all());

            if (!$respuestaLicencia->successful()) {
                throw new \Exception('Error al crear la licencia externa: ' . $respuestaLicencia->status());
            }

            $datosLicencia = $respuestaLicencia->json();
            if (!isset($datosLicencia['licencias']) || empty($datosLicencia['licencias'])) {
                throw new \Exception('Respuesta inválida del servidor de licencias');
            }

            $licenciaId = $datosLicencia['licencias'][0]['sis_licenciasid'];
            $request['sis_licenciasid'] = $licenciaId;
        } catch (\Exception $e) {
            flash('Error al crear la licencia: ' . $e->getMessage())->error();
            return redirect()->back()->withInput();
        }

        // === GUARDADO EN BASE DE DATOS LOCAL ===
        try {
            $licencia = Licenciasweb::create($request->all());
        } catch (\Exception $e) {
            flash('Error al guardar la licencia en base de datos')->error();
            return redirect()->back()->withInput();
        }

        //Registro de log
        LogService::crear('Licencia Web', $request->all());

        // === ENVÍO DE EMAIL ===
        $emailEnviado = $this->enviarEmailLicenciaWeb($licencia, $request);

        if ($emailEnviado) {
            flash('Guardado Correctamente')->success();
        } else {
            flash('Licencia creada correctamente, pero ocurrió un error al enviar el email.')->warning();
        }

        return redirect()->route('licencias.Web.editar', [$request['sis_clientesid'], $request->sis_servidoresid, $licenciaId]);
    }

    public function actualizarWeb(Request $request, $servidorid, $licenciaid)
    {
        // === PREPARACIÓN DE DATOS BÁSICOS ===
        $fechaActual = now();
        $usuarioActual = Auth::user();

        try {
            // === CONSULTA DE LICENCIA ACTUAL ===
            $servidor = Servidores::where('sis_servidoresid', $servidorid)->firstOrFail();
            $urlConsulta = $servidor->dominio . '/registros/consulta_licencia';

            $respuestaConsulta = Http::withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8',
                'verify' => false
            ])
                ->withOptions(['verify' => false])
                ->timeout(30)
                ->post($urlConsulta, ['sis_licenciasid' => $licenciaid]);

            if (!$respuestaConsulta->successful()) {
                throw new \Exception('Error al consultar la licencia: ' . $respuestaConsulta->status());
            }

            $datosConsulta = $respuestaConsulta->json();
            if (!isset($datosConsulta['licencias']) || empty($datosConsulta['licencias'])) {
                throw new \Exception('Licencia no encontrada en el servidor externo');
            }

            $licenciaActual = json_decode(json_encode($datosConsulta['licencias'][0]));
            $parametrosJson = json_decode($licenciaActual->parametros_json);

            // === PROCESAMIENTO SEGÚN TIPO DE OPERACIÓN ===
            $datosOperacion = $this->procesarTipoOperacion($request, $parametrosJson, $licenciaActual);
            $asunto = $datosOperacion['asunto'];
            $parametrosJson = $datosOperacion['parametros'];

            // === PREPARACIÓN DE DATOS PARA ACTUALIZACIÓN ===
            $request->merge([
                'fechamodificacion' => $fechaActual->format('YmdHis'),
                'usuariomodificacion' => $usuarioActual->nombres,
                'fechainicia' => date('Ymd', strtotime($request->fechainicia)),
                'fechacaduca' => date('Ymd', strtotime($request->fechacaduca)),
                'parametros_json' => json_encode($parametrosJson),
                'sis_licenciasid' => $licenciaid,
            ]);

            // === PROCESAMIENTO DE MÓDULOS ===
            $modulosDisponibles = ['nomina', 'activos', 'produccion', 'restaurantes', 'talleres', 'garantias', 'ecommerce'];
            $modulosXml = $this->generarModulosXml($request, $modulosDisponibles);
            $request['modulos'] = $modulosXml;

            // Eliminar campos temporales del request
            $camposAEliminar = array_merge($modulosDisponibles, ['tipo']);
            foreach ($camposAEliminar as $campo) {
                unset($request[$campo]);
            }

            // === ACTUALIZACIÓN EN SERVIDOR EXTERNO ===
            $urlActualizar = $servidor->dominio . '/registros/editar_licencia';

            $respuesta = Http::withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8',
                'verify' => false
            ])
                ->withOptions(['verify' => false])
                ->timeout(30)
                ->post($urlActualizar, $request->all());

            if (!$respuesta->successful()) {
                throw new \Exception('Error al actualizar en servidor externo: ' . $respuesta->status());
            }

            $datosRespuesta = $respuesta->json();
            if (!isset($datosRespuesta['licencias'])) {
                throw new \Exception('Respuesta inválida del servidor externo');
            }

            // === ACTUALIZACIÓN EN BASE DE DATOS LOCAL ===
            $licenciaweb = Licenciasweb::where('sis_licenciasid', $licenciaid)
                ->where('sis_servidoresid', $servidorid)
                ->where('sis_clientesid', $request['sis_clientesid'])
                ->firstOrFail();

            $licenciaweb->update($request->all());

            //Registro de log
            LogService::modificar('Licencia Web', $request->all());

            // === ENVÍO DE EMAIL  ===
            $emailEnviado = $this->enviarEmailLicenciaWeb($licenciaweb, $request, $asunto);

            if ($emailEnviado) {
                flash('Actualizado Correctamente')->success();
            } else {
                flash('Licencia actualizada correctamente, pero ocurrió un error al enviar el email.')->warning();
            }
        } catch (\Exception $e) {
            flash('Error al actualizar la licencia: ' . $e->getMessage())->error();
        }

        return back();
    }

    private function procesarTipoOperacion($request, $parametrosJson, $licenciaActual)
    {
        $asunto = '';

        switch ($request->tipo) {
            case 'mes':
                $request['fechacaduca'] = date("Ymd", strtotime($request->fechacaduca . "+ 1 month"));
                $request['fecha_renovacion'] = date('YmdHis', strtotime(now()));
                $request['periodo'] = 1;
                $asunto = 'Renovacion Mensual Licencia Web';
                break;

            case 'anual':
                $request['fecha_renovacion'] = date('YmdHis', strtotime(now()));
                $request['fechacaduca'] = date("Ymd", strtotime($request->fechacaduca . "+ 1 year"));
                $asunto = 'Renovacion Anual Licencia Web';

                if ($request->producto != 12) {
                    $request['periodo'] = 2;
                } else {
                    $parametrosJson = $this->procesarDocumentosFacturito($parametrosJson, $request->periodo);
                }
                break;

            case 'recargar':
                $parametrosJson->Documentos = $parametrosJson->Documentos + 120;
                $request['fechacaduca'] = date('Ymd', strtotime($request->fechacaduca));
                $asunto = $licenciaActual->producto == 9 ?
                    'Recarga 120 Documentos Perseo Web Lite' :
                    'Recarga 120 Documentos Perseo Web Emprendedor';
                break;

            case 'recargar240':
                $parametrosJson->Documentos = $parametrosJson->Documentos + 240;
                $request['fechacaduca'] = date('Ymd', strtotime($request->fechacaduca));
                $asunto = 'Recarga 240 Documentos Perseo Web Emprendedor';
                break;

            default:
                $request['fechacaduca'] = date('Ymd', strtotime($request->fechacaduca));
                $asunto = 'Modificar Licencia Web';
                break;
        }

        // Procesamiento adicional para producto 12 (Facturito)
        if ($request->producto == 12 && $licenciaActual->periodo != $request->periodo) {
            $parametrosJson = $this->procesarDocumentosFacturito($parametrosJson, $request->periodo);
        }

        return [
            'asunto' => $asunto,
            'parametros' => $parametrosJson
        ];
    }

    private function procesarDocumentosFacturito($parametrosJson, $periodo)
    {
        switch ($periodo) {
            case '1': // Inicial
                $parametrosJson->Documentos = $parametrosJson->Documentos + 60;
                break;
            case '2': // Básico
                $parametrosJson->Documentos = $parametrosJson->Documentos + 150;
                break;
            case '3': // Pro
                $parametrosJson->Documentos = 100000;
                break;
            case '4': // Gratis
                $parametrosJson->Documentos = 30;
                break;
        }

        return $parametrosJson;
    }

    private function obtenerParametrosProducto($producto, $periodo = null)
    {
        $parametros = [
            'Documentos' => "0",
            'Productos' => "0",
            'Almacenes' => "0",
            'Nomina' => "0",
            'Produccion' => "0",
            'Activos' => "0",
            'Talleres' => "0",
            'Garantias' => "0",
        ];

        switch ($producto) {
            case '6': // Lite anterior
                $parametros['Documentos'] = "120";
                $parametros['Productos'] = "500";
                $parametros['Almacenes'] = "1";
                $parametros['Nomina'] = "3";
                $parametros['Produccion'] = "3";
                $parametros['Activos'] = "3";
                $parametros['Talleres'] = "3";
                $parametros['Garantias'] = "3";
                break;

            case '9': // Lite
                $parametros['Documentos'] = "100000";
                $parametros['Productos'] = "100000";
                $parametros['Almacenes'] = "1";
                $parametros['Nomina'] = "3";
                $parametros['Produccion'] = "3";
                $parametros['Activos'] = "3";
                $parametros['Talleres'] = "3";
                $parametros['Garantias'] = "3";
                break;

            case '10': // Emprendedor
                $parametros['Documentos'] = "120";
                break;

            case '11': // Socio
                $parametros['Documentos'] = "5";
                break;

            case '12': // Plan con períodos
                $documentosPorPeriodo = [
                    '1' => "60",    // Inicial
                    '2' => "150",   // Básico
                    '3' => "100000", // Pro
                    '4' => "30",    // Gratis
                ];
                $parametros['Documentos'] = $documentosPorPeriodo[$periodo] ?? "0";
                break;
        }

        return $parametros;
    }

    private function generarModulosXml($request, $modulosDisponibles)
    {
        $xw = xmlwriter_open_memory();
        xmlwriter_start_document($xw, '1.0', 'UTF-8');
        xmlwriter_start_element($xw, 'modulos');

        foreach ($modulosDisponibles as $modulo) {
            xmlwriter_start_element($xw, $modulo);
            xmlwriter_text($xw, $request->$modulo === 'on' ? 1 : 0);
            xmlwriter_end_element($xw);
        }

        xmlwriter_end_element($xw);
        xmlwriter_end_document($xw);

        return xmlwriter_output_memory($xw);
    }

    private function obtenerDescripcionPeriodo($producto, $periodo)
    {
        if ($producto == 12) {
            $periodos = [
                '1' => 'Inicial',
                '2' => 'Básico',
                '3' => 'Premium',
                '4' => 'Gratis',
            ];
            return $periodos[$periodo] ?? 'No definido';
        }

        return $periodo == 1 ? 'Mensual' : 'Anual';
    }

    private function enviarEmailLicenciaWeb($licencia, $request, $asuntoPersonalizado = null)
    {
        try {
            // Obtener datos del cliente
            $cliente = Clientes::select(
                'sis_clientes.correos',
                'sis_clientes.nombres',
                'sis_clientes.identificacion',
                'sis_distribuidores.correos AS distribuidor',
                'sis_revendedores.correo AS vendedor',
                'revendedor.correo AS revendedor',
                'sis_revendedores.razonsocial',
                'sis_distribuidores.razonsocial AS nombredistribuidor'
            )
                ->join('sis_distribuidores', 'sis_distribuidores.sis_distribuidoresid', 'sis_clientes.sis_distribuidoresid')
                ->join('sis_revendedores', 'sis_revendedores.sis_revendedoresid', 'sis_clientes.sis_vendedoresid')
                ->join('sis_revendedores as revendedor', 'revendedor.sis_revendedoresid', 'sis_clientes.sis_vendedoresid')
                ->where('sis_clientesid', $request['sis_clientesid'])
                ->firstOrFail();

            // Preparar datos del email
            $datosEmail = [
                'view' => 'emails.licenciaweb',
                'from' => env('MAIL_FROM_ADDRESS'),
                'subject' => $asuntoPersonalizado ?? 'Nuevo Registro Licencia Web',
                'cliente' => $cliente->nombres,
                'vendedor' => $cliente->razonsocial,
                'identificacion' => $cliente->identificacion,
                'correos' => $cliente->correos,
                'numerocontrato' => $licencia['numerocontrato'],
                'producto' => $licencia['producto'],
                'distribuidor' => $cliente->nombredistribuidor,
                'periodo' => $this->obtenerDescripcionPeriodo($request['producto'], $request['periodo'] ?? null),
                'fechainicia' => date("d-m-Y", strtotime($licencia['fechainicia'])),
                'fechacaduca' => date("d-m-Y", strtotime($licencia['fechacaduca'])),
                'empresas' => $licencia['empresas'],
                'numeromoviles' => $licencia['numeromoviles'],
                'usuarios' => $licencia['usuarios'],
                'modulos' => json_decode(json_encode(simplexml_load_string($licencia['modulos']))),
                'usuario' => Auth::user()->nombres,
                'fecha' => $licencia['fechacreacion'] ?? date("Y-m-d H:i:s", strtotime($request['fechamodificacion'] ?? now())),
                'tipo' => $request['producto'] == 12 ? ($asuntoPersonalizado ? 8 : 7) : ($asuntoPersonalizado ? 3 : 1),
            ];

            // Preparar lista de emails
            $emails = array_filter(array_merge(
                explode(", ", $cliente->distribuidor),
                [
                    "facturacion@perseo.ec",
                    $cliente->vendedor,
                    $cliente->revendedor,
                    $cliente->correos,
                    Auth::user()->correo,
                ]
            ));

            // Enviar email solo en producción
            if (config('app.env') !== 'local') {
                Mail::to($emails)->queue(new enviarlicencia($datosEmail));
            }

            return true; // Email enviado correctamente

        } catch (\Exception $e) {
            return false; // Error al enviar email
        }
    }

    public function eliminarWeb($servidorid, $licenciaid)
    {
        $isAjax = request()->ajax() || request()->wantsJson();

        try {
            DB::beginTransaction();

            // Obtener servidor
            $servidor = Servidores::where('sis_servidoresid', $servidorid)->first();
            if (!$servidor) {
                if ($isAjax) {
                    return response()->json(['success' => false, 'message' => 'Servidor no encontrado.'], 404);
                }
                flash('Servidor no encontrado.')->error();
                return back();
            }

            // Consultar licencia en servidor externo
            $urlConsulta = $servidor->dominio . '/registros/consulta_licencia';
            $licenciaConsulta = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false])
                ->withOptions(["verify" => false])
                ->timeout(10)
                ->post($urlConsulta, ['sis_licenciasid' => $licenciaid])
                ->json();

            if (!isset($licenciaConsulta['licencias'][0])) {
                if ($isAjax) {
                    return response()->json(['success' => false, 'message' => 'Licencia no encontrada.'], 404);
                }
                flash('Licencia no encontrada.')->error();
                return back();
            }

            $licenciaData = $licenciaConsulta['licencias'][0];

            // Verificar dependencias
            $adicionales = Adicionales::where('numerocontrato', $licenciaData['numerocontrato'])->count();
            if ($adicionales > 0) {
                $mensaje = "No se puede eliminar la licencia porque tiene {$adicionales} recurso(s) adicional(es) asociado(s).";
                if ($isAjax) {
                    return response()->json(['success' => false, 'message' => $mensaje], 422);
                }
                flash($mensaje)->error();
                return back();
            }

            // Eliminar del servidor externo
            $urlEliminar = $servidor->dominio . '/registros/eliminar_licencia';
            $eliminarLicencia = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false])
                ->withOptions(["verify" => false])
                ->timeout(15)
                ->post($urlEliminar, ['sis_licenciasid' => $licenciaid])
                ->json();

            if (!isset($eliminarLicencia['respuesta'])) {
                throw new \Exception('Error al eliminar la licencia del servidor externo.');
            }

            // Eliminar de base de datos local
            $licenciaweb = Licenciasweb::where('sis_licenciasid', $licenciaid)
                ->where('sis_servidoresid', $servidorid)
                ->where('sis_clientesid', $licenciaData['sis_clientesid'])
                ->first();

            if ($licenciaweb) {
                $licenciaweb->delete();
            }

            // Eliminar recursos adicionales
            Adicionales::where('numerocontrato', $licenciaData['numerocontrato'])->delete();

            // Registro de log
            LogService::eliminar('Licencia Web', $licenciaData);

            DB::commit();

            // Respuesta exitosa
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'respuesta' => true // Mantener compatibilidad con código existente
                ]);
            }

            flash('Licencia eliminada correctamente')->success();
            return back();

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error eliminando licencia Web: ' . $e->getMessage());

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar la licencia: ' . $e->getMessage()
                ], 500);
            }

            flash('Ocurrió un error, vuelva a intentarlo')->error();
            return back();
        }
    }

    public function crearPC(Clientes $cliente)
    {
        $licencia = new Licencias();
        $licencia->fechacaduca = date("d-m-Y", strtotime(date("d-m-Y") . "+ 5 year"));
        $licencia->fechacaduca_soporte = date("d-m-Y", strtotime(date("d-m-Y") . "+ 1 year"));
        $licencia->numeroequipos = 1;
        $licencia->numeromoviles = 0;
        $licencia->numerosucursales = 0;
        $licencia->usuario = "perseo";
        $licencia->clave = "Invencible4050*";
        $licencia->ipservidor = "127.0.0.1";
        $licencia->puerto = "5588";
        $licencia->puertows = "80";
        $licencia->actulizaciones = 1;
        $licencia->aplicaciones = " s";
        $licencia->plan_soporte = 1;
        $licencia->fechaactulizaciones = date("d-m-Y", strtotime(date("d-m-Y") . "+ 1 month"));
        $licencia->sis_distribuidoresid = $cliente->sis_distribuidoresid;
        $licencia->numerocontrato = $this->generarContrato();
        $licencia->periodo = 3;
        $modulos = [];
        $modulos = [
            'nomina' => false,
            'activos' => false,
            'produccion' => false,
            'restaurante' => false,
            'talleres' => false,
            'garantias' => false,
            'operadoras' => false,
            'encomiendas' => false,
            'crm_cartera' => false,
            'tienda_perseo_publico' => false,
            'tienda_perseo_distribuidor' => false,
            'perseo_hybrid' => false,
            'tienda_woocommerce' => false,
            'api_whatsapp' => false,
            'cash_manager' => false,
            'reporte_equifax' => false,
        ];

        $modulos = json_encode([$modulos]);
        $modulos = json_decode($modulos);

        return view('admin.licencias.PC.crear', compact('cliente', 'licencia', 'modulos'));
    }

    public function editarPC(Clientes $cliente, Licencias $licencia)
    {
        $modulos = json_decode($licencia->modulos);
        if (empty($licencia->cantidadempresas)) {
            $empresas = [];
            $empresas = [
                'empresas_activas' => 0,
                'empresas_inactivas' => 0,
            ];
            $empresas = json_decode(json_encode($empresas));
        } else {
            $empresas = json_decode($licencia->cantidadempresas);
        }
        $licencia->fechacaduca = date("d-m-Y", strtotime($licencia->fechacaduca));
        $licencia->fechacaduca_soporte = date("d-m-Y", strtotime($licencia->fechacaduca_soporte));
        $licencia->fechaactulizaciones = date("d-m-Y", strtotime($licencia->fechaactulizaciones));
        $licencia->fecha_actualizacion_ejecutable = date("d-m-Y", strtotime($licencia->fecha_actualizacion_ejecutable));
        $licencia->fecha_respaldo = date("d-m-Y", strtotime($licencia->fecha_respaldo));

        //Consulta de adicionales
        $adicionales = Adicionales::where('numerocontrato', $licencia->numerocontrato)->get();

        $tiposLicencia = config('sistema.tipos_productos');
        $tiposAdicional = config('sistema.tipos_venta_adicionales');

        // Transformar ambos campos
        $adicionales->transform(function ($adicional) use ($tiposAdicional, $tiposLicencia) {
            $adicional->tipo_adicional = $tiposAdicional[$adicional->tipo_adicional] ?? $adicional->tipo_adicional;
            $adicional->tipo_licencia = $tiposLicencia[$adicional->tipo_licencia] ?? $adicional->tipo_licencia;

            return $adicional;
        });

        return view('admin.licencias.PC.editar', compact('cliente', 'licencia', 'modulos', 'empresas', 'adicionales'));
    }

    public function guardarPC(Request $request)
    {
        // === VALIDACIONES ===
        $request->validate(
            [
                'Identificador' => ['required', 'unique:sis_licencias'],
                'correopropietario' => ['required', 'email'],
                'correoadministrador' => ['required', 'email'],
                'correocontador' => ['required', 'email'],
            ],
            [
                'Identificador.required' => 'Ingrese un Identificador',
                'Identificador.unique' => 'El identificador ya se encuentra registrado',
                'correopropietario.required' => 'Ingrese un Correo de Propietario',
                'correopropietario.email' => 'Ingrese un Correo de Propietario válido',
                'correoadministrador.required' => 'Ingrese un Correo de Administrador',
                'correoadministrador.email' => 'Ingrese un Correo de Administrador válido',
                'correocontador.required' => 'Ingrese un Correo de Contador',
                'correocontador.email' => 'Ingrese un Correo de Contador válido',
            ]
        );

        if (!$request->modulopractico && !$request->modulocontrol && !$request->modulocontable && !$request->modulonube) {
            flash("Debe seleccionar al menos un sistema principal (Práctico, Control, Contable o Nube)")->error();
            return redirect()->back()->withInput();
        }

        // Verificar si el módulo nube está activo
        $moduloNubeActivo = $request->modulonube === 'on' || $request->modulonube == 1;

        if (!$moduloNubeActivo) {
            // Si no es nube, establecer valores por defecto
            $request->merge([
                'usuarios' => 0,              // Valor por defecto para usuarios
            ]);
        }

        // === PREPARACIÓN DE DATOS BÁSICOS ===
        $fechaActual = now();
        $usuarioActual = Auth::user();

        $request->merge([
            'fechacreacion' => $fechaActual,
            'fechainicia' => date('Y-m-d', strtotime($fechaActual)),
            'fechacaduca' => date('Y-m-d', strtotime($request->fechacaduca)),
            'fechacaduca_soporte' => date('Y-m-d', strtotime($request->fechacaduca_soporte)),
            'fechaactulizaciones' => date('Y-m-d', strtotime($request->fechaactulizaciones)),
            'fechaultimopago' => date('Y-m-d', strtotime($fechaActual)),
            'usuariocreacion' => $usuarioActual->nombres,
        ]);

        // === PROCESAMIENTO DE MÓDULOS PRINCIPALES ===
        $modulosPrincipales = ['modulopractico', 'modulocontrol', 'modulocontable', 'modulonube', 'actulizaciones', 'plan_soporte'];
        foreach ($modulosPrincipales as $modulo) {
            $request[$modulo] = $request->$modulo === 'on' ? 1 : 0;
        }

        // === PROCESAMIENTO DE MÓDULOS ADICIONALES ===
        $modulosAdicionales = [
            'nomina' => 'nomina',
            'activos' => 'activos',
            'produccion' => 'produccion',
            'restaurante' => 'restaurante',
            'talleres' => 'talleres',
            'garantias' => 'garantias',
            'operadoras' => 'tvcable',
            'encomiendas' => 'encomiendas',
            'crm_cartera' => 'crmcartera',
            'tienda_perseo_distribuidor' => 'integraciones',
            'tienda_perseo_publico' => 'tienda',
            'perseo_hybrid' => 'hybrid',
            'tienda_woocommerce' => 'woocomerce',
            'api_whatsapp' => 'apiwhatsapp',
            'cash_manager' => 'cashmanager',
            'cash_debito' => 'cashdebito',
            'reporte_equifax' => 'equifax',
            'caja_ahorros' => 'ahorros',
            'academico' => 'academico',
            'perseo_contador' => 'perseo_contador',
            'api_urbano' => 'api_urbano',
        ];

        $modulos = [];
        $camposAEliminar = ['tipo', 'aplicaciones_permisos'];

        foreach ($modulosAdicionales as $moduloKey => $requestField) {
            $modulos[$moduloKey] = $request->$requestField === 'on';
            $request[$requestField] = $modulos[$moduloKey];
            $camposAEliminar[] = $requestField;
        }

        // === ASIGNACIÓN DE CAMPOS ADICIONALES ===
        $request->merge([
            'motivobloqueo' => $request->motivobloqueo ?? '',
            'mensaje' => $request->mensaje ?? '',
            'observacion' => $request->observacion ?? '',
            'ipservidorremoto' => $request->ipservidorremoto ?? '',
            'tokenrespaldo' => $request->tokenrespaldo ?? '',
            'numerogratis' => 0,
            'tipo_licencia' => 2,
            'aplicaciones' => $request->aplicaciones_permisos,
            'modulos' => json_encode([$modulos])
        ]);

        // Eliminar campos temporales del request
        foreach ($camposAEliminar as $campo) {
            unset($request[$campo]);
        }

        // === GENERACIÓN DE LICENCIA EXTERNA ===
        try {
            $servidor = Servidores::where('sis_servidoresid', 4)->firstOrFail();
            $urlLicencia = $servidor->dominio . '/registros/generador_licencia';

            $respuestaLicencia = Http::withHeaders([
                'Content-Type' => 'application/json',
                'verify' => false
            ])
                ->withOptions(['verify' => false])
                ->timeout(30)
                ->post($urlLicencia, $request->all());

            if (!$respuestaLicencia->successful()) {
                throw new \Exception('Error al generar la licencia externa: ' . $respuestaLicencia->status());
            }

            $datosLicencia = $respuestaLicencia->json();
            if (!isset($datosLicencia['licencia'])) {
                throw new \Exception('Respuesta inválida del servidor de licencias');
            }

            $request['key'] = $datosLicencia['licencia'];
        } catch (\Exception $e) {
            flash('Error al generar la licencia: ' . $e->getMessage())->error();
            return redirect()->back()->withInput();
        }

        // === GUARDADO EN BASE DE DATOS ===
        try {
            $licencia = Licencias::create($request->all());
        } catch (\Exception $e) {
            flash('Error al guardar la licencia en base de datos')->error();
            return redirect()->back()->withInput();
        }

        //Registro de log
        LogService::crear('Licencia PC', $licencia);

        // === ENVÍO DE EMAIL ===
        $this->enviarEmailLicencia($licencia, 'Nuevo Registro Licencia PC', '2', $fechaActual);

        flash('Guardado Correctamente')->success();
        return redirect()->route('licencias.Pc.editar', [$request['sis_clientesid'], $licencia->sis_licenciasid]);
    }

    public function actualizarPC(Request $request, Licencias $licencia)
    {
        // === VALIDACIONES ===
        $request->validate(
            [
                'Identificador' => ['required', 'unique:sis_licencias,identificador,' . $licencia->sis_licenciasid . ',sis_licenciasid'],
                'correopropietario' => ['required', 'email'],
                'correoadministrador' => ['required', 'email'],
                'correocontador' => ['required', 'email'],
            ],
            [
                'Identificador.required' => 'Ingrese un Identificador',
                'Identificador.unique' => 'El identificador ya se encuentra registrado',
                'correopropietario.required' => 'Ingrese un Correo de Propietario',
                'correopropietario.email' => 'Ingrese un Correo de Propietario válido',
                'correoadministrador.required' => 'Ingrese un Correo de Administrador',
                'correoadministrador.email' => 'Ingrese un Correo de Administrador válido',
                'correocontador.required' => 'Ingrese un Correo de Contador',
                'correocontador.email' => 'Ingrese un Correo de Contador válido',
            ]
        );

        if (!$request->modulopractico && !$request->modulocontrol && !$request->modulocontable && !$request->modulonube) {
            flash("Debe seleccionar al menos un sistema principal (Práctico, Control, Contable o Nube)")->error();
            return redirect()->back()->withInput();
        }

        // === PROCESAMIENTO DE FECHAS Y ASUNTO SEGÚN TIPO DE ACTUALIZACIÓN ===
        $fechaActual = now();
        $usuarioActual = Auth::user();

        $tiposActualizacion = [
            'mes' => [
                'fechacaduca_modificador' => '+ 1 month',
                'fechaactualizaciones_modificador' => '+ 1 month',
                'asunto' => 'Renovación Mensual Perseo PC'
            ],
            'anual' => [
                'fechacaduca_modificador' => '+ 1 year',
                'fechaactualizaciones_modificador' => '+ 1 year',
                'asunto' => 'Renovación Anual Perseo PC'
            ],
            'actualizacion' => [
                'fechacaduca_modificador' => null,
                'fechaactualizaciones_modificador' => '+ 1 year',
                'asunto' => 'Actualización Anual Perseo PC'
            ]
        ];

        $tipoActualizacion = $request->tipo ?? 'default';
        $configuracion = $tiposActualizacion[$tipoActualizacion] ?? null;

        if ($configuracion) {
            $fechaCaduca = $configuracion['fechacaduca_modificador']
                ? date("Y-m-d", strtotime($request->fechacaduca . ' ' . $configuracion['fechacaduca_modificador']))
                : date('Y-m-d', strtotime($request->fechacaduca));

            $fechaActualizaciones = date('Y-m-d', strtotime($request->fechaactulizaciones . ' ' . $configuracion['fechaactualizaciones_modificador']));
            $asuntoEmail = $configuracion['asunto'];
        } else {
            $fechaCaduca = date('Y-m-d', strtotime($request->fechacaduca));
            $fechaActualizaciones = date('Y-m-d', strtotime($request->fechaactulizaciones));
            $asuntoEmail = 'Modificación Registro Licencia PC';
        }

        // === ASIGNACIÓN DE DATOS BÁSICOS ===
        $request->merge([
            'fechacaduca' => $fechaCaduca,
            'fechaactulizaciones' => $fechaActualizaciones,
            'fechamodificacion' => $fechaActual,
            'usuariomodificacion' => $usuarioActual->nombres,
            'fechacaduca_soporte' => date('Y-m-d', strtotime($request->fechacaduca_soporte)),
            'aplicaciones' => $request->aplicaciones_permisos,
        ]);

        // === PROCESAMIENTO DE MÓDULOS PRINCIPALES ===
        $modulosPrincipales = [
            'modulopractico',
            'modulocontrol',
            'modulocontable',
            'modulonube',
            'actulizaciones',
            'plan_soporte'
        ];

        foreach ($modulosPrincipales as $modulo) {
            $request[$modulo] = $request->$modulo === 'on' ? 1 : 0;
        }

        // === PROCESAMIENTO DE CAMPOS OPCIONALES ===
        $camposOpcionales = ['tokenrespaldo', 'ipservidorremoto', 'motivobloqueo', 'mensaje', 'observacion'];
        foreach ($camposOpcionales as $campo) {
            $request[$campo] = $request->$campo ?? '';
        }

        // === PROCESAMIENTO DE MÓDULOS ADICIONALES ===
        $modulosAdicionales = [
            'nomina' => 'nomina',
            'activos' => 'activos',
            'produccion' => 'produccion',
            'restaurante' => 'restaurante',
            'talleres' => 'talleres',
            'garantias' => 'garantias',
            'operadoras' => 'tvcable',
            'encomiendas' => 'encomiendas',
            'crm_cartera' => 'crmcartera',
            'tienda_perseo_distribuidor' => 'integraciones',
            'tienda_perseo_publico' => 'tienda',
            'perseo_hybrid' => 'hybrid',
            'tienda_woocommerce' => 'woocomerce',
            'api_whatsapp' => 'apiwhatsapp',
            'cash_manager' => 'cashmanager',
            'cash_debito' => 'cashdebito',
            'reporte_equifax' => 'equifax',
            'caja_ahorros' => 'ahorros',
            'academico' => 'academico',
            'perseo_contador' => 'perseo_contador',
            'api_urbano' => 'api_urbano',
        ];

        $modulos = [];
        $camposAEliminar = [
            'tipo',
            'empresas_activas',
            'empresas_inactivas',
            'aplicaciones_permisos'
        ];

        foreach ($modulosAdicionales as $moduloKey => $requestField) {
            $modulos[$moduloKey] = $request->$requestField === 'on';
            $request[$requestField] = $modulos[$moduloKey];
            $camposAEliminar[] = $requestField;
        }

        $request['modulos'] = json_encode([$modulos]);

        // Eliminar campos temporales del request
        foreach ($camposAEliminar as $campo) {
            unset($request[$campo]);
        }

        // === GENERACIÓN DE LICENCIA EXTERNA ===
        try {
            $servidor = Servidores::where('sis_servidoresid', 4)->firstOrFail();
            $urlLicencia = $servidor->dominio . '/registros/generador_licencia';

            $respuestaLicencia = Http::withHeaders([
                'Content-Type' => 'application/json',
                'verify' => false
            ])
                ->withOptions(['verify' => false])
                ->timeout(30)
                ->post($urlLicencia, $request->all());

            if (!$respuestaLicencia->successful()) {
                throw new \Exception('Error al generar la licencia externa: ' . $respuestaLicencia->status());
            }

            $datosLicencia = $respuestaLicencia->json();
            if (!isset($datosLicencia['licencia'])) {
                throw new \Exception('Respuesta inválida del servidor de licencias');
            }

            $request['key'] = $datosLicencia['licencia'];
        } catch (\Exception $e) {
            flash('Error al generar la licencia: ' . $e->getMessage())->error();
            return back()->withInput();
        }

        // === ACTUALIZACIÓN EN BASE DE DATOS ===
        try {
            $licencia->update($request->all());
            $licencia->refresh();
        } catch (\Exception $e) {
            flash('Error al actualizar la licencia en base de datos: ' . $e->getMessage())->error();
            return back()->withInput();
        }

        //Registro de log
        LogService::modificar('Licencia PC', $licencia);

        // === ENVÍO DE EMAIL ===
        $this->enviarEmailLicencia($licencia, $asuntoEmail, '4', $fechaActual);

        flash('Actualizada Correctamente')->success();
        return back();
    }

    private function enviarEmailLicencia(Licencias $licencia, string $asunto, string $tipoEmail, $fecha)
    {
        // === OBTENCIÓN DE DATOS DEL CLIENTE ===
        $cliente = Clientes::select(
            'sis_clientes.correos',
            'sis_distribuidores.correos AS distribuidor',
            'sis_clientes.nombres',
            'sis_clientes.identificacion',
            'sis_revendedores.correo AS vendedor'
        )
            ->join('sis_distribuidores', 'sis_distribuidores.sis_distribuidoresid', 'sis_clientes.sis_distribuidoresid')
            ->join('sis_revendedores', 'sis_revendedores.sis_revendedoresid', 'sis_clientes.sis_vendedoresid')
            ->where('sis_clientesid', $licencia->sis_clientesid)
            ->first();

        $usuarioActual = Auth::user();

        // === PREPARACIÓN DE DATOS PARA EMAIL ===
        $datosEmail = [
            'view' => 'emails.licenciapc',
            'from' => env('MAIL_FROM_ADDRESS'),
            'subject' => $asunto,
            'cliente' => $cliente->nombres,
            'identificacion' => $cliente->identificacion,
            'correo' => $cliente->correos,
            'numerocontrato' => $licencia->numerocontrato,
            'identificador' => $licencia->Identificador,
            'modulopractico' => $licencia->modulopractico,
            'modulocontable' => $licencia->modulocontable,
            'modulocontrol' => $licencia->modulocontrol,
            'modulonube' => $licencia->modulonube,
            'tipo_nube' => $licencia->tipo_nube,
            'nivel_nube' => $licencia->nivel_nube,
            'ipservidor' => $licencia->ipservidor,
            'ipservidorremoto' => $licencia->ipservidorremoto,
            'numeroequipos' => $licencia->numeroequipos,
            'numeromoviles' => $licencia->numeromoviles,
            'numerosucursales' => $licencia->numerosucursales,
            'modulos' => json_decode($licencia->modulos),
            'usuario' => $usuarioActual->nombres,
            'fecha' => $fecha,
            'tipo' => $tipoEmail,
            'fechaactulizaciones' => $licencia->fechaactulizaciones,
        ];

        // === PREPARACIÓN DE EMAILS DESTINATARIOS ===
        $emailsDestinatarios = array_filter(
            array_merge(
                explode(", ", $cliente->distribuidor ?? ''),
                [
                    "facturacion@perseo.ec",
                    $cliente->vendedor ?? '',
                    $usuarioActual->correo ?? '',
                ]
            ),
            fn($email) => !empty(trim($email))
        );

        // === ENVÍO DE EMAIL ===
        try {
            if (config('app.env') !== 'local') {
                Mail::to($emailsDestinatarios)->queue(new enviarlicencia($datosEmail));
            }
            return true;
        } catch (\Exception $e) {
            //\Log::error('Error enviando email de licencia: ' . $e->getMessage());
            flash('Operación completada correctamente, pero hubo un error enviando el email de notificación')->warning();
            return false;
        }
    }

    public function eliminarPc(Licencias $licencia)
    {
        $isAjax = request()->ajax() || request()->wantsJson();

        try {
            DB::beginTransaction();

            // Verificar dependencias
            $adicionales = Adicionales::where('numerocontrato', $licencia->numerocontrato)->count();
            if ($adicionales > 0) {
                $mensaje = "No se puede eliminar la licencia porque tiene {$adicionales} recurso(s) adicional(es) asociado(s).";
                if ($isAjax) {
                    return response()->json(['success' => false, 'message' => $mensaje], 422);
                }
                flash($mensaje)->error();
                return back();
            }

            // Guardar datos para el log
            $licenciaData = $licencia->toArray();

            // Eliminar recursos adicionales y licencia
            Adicionales::where('numerocontrato', $licencia->numerocontrato)->delete();
            $licencia->delete();

            // Registro de log
            LogService::eliminar('Licencia PC', $licenciaData);

            DB::commit();

            // Respuesta exitosa
            if ($isAjax) {
                return response()->json(['success' => true]);
            }

            flash('Licencia eliminada correctamente')->success();
            return back();

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error eliminando licencia PC: ' . $e->getMessage());

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar la licencia: ' . $e->getMessage()
                ], 500);
            }

            flash('Ocurrió un error, vuelva a intentarlo')->error();
            return back();
        }
    }

    public function crearVps(Clientes $cliente)
    {
        $licencia = new Licencias();
        $licencia->numerocontrato = $this->generarContrato();
        return view('admin.licencias.Vps.crear', compact('licencia', 'cliente'));
    }

    public function guardarVps(Request $request)
    {
        //Validaciones
        $request->validate(
            [
                'usuario' => ['required'],
                'clave' => ['required'],
                'ip' => ['required'],
            ],
            [
                'usuario.required' => 'Ingrese un Usuario',
                'clave.required' => 'Ingrese una Clave',
                'ip.required' => 'Ingrese una IP',
            ],
        );
        //Asignacion masiva para los campos asignados en guarded o fillable en el modelo
        $request['fechacreacion'] = date('Y-m-d H:i:s', strtotime(now()));
        $request['usuariocreacion'] = Auth::user()->nombres;
        $request['fecha_corte_proveedor'] = date('Ymd', strtotime($request->fecha_corte_proveedor));
        $request['fecha_corte_cliente'] = date('Ymd', strtotime($request->fecha_corte_cliente));
        $request['tipo_licencia'] = 3;

        $licencia = Licenciasvps::create($request->all());

        LogService::crear('Licencia Vps', $licencia);

        $cliente = Clientes::select('sis_clientes.correos', 'sis_clientes.nombres', 'sis_clientes.identificacion', 'sis_distribuidores.correos AS distribuidor', 'sis_revendedores.correo AS vendedor', 'revendedor.correo AS revendedor', 'sis_revendedores.razonsocial', 'sis_distribuidores.razonsocial AS nombredistribuidor')
            ->join('sis_distribuidores', 'sis_distribuidores.sis_distribuidoresid', 'sis_clientes.sis_distribuidoresid')
            ->join('sis_revendedores', 'sis_revendedores.sis_revendedoresid', 'sis_clientes.sis_vendedoresid')
            ->join('sis_revendedores as revendedor', 'revendedor.sis_revendedoresid', 'sis_clientes.sis_vendedoresid')
            ->where('sis_clientesid', $request['sis_clientesid'])
            ->first();

        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['subject'] = 'Crear Licencia VPS';
        $array['cliente'] = $cliente->nombres;
        $array['identificacion'] = $cliente->identificacion;
        $array['correo'] = $cliente->correos;
        $array['numerocontrato'] = $licencia->numerocontrato;
        $array['ip'] = $licencia->ip;
        $array['fecha_corte_proveedor'] = date("d-m-Y", strtotime($licencia->fecha_corte_proveedor));
        $array['fecha_corte_cliente'] = date("d-m-Y", strtotime($licencia->fecha_corte_cliente));
        $array['usuario'] = Auth::user()->nombres;
        $array['fecha'] = $request['fechacreacion'];
        $array['tipo'] = '10';

        $emails = explode(", ", $cliente->distribuidor);

        $emails = array_merge($emails, [
            "facturacion@perseo.ec",
            $cliente->vendedor,
            $cliente->revendedor,
            $cliente->correos,
            Auth::user()->correo,
        ]);

        $emails = array_diff($emails, array(" ", 0, null));
        try {
            if (config('app.env') !== 'local') {
                Mail::to($emails)->queue(new enviarlicencia($array));
            }
        } catch (\Exception $e) {
            flash('Error enviando email')->error();
            return redirect()->route('licencias.Vps.editar', [$request['sis_clientesid'], $licencia->sis_licenciasid]);
        }

        flash('Guardado Correctamente')->success();
        return redirect()->route('licencias.Vps.editar', [$request['sis_clientesid'], $licencia->sis_licenciasid]);
    }

    public function editarVPS(Clientes $cliente, Licenciasvps $licencia)
    {
        $licencia->fecha_corte_proveedor = date("d-m-Y", strtotime($licencia->fecha_corte_proveedor));
        $licencia->fecha_corte_cliente = date("d-m-Y", strtotime($licencia->fecha_corte_cliente));

        return view('admin.licencias.Vps.editar', compact('cliente', 'licencia'));
    }

    public function actualizarVPS(Request $request, Licenciasvps $licencia)
    {
        //Validaciones
        $request->validate(
            [
                'usuario' => ['required'],
                'clave' => ['required'],
                'ip' => ['required'],
            ],
            [
                'usuario.required' => 'Ingrese un Usuario',
                'clave.required' => 'Ingrese una Clave',
                'ip.required' => 'Ingrese una IP',
            ],
        );

        $request['fecha_corte_proveedor'] = date("Ymd", strtotime($request->fecha_corte_proveedor));
        $request['fecha_corte_cliente'] = date("Ymd", strtotime($request->fecha_corte_cliente));
        $request['fechamodificacion'] = now();
        $request['usuariomodificacion'] = Auth::user()->nombres;

        $licencia->update($request->all());

        LogService::modificar('Licencia Vps', $licencia);

        $cliente = Clientes::select('sis_clientes.correos', 'sis_distribuidores.correos AS distribuidor', 'sis_clientes.nombres', 'sis_clientes.identificacion', 'sis_revendedores.correo AS vendedor')
            ->join('sis_distribuidores', 'sis_distribuidores.sis_distribuidoresid', 'sis_clientes.sis_distribuidoresid')
            ->join('sis_revendedores', 'sis_revendedores.sis_revendedoresid', 'sis_clientes.sis_vendedoresid')
            ->where('sis_clientesid', $licencia->sis_clientesid)
            ->first();

        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['subject'] = 'Modificar Licencia VPS';
        $array['cliente'] = $cliente->nombres;
        $array['identificacion'] = $cliente->identificacion;
        $array['correo'] = $cliente->correos;
        $array['numerocontrato'] = $licencia->numerocontrato;
        $array['ip'] = $licencia->ip;
        $array['fecha_corte_proveedor'] = $licencia->fecha_corte_proveedor;
        $array['fecha_corte_cliente'] = $licencia->fecha_corte_cliente;
        $array['usuario'] = Auth::user()->nombres;
        $array['fecha'] = $request['fechamodificacion'];
        $array['tipo'] = '11';

        $emails = explode(", ", $cliente->distribuidor);

        $emails = array_merge($emails, [
            "facturacion@perseo.ec",
            $cliente->vendedor,
            Auth::user()->correo,
        ]);

        $emails = array_diff($emails, array(" ", 0, null));

        try {
            if (config('app.env') !== 'local') {
                Mail::to($emails)->queue(new enviarlicencia($array));
            }
        } catch (\Exception $e) {
            flash('Error enviando email')->error();
            return back();
        }

        flash('Actualizado Correctamente')->success();
        return back();
    }

    public function eliminarVps(Licenciasvps $licencia)
    {
        $isAjax = request()->ajax() || request()->wantsJson();

        try {
            DB::beginTransaction();

            // Verificar dependencias (aunque VPS normalmente no las tiene)
            $adicionales = Adicionales::where('numerocontrato', $licencia->numerocontrato)->count();
            if ($adicionales > 0) {
                $mensaje = "No se puede eliminar la licencia porque tiene {$adicionales} recurso(s) adicional(es) asociado(s).";
                if ($isAjax) {
                    return response()->json(['success' => false, 'message' => $mensaje], 422);
                }
                flash($mensaje)->error();
                return back();
            }

            // Guardar datos para el log
            $licenciaData = $licencia->toArray();

            // Eliminar recursos adicionales y licencia
            Adicionales::where('numerocontrato', $licencia->numerocontrato)->delete();
            $licencia->delete();

            // Registro de log
            LogService::eliminar('Licencia VPS', $licenciaData);

            DB::commit();

            // Respuesta exitosa
            if ($isAjax) {
                return response()->json(['success' => true]);
            }

            flash('Licencia eliminada correctamente')->success();
            return back();

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error eliminando licencia VPS: ' . $e->getMessage());

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar la licencia: ' . $e->getMessage()
                ], 500);
            }

            flash('Ocurrió un error, vuelva a intentarlo')->error();
            return back();
        }
    }

    public function enviarEmail($clienteId, $productoId)
    {
        $cliente = Clientes::select('sis_clientes.nombres', 'sis_clientes.identificacion', 'sis_clientes.correos', 'sis_distribuidores.correos as distribuidor')
            ->join('sis_distribuidores', 'sis_distribuidores.sis_distribuidoresid', 'sis_clientes.sis_distribuidoresid')
            ->where('sis_clientesid', $clienteId)
            ->first();

        if ($cliente != null) {

            if ($productoId == 12) {
                $array['view'] = 'emails.envio_credenciales_facturito';
                $array['tipo'] = 9;
            } else {
                $array['tipo'] = 5;
                $array['view'] = 'emails.envio_credenciales';
            }

            $array['from'] = env('MAIL_FROM_ADDRESS');
            $array['subject'] = 'Envio Credenciales ';
            $array['nombre'] = $cliente->nombres;
            $array['usuario'] = $cliente->identificacion;

            $emails = explode(", ", $cliente->distribuidor);
            array_push($emails, $cliente->correos);
            $emails = array_diff($emails, array(" ", 0, null));

            try {
                if (config('app.env') !== 'local') {
                    Mail::to($emails)->queue(new enviarlicencia($array));
                }
            } catch (\Exception $e) {
                flash('Error enviando email')->error();
                return back();
            }

            flash('Correo Enviado Correctamente')->success();
            return back();
        }
    }

    public function actividad($servidorid, $licenciaid)
    {
        $servidor = Servidores::where('sis_servidoresid', $servidorid)->first();

        $url = $servidor->dominio . '/registros/consulta_actividades';
        $resultado = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->post($url, ['sis_licenciasid' => $licenciaid])
            ->json();
        if (isset($resultado['actividades'])) {
            return with($resultado);
        } else {
            return with(["actividades" => []]);
        }
    }

    public function editarClave(Clientes $cliente, Servidores $servidor, $licenciaid)
    {
        $url = $servidor->dominio . '/registros/restaurar_clave_usuario';
        $usuario = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->post($url, ['sis_licenciasid' => $licenciaid, 'identificacion' => substr($cliente->identificacion, 0, 10)])
            ->json();
        if (isset($usuario['respuesta'])) {
            $resultado = ['mensaje' => "Clave Reseteada Correctamente", "tipo" => 'success'];
        } else {
            $resultado = ['mensaje' => $usuario['fault']['detail'], "tipo" => 'warning'];
        }
        return $resultado;
    }
}
