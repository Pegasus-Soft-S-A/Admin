<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Licencias\LicenciasBaseController;
use App\Models\Clientes;
use App\Models\Distribuidores;
use App\Models\Grupos;
use App\Models\Licencias;
use App\Models\Licenciasvps;
use App\Models\Licenciasweb;
use App\Models\Links;
use App\Models\Revendedores;
use App\Models\Servidores;
use App\Rules\UniqueSimilar;
use App\Rules\ValidarCelular;
use App\Rules\ValidarCorreo;
use App\Services\LogService;
use App\Services\ExternalServerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\DataTables as DataTables;
use Illuminate\Support\Facades\Session;

class clientesController extends LicenciasBaseController
{
    public function index(Request $request)
    {
        $servidores = Servidores::where('estado', 1)->get();

        if (Auth::user()->tipo == 1 || Auth::user()->tipo == 6 || Auth::user()->tipo == 9) {
            $vendedores = Revendedores::where('sis_revendedores.tipo', 2)->orderBy('sis_revendedores.razonsocial')->get();
            $revendedores = Revendedores::where('sis_revendedores.tipo', 1)->orderBy('sis_revendedores.razonsocial')->get();
            $distribuidores = Distribuidores::all();
        } else {
            $vendedores = Revendedores::where('sis_revendedores.tipo', 2)->where('sis_revendedores.sis_distribuidoresid', Auth::user()->sis_distribuidoresid)->orderBy('sis_revendedores.razonsocial')->get();
            $revendedores = Revendedores::where('sis_revendedores.tipo', 1)->where('sis_revendedores.sis_distribuidoresid', Auth::user()->sis_distribuidoresid)->orderBy('sis_revendedores.razonsocial')->get();
            $distribuidores = Distribuidores::where('sis_distribuidores.sis_distribuidoresid', Auth::user()->sis_distribuidoresid)->get();
        }

        $licencias = Clientes::Clientes();

        $merged = collect($licencias);
        Session::put('data', $merged);

        return view('admin.clientes.index', compact('vendedores', 'distribuidores', 'revendedores'));
    }

