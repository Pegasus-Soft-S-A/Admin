<?php

namespace App\Http\Controllers;

use App\Models\Ciudades;
use App\Models\Clientes;
use App\Models\Distribuidores;
use App\Models\Grupos;
use App\Models\Licencias;
use App\Models\Licenciasweb;
use App\Models\Links;
use App\Models\Log;
use App\Models\Revendedores;
use App\Models\Servidores;
use App\Rules\UniqueSimilar;
use App\Rules\ValidarCelular;
use App\Rules\ValidarCorreo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\DataTables as DataTables;
use Illuminate\Support\Facades\Session;

class clientesController extends Controller
{
    public function index(Request $request)
    {
        $servidores = Servidores::where('estado', 1)->get();

        if (Auth::user()->tipo == 1 || Auth::user()->tipo == 6) {
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
            $licenseTypes = [
                1 => 'Web',
                2 => 'PC',
                3 => 'VPS'
            ];

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
            $webProducts = [
                2 => 'Facturación',
                3 => 'Servicios',
                4 => 'Comercial',
                5 => 'Soy Contador Comercial',
                6 => 'Perseo Lite Anterior',
                7 => 'Total',
                8 => 'Soy Contador Servicios',
                9 => 'Perseo Lite',
                10 => 'Emprendedor',
                11 => 'Socio Perseo',
                12 => 'Facturito'
            ];

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
                2 => 'Anual'
            ];

            // Tipos de nube
            $cloudTypes = [
                1 => 'Prime',
                2 => 'Contaplus'
            ];

            // Provincias del Ecuador
            $provinces = [
                1 => 'AZUAY',
                2 => 'BOLIVAR',
                3 => 'CAÑAR',
                4 => 'CARCHI',
                5 => 'CHIMBORAZO',
                6 => 'COTOPAXI',
                7 => 'EL ORO',
                8 => 'ESMERALDAS',
                9 => 'GUAYAS',
                10 => 'IMBABURA',
                11 => 'LOJA',
                12 => 'LOS RIOS',
                13 => 'MANABI',
                14 => 'MORONA SANTIAGO',
                15 => 'NAPO',
                16 => 'PASTAZA',
                17 => 'PICHINCHA',
                18 => 'TUNGURAHUA',
                19 => 'ZAMORA CHINCHIPE',
                20 => 'GALAPAGOS',
                21 => 'SUCUMBIOS',
                22 => 'ORELLANA',
                23 => 'SANTO DOMINGO DE LOS TSACHILAS',
                24 => 'SANTA ELENA'
            ];

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

    // ========== MÉTODOS DE BÚSQUEDA Y PAGINACIÓN ==========

    /**
     * Extrae todos los parámetros de búsqueda del request
     */
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

    /**
     * Obtiene los datos aplicando búsqueda o paginación según corresponda
     */
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

    /**
     * Construye el DataTable con todas las columnas formateadas
     */
    private function buildDataTable($data, $canEditClient, $canViewSensitiveInfo, $distribuidores, $vendedores, $grupos, $links, $licenseTypes, $webProducts, $facturitoPeriods, $cloudTypes, $provinces, $billingPeriods)
    {
        return DataTables::of($data)
            ->editColumn('validado', function ($cliente) {
                $checked = $cliente->validado == 1 ? 'checked' : '';
                return '<label class="checkbox checkbox-single checkbox-primary mb-0"><input type="checkbox" class="checkable" ' . $checked . ' disabled><span></span></label>';
            })
            ->editColumn('identificacion', function ($cliente) use ($canEditClient) {
                $user = Auth::user();
                if ($canEditClient($user->tipo, $user->sis_distribuidoresid, $cliente->sis_distribuidoresid, $cliente->tipo_licencia)) {
                    return '<a class="text-primary" href="' . route('clientes.editar', $cliente->sis_clientesid) . '">' . $cliente->identificacion . ' </a>';
                }
                return $cliente->identificacion;
            })
            ->editColumn('action', function ($cliente) use ($canEditClient) {
                $user = Auth::user();
                if ($canEditClient($user->tipo, $user->sis_distribuidoresid, $cliente->sis_distribuidoresid, $cliente->tipo_licencia)) {
                    return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('clientes.editar', $cliente->sis_clientesid) . '" title="Editar"> <i class="la la-edit"></i> </a>';
                }
                return '';
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
                return $cliente->fechainicia == null ? date('d-m-Y', strtotime(now()))  : date('d-m-Y', $cliente->fechainicia);
            })
            ->editColumn('fechacaduca', function ($cliente) {
                return $cliente->fechacaduca == null ? date('d-m-Y', strtotime(now()))  : date('d-m-Y', $cliente->fechacaduca);
            })
            ->editColumn('fechaultimopago', function ($cliente) {
                return $cliente->fechaultimopago == null ? date('d-m-Y', strtotime(now()))  : date('d-m-Y', $cliente->fechaultimopago);
            })
            ->editColumn('fechaactulizaciones', function ($cliente) {
                return $cliente->fechaactulizaciones == null ? date('d-m-Y', strtotime(now()))  : date('d-m-Y', $cliente->fechaactulizaciones);
            })
            ->editColumn('tipo_licencia', function ($cliente) use ($licenseTypes) {
                return $licenseTypes[$cliente->tipo_licencia] ?? '';
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
            ->editColumn('red_origen', function ($cliente) use ($links) {
                $posicion = array_search($cliente->red_origen, array_column($links, 'sis_linksid'));
                return $links[$posicion]['codigo'];
            })
            ->editColumn('provinciasid', function ($cliente) use ($provinces) {
                return $provinces[$cliente->provinciasid] ?? '';
            })
            ->editColumn('periodo', function ($cliente) use ($billingPeriods) {
                return $billingPeriods[$cliente->periodo] ?? '';
            })
            ->rawColumns(['action', 'identificacion', 'validado']);
    }

    /**
     * Construye la respuesta final del DataTable según el tipo de consulta
     */
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

    // ========== MÉTODOS DE FILTROS ==========

    /**
     * Aplica filtros de fecha según el tipo especificado
     */
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

    /**
     * Aplica filtros básicos (distribuidor, vendedor, revendedor, origen, validado, provincias)
     */
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

    /**
     * Aplica filtros de tipo de licencia y productos
     */
    private function applyLicenseAndProductFilter($query, $tipolicencia, $producto, $periodo)
    {
        if ($tipolicencia == 1) {
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
                break;

            case '4': // Licencias VPS
                $query = $query->where('tipo_licencia', 3);
                break;
        }

        return $query;
    }

    /**
     * Aplica filtros específicos para productos PC
     */
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
        //Validaciones
        $request->validate(
            [
                'identificacion' => ['required', new UniqueSimilar],
                'nombres' => 'required',
                'direccion' => 'required',
                'correos' => ['required', 'email', new ValidarCorreo],
                'provinciasid' => 'required',
                //'telefono1' => ['required', 'min:7|max:10'],
                'telefono2' => ['required', 'size:10', new ValidarCelular],
                'sis_distribuidoresid' => 'required',
                'sis_vendedoresid' => 'required',
                'sis_revendedoresid' => 'required',
                'red_origen' => 'required',
                'ciudadesid' => 'required',
                'grupo' => 'required'
            ],
            [
                'identificacion.required' => 'Ingrese su cédula o RUC ',
                'nombres.required' => 'Ingrese los Nombres',
                'direccion.required' => 'Ingrese una Dirección',
                'correos.required' => 'Ingrese un Correo',
                'correos.email' => 'Ingrese un Correo válido',
                'provinciasid.required' => 'Seleccione una Provincia',
                'telefono1.required' => 'Ingrese un Número Convencional',
                //'telefono1.min' => 'Mínimo 7 dígitos',
                //'telefono1.max' => 'Máximo 10 dígitos',
                'telefono2.required' => 'Ingrese un Número Celular',
                'telefono2.size' => 'Ingrese 10 dígitos',
                'sis_distribuidoresid.required' => 'Seleccione un Distribuidor',
                'sis_vendedoresid.required' => 'Seleccione un Vendedor',
                'sis_revendedoresid.required' => 'Seleccione un Revendedor',
                'red_origen.required' => 'Seleccione un Origen',
                'grupo.required' => 'Seleccione un Tipo de Negocio',
                'ciudadesid.required' => 'Seleccione una Ciudad'
            ],
        );

        $request['fechacreacion'] = now();
        $request['usuariocreacion'] = Auth::user()->nombres;
        $request['ciudadesid'] = str_pad($request->ciudadesid, '4', "0", STR_PAD_LEFT);
        $request['telefono1'] = $request['telefono1'] <> "" ? $request['telefono1'] : "";

        DB::beginTransaction();

        $servidores = Servidores::all();
        $cliente = Clientes::create($request->all());

        $clientes_creados = []; // variable para almacenar los clientes creados en los servidores remotos

        // Verificar si se creó el cliente en el servidor local
        if (!$cliente) {
            flash('Ocurrió un error al crear el cliente')->warning();
            DB::rollBack();
            return back();
        }

        $request['sis_clientesid'] = $cliente->sis_clientesid;

        // Insertar el cliente en cada uno de los servidores remotos
        foreach ($servidores as $servidor) {
            $url = $servidor->dominio . '/registros/crear_clientes';
            $crearCliente = Http::withHeaders(['Content-Type' => 'application/json; ', 'verify' => false])
                ->withOptions(["verify" => false])
                ->post($url, $request->all())
                ->json();

            if (isset($crearCliente['sis_clientes'])) {
                $clientes_creados[] = [
                    'dominio' => $servidor->dominio,
                    'sis_clientesid' => $crearCliente["sis_clientes"][0]['sis_clientesid']
                ];
            } else {
                foreach ($clientes_creados as $registro) {
                    $url = $registro['dominio'] . '/registros/eliminar_cliente';
                    $eliminarCliente = Http::withHeaders(['Content-Type' => 'application/json; ', 'verify' => false])
                        ->withOptions(["verify" => false])
                        ->post($url, ["sis_clientesid" => $registro['sis_clientesid']])
                        ->json();
                }

                flash('Ocurrió un error al crear el cliente, intentelo nuevamente')->warning();
                DB::rollBack();
                return back();
            }
        }

        $log = new Log();
        $log->usuario = Auth::user()->nombres;
        $log->pantalla = "Clientes";
        $log->tipooperacion = "Crear";
        $log->fecha = now();
        $log->detalle = $cliente;
        $log->save();
        $request['sis_clientesid'] = $cliente->sis_clientesid;

        DB::commit();

        flash('Guardado Correctamente')->success();
        return redirect()->route('clientes.editar', $cliente->sis_clientesid);
    }

    public function editar(Request $request, clientes $cliente)
    {
        $distribuidores = Distribuidores::all();
        $links = Links::where('estado', 1)->get();
        return view('admin.clientes.editar', compact('cliente', 'distribuidores', 'links'));
    }

    public function actualizar(Request $request, Clientes $cliente)
    {
        //Validaciones
        $request->validate(
            [
                'identificacion' => ['required', new UniqueSimilar($cliente->sis_clientesid)],
                'nombres' => 'required',
                'direccion' => 'required',
                'correos' => ['required', 'email', new ValidarCorreo],
                'provinciasid' => 'required',
                //'telefono1' => ['required', 'min:7|max:10'],
                'telefono2' => ['required', 'size:10', new ValidarCelular],
                'sis_distribuidoresid' => 'required',
                'sis_vendedoresid' => 'required',
                'sis_revendedoresid' => 'required',
                'red_origen' => 'required',
                'ciudadesid' => 'required',
                'grupo' => 'required'
            ],
            [
                'identificacion.required' => 'Ingrese su cédula o RUC ',
                'nombres.required' => 'Ingrese los Nombres',
                'direccion.required' => 'Ingrese una Dirección',
                'correos.required' => 'Ingrese un Correo',
                'correos.email' => 'Ingrese un Correo válido',
                'provinciasid.required' => 'Seleccione una Provincia',
                'telefono1.required' => 'Ingrese un Número Convencional',
                //'telefono1.min' => 'Mínimo 7 dígitos',
                //'telefono1.max' => 'Máximo 10 dígitos',
                'telefono2.required' => 'Ingrese un Número Celular',
                'telefono2.size' => 'Ingrese 10 dígitos',
                'sis_distribuidoresid.required' => 'Seleccione un Distribuidor',
                'sis_vendedoresid.required' => 'Seleccione un Vendedor',
                'sis_revendedoresid.required' => 'Seleccione un Revendedor',
                'red_origen.required' => 'Seleccione un Origen',
                'grupo.required' => 'Seleccione un Tipo de Negocio',
                'ciudadesid.required' => 'Seleccione una Ciudad'
            ],
        );


        DB::beginTransaction();
        try {
            $servidores = Servidores::where('estado', 1)->get();
            $request['ciudadesid'] = str_pad($request->ciudadesid, '4', "0", STR_PAD_LEFT);
            $request['fechamodificacion'] =  now();
            $request['usuariomodificacion'] = Auth::user()->nombres;
            $request['telefono1'] = $request['telefono1'] <> "" ? $request['telefono1'] : "";

            $cliente->update($request->all());

            $log = new Log();
            $log->usuario = Auth::user()->nombres;
            $log->pantalla = "Clientes";
            $log->tipooperacion = "Modificar";
            $log->fecha = now();
            $log->detalle = $cliente;
            $log->save();

            $request['sis_clientesid'] = $cliente->sis_clientesid;
            $request['fechamodificacion'] =   date('YmdHis', strtotime($request['fechamodificacion']));

            foreach ($servidores as $servidor) {

                $urlEditar = $servidor->dominio . '/registros/editar_clientes';
                $clienteEditar = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                    ->withOptions(["verify" => false])
                    ->post($urlEditar, $request->all())
                    ->json();

                if (!isset($clienteEditar['sis_clientes'])) {
                    DB::rollBack();
                    flash('Ocurrió un error vuelva a intentarlo')->warning();
                    return back();
                }
            }
            flash('Guardado Correctamente')->success();
            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
            flash('Ocurrió un error vuelva a intentarlo')->warning();
        };
        return back();
    }

    public function eliminar(Clientes $cliente)
    {

        $servidores = Servidores::all();
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

        $data = Licencias::select('sis_licenciasid', 'numerocontrato', 'tipo_licencia', 'fechacaduca', 'sis_clientesid', 'sis_servidoresid')
            ->where('sis_clientesid', $cliente->sis_clientesid)
            ->get();

        if ($web) {
            $unir = array_merge($web, $data->toArray());
        } else {
            $unir =  $data->toArray();
        }
        if (count($unir) > 0) {
            flash('Existen licencias creadas para este cliente')->error();
            return back();
        }


        DB::beginTransaction();
        try {
            $servidores = Servidores::where('estado', 1)->get();

            foreach ($servidores as $servidor) {
                $url = $servidor->dominio . '/registros/eliminar_cliente';
                $eliminarCliente = Http::withHeaders(['Content-Type' => 'application/json; ', 'verify' => false])
                    ->withOptions(["verify" => false])
                    ->post($url, ["sis_clientesid" => $cliente->sis_clientesid])
                    ->json();

                if (!isset($eliminarCliente['respuesta'])) {
                    DB::rollBack();
                    flash('Ocurrió un error vuelva a intentarlo')->warning();
                    return back();
                }
            }

            Licencias::where('sis_clientesid', $cliente->sis_clientesid)->delete();
            Licenciasweb::where('sis_clientesid', $cliente->sis_clientesid)->delete();

            $cliente->delete();
            $log = new Log();
            $log->usuario = Auth::user()->nombres;
            $log->pantalla = "Cliente";
            $log->tipooperacion = "Eliminar";
            $log->fecha = now();
            $log->detalle = $cliente;
            $log->save();
            flash('Eliminado Correctamente')->success();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            flash('Ocurrió un error vuelva a intentarlo')->warning();
            return back();
        };
        return redirect()->route('clientes.index');
    }
}