    public function cargarTabla(Request $request)
    {
        if ($request->ajax()) {

            // ========== DICCIONARIOS DE DATOS ==========

            // Tipos de usuario
            $userTypes = [
                'ADMIN' => 1,
                'SOPORTE_DISTRIBUIDOR' => 3,
                'VISOR' => 6,
                'SOPORTE_MATRIZ' => 7,
                'COMERCIAL' => 8,
                'POSVENTA' => 9
            ];

            // Roles que pueden editar y ver información sensible
            $editableRoles = [$userTypes['ADMIN'], $userTypes['COMERCIAL'], $userTypes['POSVENTA']];
            $viewSensitiveRoles = [$userTypes['VISOR'], $userTypes['ADMIN'], $userTypes['COMERCIAL'], $userTypes['POSVENTA']];

            // Tipos de licencia
            $licenseTypes = config('sistema.tipos_productos');

            // Campos de fecha según tipo
            $dateFields = [
                1 => 'fechainicia',
                2 => 'fechacaduca',
                3 => 'fechaactulizaciones',
                4 => 'fechamodificacion'
            ];

            // Distribuidores específicos
            $distributors = [
                'ALFA' => 1,
                'MATRIZ' => 6,
                'SOCIO' => 12,
                'SIGMA' => 15
            ];

            // Productos Web
            $webProducts = collect(config('sistema.productos.web'))
                ->mapWithKeys(fn($producto, $id) => [$id => $producto['descripcion']])
                ->toArray();

            // Periodos de Facturito
            $facturitoPeriods = [
                1 => ' Inicial',
                2 => ' Básico',
                3 => ' Pro',
                4 => ' Gratis'
            ];

            // Periodos generales
            $billingPeriods = [
                1 => 'Mensual',
                2 => 'Anual',
                3 => 'Venta',
            ];

            // Tipos de nube
            $cloudTypes = [
                1 => 'Prime',
                2 => 'Contaplus'
            ];

            // Provincias del Ecuador
            $provinces = config('sistema.provincias');

            // ========== OBTENER PARÁMETROS DEL REQUEST ==========

            $searchParams = $this->extractSearchParameters($request);
            $distribuidores = Distribuidores::pluck('sis_distribuidoresid', 'razonsocial')->toArray();
            $links = Links::all()->toArray();
            $grupos = Grupos::all()->toArray();
            $vendedores = Revendedores::all()->toArray();

            // ========== OBTENER Y PROCESAR DATOS ==========

            $searchResult = $this->getDataWithSearch($searchParams['search']);
            $final = $searchResult['data'];
            $records = $searchResult['records'];
            $totalPages = $searchResult['totalPages'] ?? null;
            $start = $searchResult['start'] ?? null;

            // ========== APLICAR FILTROS ==========

            if ($searchParams['buscar_filtro'] == 1) {
                $final = $searchResult['merged'] ?? Session::get('data');

                // Aplicar filtro de fechas
                $final = $this->applyDateFilter($final, $searchParams['tipo'], $searchParams['fecha'], $dateFields);

                // Aplicar filtros básicos
                $final = $this->applyBasicFilters(
                    $final,
                    $searchParams['distribuidor'],
                    $searchParams['vendedor'],
                    $searchParams['revendedor'],
                    $searchParams['origen'],
                    $searchParams['validado'],
                    $searchParams['provinciasid']
                );

                // Aplicar filtros de licencia y productos
                $final = $this->applyLicenseAndProductFilter($final, $searchParams['tipolicencia'], $searchParams['producto'], $searchParams['periodo']);
            }

            // ========== FUNCIONES DE PERMISOS ==========

            $canEditClient = function ($userTipo, $userDistribuidorId, $clienteDistribuidorId, $clienteTipoLicencia) use ($userTypes, $editableRoles, $distributors) {
                // Soporte Matriz puede editar licencias Web O licencias PC que sean de SOCIO
                if ($userTipo == $userTypes['SOPORTE_MATRIZ']) {
                    if ($clienteTipoLicencia == 1 || ($clienteTipoLicencia == 2 && $clienteDistribuidorId == $distributors['SOCIO'])) {
                        return true;
                    }
                }

                // Administradores, Comerciales y Posventa siempre pueden editar
                if (in_array($userTipo, $editableRoles)) {
                    return true;
                }

                // Visores nunca pueden editar
                if ($userTipo == $userTypes['VISOR']) {
                    return false;
                }

                // Soporte Distribuidor con excepciones específicas
                if ($userTipo == $userTypes['SOPORTE_DISTRIBUIDOR']) {
                    if (($userDistribuidorId == $distributors['ALFA'] && $clienteDistribuidorId == $distributors['SIGMA']) ||
                        ($userDistribuidorId == $distributors['MATRIZ'] && $clienteDistribuidorId == $distributors['SOCIO'])
                    ) {
                        return true;
                    }
                }

                return $userDistribuidorId == $clienteDistribuidorId;
            };

            $canViewSensitiveInfo = function ($userTipo, $userDistribuidorId, $clienteDistribuidorId) use ($viewSensitiveRoles) {
                return in_array($userTipo, $viewSensitiveRoles) || $userDistribuidorId == $clienteDistribuidorId;
            };

            // ========== GENERAR DATATABLE ==========

            $datatable = $this->buildDataTable($final, $canEditClient, $canViewSensitiveInfo, $distribuidores, $vendedores, $grupos, $links, $licenseTypes, $webProducts, $facturitoPeriods, $cloudTypes, $provinces, $billingPeriods);

            // ========== PREPARAR RESPUESTA ==========

            return $this->buildDataTableResponse($datatable, $searchParams, $records, $totalPages, $start);
        }
    }

    //Extrae todos los parámetros de búsqueda del request
    private function extractSearchParameters(Request $request): array
    {
        return [
            'search' => $request->search['value'] ?? null,
            'buscar_filtro' => $request->buscar_filtro,
            'start' => $request->start,
            'length' => $request->length,
            'tipo' => $request->tipofecha,
            'tipolicencia' => $request->tipolicencia,
            'fecha' => $request->fecha,
            'distribuidor' => $request->distribuidor,
            'vendedor' => $request->vendedor,
            'revendedor' => $request->revendedor,
            'origen' => $request->origen,
            'validado' => $request->validado,
            'producto' => $request->producto,
            'periodo' => $request->periodo,
            'provinciasid' => $request->provinciasid,
        ];
    }

    //Obtiene los datos aplicando búsqueda o paginación según corresponda
    private function getDataWithSearch(?string $search): array
    {
        if ($search !== null) {
            // Modo búsqueda: obtener resultados filtrados por búsqueda
            return [
                'data' => Clientes::Clientes(0, $search),
                'records' => count(Session::get('data'))
            ];
        } else {
            // Modo paginación: obtener datos desde sesión y aplicar paginación
            $merged = Session::get('data');
            $records = $merged->count();

            // Obtener parámetros de paginación desde request global
            $start = request()->start;
            $limit = request()->length;

            // Aplicar paginación
            $final = $merged->slice($start, $limit);
            $totalPages = ceil($records / $limit);

            return [
                'data' => $final,
                'records' => $records,
                'totalPages' => $totalPages,
                'start' => $start,
                'merged' => $merged
            ];
        }
    }

    private function calcularPrecio($cliente)
    {
        $configuracion = config('sistema.productos');

        if ($cliente->tipo_licencia == 1) {
            // Licencias Web
            return $this->calcularPrecioWeb($cliente, $configuracion['web']);
        } elseif ($cliente->tipo_licencia == 2) {
            // Licencias PC
            return $this->calcularPrecioPC($cliente, $configuracion['pc']);
        }

        return 0; // VPS o tipos no definidos
    }

    private function calcularPrecioWeb($cliente, $configWeb)
    {
        $producto = $configWeb[$cliente->producto] ?? null;
        if (!$producto) return 0;

        // Casos especiales
        if ($cliente->producto == 12) { // Facturito
            $periodos = ['inicial', 'basico', 'premium', 'gratis'];
            $periodo = $periodos[$cliente->periodo - 1] ?? 'gratis';
            return $producto[$periodo]['precio'] ?? 0;
        }

        if ($cliente->producto == 4) { // Comercial - tiene módulos diferentes por período
            $periodo = $cliente->periodo == 1 ? 'mensual' : 'anual';
            return $producto[$periodo]['precio'] ?? 0;
        }

        // Productos normales
        $periodo = $cliente->periodo == 1 ? 'mensual' : 'anual';
        return $producto[$periodo]['precio'] ?? 0;
    }

    private function calcularPrecioPC($cliente, $configPC)
    {
        $modulosPrincipales = $configPC['modulos_principales'];
        $periodos = ['mensual', 'anual', 'venta'];

        // Determinar el módulo activo
        $moduloActivo = null;
        if ($cliente->modulonube == 1) {
            $moduloActivo = 'nube';
        } elseif ($cliente->modulocontable == 1) {
            $moduloActivo = 'contable';
        } elseif ($cliente->modulocontrol == 1) {
            $moduloActivo = 'control';
        } elseif ($cliente->modulopractico == 1) {
            $moduloActivo = 'practico';
        }

        if (!$moduloActivo || !isset($modulosPrincipales[$moduloActivo])) {
            return 0;
        }

        $config = $modulosPrincipales[$moduloActivo];

        if ($moduloActivo === 'nube') {
            // Para nube, usar tipo y nivel
            $tipoNube = $cliente->tipo_nube == 1 ? 'prime' : 'contaplus';
            $nivel = 'nivel' . $cliente->nivel_nube;

            return $config['precios'][$tipoNube][$nivel] ?? 0;
        } else {
            // Para otros módulos, usar período
            $periodo = $periodos[$cliente->periodo - 1] ?? 'anual';
            return $config['precios'][$periodo] ?? 0;
        }
    }

    //Construye el DataTable con todas las columnas formateadas
    private function buildDataTable($data, $canEditClient, $canViewSensitiveInfo, $distribuidores, $vendedores, $grupos, $links, $licenseTypes, $webProducts, $facturitoPeriods, $cloudTypes, $provinces, $billingPeriods)
    {
        return DataTables::of($data)
            ->editColumn('validado', function ($cliente) {
                $checked = $cliente->validado == 1 ? 'checked' : '';
                return '<label class="checkbox checkbox-single checkbox-primary mb-0"><input type="checkbox" class="checkable" ' . $checked . ' disabled><span></span></label>';
            })
            ->editColumn('numerocontrato', function ($cliente) use ($canEditClient) {
                $user = Auth::user();
                if ($canEditClient($user->tipo, $user->sis_distribuidoresid, $cliente->sis_distribuidoresid, $cliente->tipo_licencia)) {

                    // Determinar la ruta según el tipo de licencia
                    $ruta = '';
                    switch ($cliente->tipo_licencia) {
                        case 1: // Web - necesita cliente, servidor, licencia
                            $ruta = route('licencias.Web.editar', [
                                'cliente' => $cliente->sis_clientesid,
                                'servidor' => $cliente->sis_servidoresid,
                                'licencia' => $cliente->sis_licenciasid
                            ]);
                            break;
                        case 2: // PC - necesita cliente, licencia
                            $ruta = route('licencias.Pc.editar', [
                                'cliente' => $cliente->sis_clientesid,
                                'licencia' => $cliente->sis_licenciasid
                            ]);
                            break;
                        case 3: // VPS - necesita cliente, licencia
                            $ruta = route('licencias.Vps.editar', [
                                'cliente' => $cliente->sis_clientesid,
                                'licencia' => $cliente->sis_licenciasid
                            ]);
                            break;
                        default:
                            // Ruta por defecto si no coincide con ningún tipo
                            $ruta = route('clientes.editar', $cliente->sis_clientesid);
                            break;
                    }

                    return '<a class="text-success" href="' . $ruta . '" data-toggle="tooltip" title="Editar Licencia">
                    <i class="la la-cog mr-1"></i>' . $cliente->numerocontrato . '
                </a>';
                }
                return $cliente->numerocontrato;
            })
            ->editColumn('identificacion', function ($cliente) use ($canEditClient) {
                $user = Auth::user();
                if ($canEditClient($user->tipo, $user->sis_distribuidoresid, $cliente->sis_distribuidoresid, $cliente->tipo_licencia)) {
                    return '<a class="text-primary" href="' . route('clientes.editar', $cliente->sis_clientesid) . '" data-toggle="tooltip" title="Editar Cliente">
                    <i class="la la-user mr-1"></i>' . $cliente->identificacion . '
                </a>';
                }
                return $cliente->identificacion;
            })
            ->editColumn('sis_distribuidoresid', function ($cliente) use ($distribuidores) {
                $posicion = array_search($cliente->sis_distribuidoresid, $distribuidores);
                return $posicion;
            })
            ->editColumn('sis_vendedoresid', function ($cliente) use ($vendedores) {
                $posicion = array_search($cliente->sis_vendedoresid, array_column($vendedores, 'sis_revendedoresid'));
                return $vendedores[$posicion]['razonsocial'];
            })
            ->editColumn('sis_revendedoresid', function ($cliente) use ($vendedores) {
                $posicion = array_search($cliente->sis_revendedoresid, array_column($vendedores, 'sis_revendedoresid'));
                return $vendedores[$posicion]['razonsocial'];
            })
            ->editColumn('grupo', function ($cliente) use ($grupos) {
                $posicion = array_search($cliente->grupo, array_column($grupos, 'gruposid'));
                if ($posicion) {
                    return $grupos[$posicion]['descripcion'];
                }
                return '';
            })
            ->editColumn('fechainicia', function ($cliente) {
                return $cliente->fechainicia == null ? '' : date('d-m-Y', $cliente->fechainicia);
            })
            ->editColumn('fechacaduca', function ($cliente) {
                return $cliente->fechacaduca == null ? '' : date('d-m-Y', $cliente->fechacaduca);
            })
            ->editColumn('fechaultimopago', function ($cliente) {
                return $cliente->fechaultimopago == null ? '' : date('d-m-Y', $cliente->fechaultimopago);
            })
            ->editColumn('fechaactulizaciones', function ($cliente) {
                return $cliente->fechaactulizaciones == null ? '' : date('d-m-Y', $cliente->fechaactulizaciones);
            })
            ->editColumn('tipo_licencia', function ($cliente) use ($licenseTypes) {
                $texto = $licenseTypes[$cliente->tipo_licencia] ?? '';
                $icono = '';

                if ($cliente->tipo_licencia == 1) {
                    $icono = '<i class="la la-cloud text-primary mr-1"></i>';
                } elseif ($cliente->tipo_licencia == 2 || $cliente->tipo_licencia == 3) {
                    $icono = '<i class="la la-tv text-warning mr-1"></i>';
                }

                return $icono . $texto;
            })
            ->editColumn('telefono2', function ($cliente) use ($canViewSensitiveInfo) {
                $user = Auth::user();
                if ($canViewSensitiveInfo($user->tipo, $user->sis_distribuidoresid, $cliente->sis_distribuidoresid)) {
                    return $cliente->telefono2;
                }
                return '';
            })
            ->editColumn('correos', function ($cliente) use ($canViewSensitiveInfo) {
                $user = Auth::user();
                if ($canViewSensitiveInfo($user->tipo, $user->sis_distribuidoresid, $cliente->sis_distribuidoresid)) {
                    return $cliente->correos;
                }
                return '';
            })
            ->editColumn('producto', function ($cliente) use ($webProducts, $facturitoPeriods, $cloudTypes) {
                $producto = "";
                if ($cliente->tipo_licencia == 1) {
                    // Productos Web
                    $producto = $webProducts[$cliente->producto] ?? '';

                    // Si es Facturito, agregar el periodo
                    if ($cliente->producto == 12 && isset($facturitoPeriods[$cliente->periodo])) {
                        $producto .= $facturitoPeriods[$cliente->periodo];
                    }
                } else {
                    // Productos PC
                    if ($cliente->modulopractico == 1) $producto = "Práctico";
                    if ($cliente->modulocontrol == 1) $producto = "Control";
                    if ($cliente->modulocontable == 1) $producto = "Contable";
                    if ($cliente->modulonube == 1) {
                        $tipoNubeText = $cloudTypes[$cliente->tipo_nube] ?? 'Otro';
                        $nivelNubeText = "Nivel " . $cliente->nivel_nube;
                        $producto = "{$tipoNubeText} {$nivelNubeText}";
                    }
                }
                return $producto;
            })
            ->editColumn('precio', function ($cliente) {
                $precio = $this->calcularPrecio($cliente);

                // Formatear precio con ícono y estilo
                $precioFormateado = number_format($precio, 2);

                return $precioFormateado;
            })
            ->editColumn('red_origen', function ($cliente) use ($links) {
                $posicion = array_search($cliente->red_origen, array_column($links, 'sis_linksid'));
                return $links[$posicion]['codigo'];
            })
            ->editColumn('provinciasid', function ($cliente) use ($provinces) {
                return $provinces[$cliente->provinciasid] ?? '';
            })
            ->editColumn('periodo', function ($cliente) use ($billingPeriods, $facturitoPeriods) {
                // Si es Facturito (producto 12) y es licencia Web (tipo 1)
                if ($cliente->tipo_licencia == 1 && $cliente->producto == 12) {
                    return $facturitoPeriods[$cliente->periodo] ?? '';
                }

                // Para todos los demás productos usar períodos normales
                return $billingPeriods[$cliente->periodo] ?? '';
            })
            ->rawColumns(['identificacion', 'validado', 'numerocontrato', 'tipo_licencia']);
    }

    //Construye la respuesta final del DataTable según el tipo de consulta
    private function buildDataTableResponse($datatable, array $searchParams, int $records, ?int $totalPages, ?int $start)
    {
        if ($searchParams['buscar_filtro'] == 1 || $searchParams['search'] !== null) {
            // Respuesta para búsqueda o filtros aplicados
            return $datatable
                ->with('recordsTotal', $records)
                ->make(true);
        } else {
            // Respuesta para paginación normal
            return $datatable
                ->setOffset($start)
                ->with('recordsTotal', $records)
                ->with('recordsFiltered', $records)
                ->with('totalPages', $totalPages)
                ->make(true);
        }
    }

    // Aplica filtros de fecha según el tipo especificado
    private function applyDateFilter($query, $tipo, $fecha, $dateFields)
    {
        if ($tipo == null || $fecha == null) {
            return $query;
        }

        $tipo_fecha = $dateFields[$tipo] ?? null;
        if (!$tipo_fecha) {
            return $query;
        }

        $fechas = explode(" / ", $fecha);
        if (count($fechas) !== 2) {
            return $query;
        }

        $desde = $fechas[0];
        $hasta = $fechas[1];

        if ($tipo_fecha == "fechamodificacion") {
            // Para fechas de modificación usamos datetime
            $desde = date('Y-m-d H:i:s', strtotime($desde));
            $hasta = date('Y-m-d H:i:s', strtotime($hasta . ' +1 day -1 second'));
        } else {
            // Para otras fechas usamos timestamp
            $desde = strtotime(date('Y-m-d', strtotime($desde)));
            $hasta = strtotime(date('Y-m-d', strtotime($hasta)));
        }

        return $query->whereBetween($tipo_fecha, [$desde, $hasta]);
    }

    //Aplica filtros básicos (distribuidor, vendedor, revendedor, origen, validado, provincias)
    private function applyBasicFilters($query, $distribuidor, $vendedor, $revendedor, $origen, $validado, $provinciasid)
    {
        if ($distribuidor != null) {
            $query = $query->where('sis_distribuidoresid', $distribuidor);
        }

        if ($vendedor != null) {
            $query = $query->where('sis_vendedoresid', $vendedor);
        }

        if ($revendedor != null) {
            $query = $query->where('sis_revendedoresid', $revendedor);
        }

        if ($origen != null) {
            $query = $query->where('red_origen', $origen);
        }

        if ($validado != null) {
            if ($validado == 1) {
                $query = $query->where('validado', 1);
            } else {
                $query = $query->whereIn('validado', [0, null]);
            }
        }

        if ($provinciasid != null) {
            $query = $query->where('provinciasid', $provinciasid);
        }

        return $query;
    }

    // Aplica filtros de licencia y productos según el tipo de licencia
    private function applyLicenseAndProductFilter($query, $tipolicencia, $producto, $periodo)
    {
        if ($tipolicencia == "") {
            return $query; // Sin filtros adicionales
        }

        switch ($tipolicencia) {
            case '2': // Licencias Web
                $query = $query->where('tipo_licencia', 1);
                if ($producto != null) {
                    $query = $query->where('producto', $producto);
                }
                if ($periodo != null) {
                    $query = $query->where('periodo', $periodo);
                }
                break;

            case '3': // Licencias PC
                $query = $query->where('tipo_licencia', 2);
                if ($producto != null) {
                    $query = $this->applyPcProductFilter($query, $producto);
                }
                if ($periodo != null) {
                    $query = $query->where('periodo', $periodo);
                }
                break;

            case '4': // Licencias VPS
                $query = $query->where('tipo_licencia', 3);
                break;
        }

        return $query;
    }

    //Aplica filtro de productos para licencias PC
    private function applyPcProductFilter($query, $producto)
    {
        switch ($producto) {
            case '1': // Práctico
                return $query->where('modulopractico', 1);

            case '2': // Control
                return $query->where('modulocontrol', 1);

            case '3': // Contable
                return $query->where('modulocontable', 1);

            case '4': // Prime Nivel 1
                return $query->where('modulonube', 1)->where('tipo_nube', 1)->where('nivel_nube', 1);

            case '5': // Prime Nivel 2
                return $query->where('modulonube', 1)->where('tipo_nube', 1)->where('nivel_nube', 2);

            case '6': // Prime Nivel 3
                return $query->where('modulonube', 1)->where('tipo_nube', 1)->where('nivel_nube', 3);

            case '7': // Contaplus Nivel 1
                return $query->where('modulonube', 1)->where('tipo_nube', 2)->where('nivel_nube', 1);

            case '8': // Contaplus Nivel 2
                return $query->where('modulonube', 1)->where('tipo_nube', 2)->where('nivel_nube', 2);

            case '9': // Contaplus Nivel 3
                return $query->where('modulonube', 1)->where('tipo_nube', 2)->where('nivel_nube', 3);

            case '10': // Nube (general)
                return $query->where('modulonube', 1);

            default:
                return $query;
        }
    }

    public function crear()
    {
        $cliente = new Clientes();
        if (Auth::user()->tipo == 1) {
            $distribuidores = Distribuidores::all();
        } else {
            $distribuidores = Distribuidores::where('sis_distribuidoresid', Auth::user()->sis_distribuidoresid)->get();
        }
        $links = Links::where('estado', 1)->get();
        return view('admin.clientes.crear', compact('cliente', 'distribuidores', 'links'));
    }

    public function guardar(Request $request)
    {
        $this->validarDatosCliente($request);
        $this->prepararDatos($request);

        try {
            // Sincronizar con servidores
            $servidores = Servidores::where('estado', 1)->get();
            $resultado = $this->externalServerService->batchOperation(
                $servidores,
                'create_client',
                $request->all(),
                true
            );

            if (!$resultado['success']) {
                throw new \Exception($resultado['error']);
            }

            // Guardar localmente y hacer log
            $clienteGuardado = $this->ejecutarCreacionConTransaccion(
                fn() => Clientes::create($request->all()),
                'Cliente',
                $request->all()
            );

            flash('Cliente creado correctamente')->success();
            return redirect()->route('clientes.editar', $clienteGuardado->sis_clientesid);

        } catch (\Exception $e) {
            flash('Error: ' . $e->getMessage())->error();
            return back()->withInput();
        }
    }

    public function editar(Request $request, clientes $cliente)
    {
        $distribuidores = Distribuidores::all();
        $links = Links::where('estado', 1)->get();
        return view('admin.clientes.editar', compact('cliente', 'distribuidores', 'links'));
    }

    public function actualizar(Request $request, Clientes $cliente)
    {
        $this->validarDatosCliente($request, $cliente->sis_clientesid);
        $this->prepararDatos($request, $cliente);

        try {
            // Sincronizar con servidores
            $servidores = Servidores::where('estado', 1)->get();
            $resultado = $this->externalServerService->batchOperation(
                $servidores,
                'update_client',
                $cliente->toArray(),
                false
            );

            if (!$resultado['success']) {
                throw new \Exception($resultado['error']);
            }

            // Guardar localmente y hacer log
            $clienteActualizado = $this->ejecutarActualizacionConTransaccion(
                function () use ($cliente, $request) {
                    $cliente->update($request->all());
                    return $cliente;
                },
                'Cliente'
            );

            flash('Actualizado Correctamente')->success();
            return back();

        } catch (\Exception $e) {
            flash('Error: ' . $e->getMessage())->error();
            return back();
        }
    }

    public function eliminar(Clientes $cliente)
    {
        $isAjax = request()->ajax() || request()->wantsJson();

        try {
            $servidores = Servidores::where('estado', 1)->get();

            // ✅ Verificar licencias - UNA LÍNEA
            if ($licenciasCount = $this->contarLicenciasAsociadas($cliente)) {
                $mensaje = "No se puede eliminar. Tiene {$licenciasCount} licencia(s) asociada(s).";
                return $isAjax ? response()->json(['success' => false, 'message' => $mensaje], 422) : back()->with('error', $mensaje);
            }

            // Eliminar cliente localmente con transacción
            $clienteData = $cliente->toArray();
            $this->ejecutarEliminacionConTransaccion(
                function () use ($cliente) {
                    // Limpiar licencias locales
                    Licencias::where('sis_clientesid', $cliente->sis_clientesid)->delete();
                    Licenciasweb::where('sis_clientesid', $cliente->sis_clientesid)->delete();
                    Licenciasvps::where('sis_clientesid', $cliente->sis_clientesid)->delete();

                    $cliente->delete();
                    return true;
                },
                'Cliente',
                $clienteData
            );

            // Sincronizar con servidores externos usando batch
            $deleteResult = $this->externalServerService->batchOperation(
                $servidores,
                'delete_client',
                ['sis_clientesid' => $cliente->sis_clientesid],
                false
            );

            $mensaje = $deleteResult['success'] ?
                'Cliente eliminado correctamente' :
                'Cliente eliminado localmente. Algunos servidores no sincronizaron.';

            return $isAjax ?
                response()->json(['success' => true, 'message' => $mensaje]) :
                redirect()->route('clientes.index')->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            $mensaje = 'Error al eliminar cliente: ' . $e->getMessage();

            return $isAjax ?
                response()->json(['success' => false, 'message' => $mensaje], 500) :
                back()->with('error', $mensaje);
        }
    }

    // ✅ MÉTODOS AUXILIARES COMPACTOS - SOLO 6 MÉTODOS CORTOS

    private function validarDatosCliente(Request $request, $clienteId = null)
    {
        $request->validate([
            'identificacion' => ['required', new UniqueSimilar($clienteId)],
            'nombres' => 'required',
            'direccion' => 'required',
            'correos' => ['required', 'email', new ValidarCorreo],
            'provinciasid' => 'required',
            'telefono2' => ['required', 'size:10', new ValidarCelular],
            'sis_distribuidoresid' => 'required',
            'sis_vendedoresid' => 'required',
            'sis_revendedoresid' => 'required',
            'red_origen' => 'required',
            'ciudadesid' => 'required',
            'grupo' => 'required'
        ], [
            'identificacion.required' => 'Ingrese su cédula o RUC',
            'nombres.required' => 'Ingrese los Nombres',
            'direccion.required' => 'Ingrese una Dirección',
            'correos.required' => 'Ingrese un Correo',
            'correos.email' => 'Ingrese un Correo válido',
            'provinciasid.required' => 'Seleccione una Provincia',
            'telefono2.required' => 'Ingrese un Número Celular',
            'telefono2.size' => 'Ingrese 10 dígitos',
            'sis_distribuidoresid.required' => 'Seleccione un Distribuidor',
            'sis_vendedoresid.required' => 'Seleccione un Vendedor',
            'sis_revendedoresid.required' => 'Seleccione un Revendedor',
            'red_origen.required' => 'Seleccione un Origen',
            'grupo.required' => 'Seleccione un Tipo de Negocio',
            'ciudadesid.required' => 'Seleccione una Ciudad'
        ]);
    }

    private function prepararDatos(Request $request, $cliente = null)
    {
        $request['ciudadesid'] = str_pad($request->ciudadesid, '4', "0", STR_PAD_LEFT);
        $request['telefono1'] = $request['telefono1'] ?: "";

        if ($cliente) {
            // Actualización
            $request['fechamodificacion'] = now();
            $request['usuariomodificacion'] = Auth::user()->nombres;
            $request['sis_clientesid'] = $cliente->sis_clientesid;
        } else {
            // Creación
            $request['fechacreacion'] = now();
            $request['usuariocreacion'] = Auth::user()->nombres;
        }
    }

    private function contarLicenciasAsociadas(Clientes $cliente): int
    {
        $servidores = Servidores::where('estado', 1)->get();
        $licenciasWeb = 0;

        // ✅ OPTIMIZACIÓN PARA MODO LOCAL
        if (config('sistema.local_mode', false)) {
            // En modo local, consultar directamente el modelo
            $licenciasWeb = \App\Models\Licenciasweb::where('sis_clientesid', $cliente->sis_clientesid)->count();
        } else {
            // En modo producción, consultar cada servidor (lógica original)
            $servidores = Servidores::where('estado', 1)->get();

            foreach ($servidores as $servidor) {
                try {
                    $resultado = $this->externalServerService->queryLicense($servidor, [
                        'sis_clientesid' => $cliente->sis_clientesid
                    ]);

                    if ($resultado['success']) {
                        $licenciasWeb += count($resultado['licenses']);
                    }
                } catch (\Exception $e) {
                    \Log::warning("Error consultando licencias en {$servidor->descripcion}: " . $e->getMessage());
                }
            }
        }

        // Contar licencias locales
        $licenciasPC = Licencias::where('sis_clientesid', $cliente->sis_clientesid)->count();
        $licenciasVPS = Licenciasvps::where('sis_clientesid', $cliente->sis_clientesid)->count();

        return $licenciasWeb + $licenciasPC + $licenciasVPS;
    }

    //API
    public function consulta_clientes(Request $request)
    {
        $clientes = Clientes::where('sis_clientesid', $request->sis_clientesid)
            ->select(
                'sis_clientes.sis_clientesid',
                'sis_clientes.identificacion',
                'sis_clientes.nombres',
                'sis_clientes.telefono2',
                'sis_clientes.correos',
                'sis_distribuidores.razonsocial as distribuidor',
                'sis_clientes.direccion')
            ->join('sis_distribuidores', 'sis_distribuidores.sis_distribuidoresid', 'sis_clientes.sis_distribuidoresid')
            ->first();

        if (!$clientes) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        return response()->json($clientes);
    }
}
