<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Distribuidores;
use App\Models\Licencias;
use App\Models\Log;
use App\Models\Revendedores;
use App\Models\Servidores;
use App\Rules\ValidarCelular;
use App\Rules\ValidarCorreo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\DataTables as DataTables;

class clientesController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->tipo == 1) {
            $vendedores = Revendedores::where('sis_revendedores.tipo', 2)->orderBy('sis_revendedores.razonsocial')->get();
            $distribuidores = Distribuidores::all();
        } else {
            $vendedores = Revendedores::where('sis_revendedores.tipo', 2)->where('sis_revendedores.sis_distribuidoresid', Auth::user()->sis_distribuidoresid)->orderBy('sis_revendedores.razonsocial')->get();
            $distribuidores = Distribuidores::where('sis_distribuidores.sis_distribuidoresid', Auth::user()->sis_distribuidoresid)->get();
        }
        return view('admin.clientes.index', compact('vendedores', 'distribuidores'));
    }

    public function cargarTabla(Request $request)
    {
        $servidores = Servidores::where('estado', 1)->get();
        $web = [];

        if ($request->ajax()) {

            $tipo = $request->tipofecha;
            $tipolicencia = $request->tipolicencia;
            $fecha = $request->fecha;
            $distribuidor = $request->distribuidor;
            $vendedor = $request->vendedor;
            $origen = $request->origen;
            $producto = $request->producto;
            $periodo = $request->periodo;
            $provinciasid = $request->provinciasid;
            $distribuidores = Distribuidores::all()->toArray();
            $vendedores = Revendedores::all()->toArray();

            if (Auth::user()->tipo == 1 || Auth::user()->tipo == 2) {
                $clientes = Clientes::select(
                    'sis_clientes.sis_clientesid',
                    'sis_clientes.identificacion',
                    'sis_clientes.nombres',
                    'sis_clientes.telefono1',
                    'sis_clientes.telefono2',
                    'sis_clientes.correos',
                    'sis_licencias.tipo_licencia',
                    DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechainicia) as fechainicia'),
                    DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechacaduca) as fechacaduca'),
                    DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechaactulizaciones) as fechaactulizaciones'),
                    DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechaultimopago) as fechaultimopago'),
                    DB::RAW('DATEDIFF(sis_licencias.fechacaduca,NOW()) as diasvencer'),
                    'sis_licencias.numerocontrato',
                    'sis_licencias.precio',
                    'sis_licencias.periodo',
                    'sis_licencias.producto',
                    'sis_clientes.red_origen',
                    'sis_clientes.sis_distribuidoresid',
                    'sis_clientes.sis_vendedoresid',
                    'sis_clientes.sis_revendedoresid',
                    'sis_clientes.provinciasid',
                    'sis_licencias.empresas',
                    'sis_licencias.usuarios',
                    'sis_licencias.numeroequipos',
                    'sis_licencias.numeromoviles',
                    'sis_clientes.usuariocreacion',
                    'sis_clientes.usuariomodificacion',
                    'sis_clientes.fechacreacion',
                    'sis_clientes.fechamodificacion',
                    'sis_licencias.modulopractico',
                    'sis_licencias.modulocontrol',
                    'sis_licencias.modulocontable'
                )
                    ->leftJoin('sis_licencias', 'sis_licencias.sis_clientesid', 'sis_clientes.sis_clientesid')
                    ->groupBy('sis_clientes.sis_clientesid')
                    ->get();
                foreach ($servidores as  $servidor) {
                    $url = $servidor->dominio . '/registros/consulta_cliente';
                    $resultado = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                        ->withOptions(["verify" => false])
                        ->post($url, ['sis_distribuidoresid' => '0'])
                        ->json();
                    if (isset($resultado['registro'])) {
                        $web = array_merge($web, $resultado['registro']);
                    }
                }

                $pc = Clientes::select(
                    'sis_clientes.sis_clientesid',
                    'sis_clientes.identificacion',
                    'sis_clientes.nombres',
                    'sis_clientes.telefono1',
                    'sis_clientes.telefono2',
                    'sis_clientes.correos',
                    'sis_licencias.tipo_licencia',
                    DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechainicia) as fechainicia'),
                    DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechacaduca) as fechacaduca'),
                    DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechaactulizaciones) as fechaactulizaciones'),
                    DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechaultimopago) as fechaultimopago'),
                    DB::RAW('DATEDIFF(sis_licencias.fechacaduca,NOW()) as diasvencer'),
                    'sis_licencias.numerocontrato',
                    'sis_licencias.precio',
                    'sis_licencias.periodo',
                    'sis_licencias.producto',
                    'sis_clientes.red_origen',
                    'sis_clientes.sis_distribuidoresid',
                    'sis_clientes.sis_vendedoresid',
                    'sis_clientes.sis_revendedoresid',
                    'sis_clientes.provinciasid',
                    'sis_licencias.empresas',
                    'sis_licencias.usuarios',
                    'sis_licencias.numeroequipos',
                    'sis_licencias.numeromoviles',
                    'sis_clientes.usuariocreacion',
                    'sis_clientes.usuariomodificacion',
                    'sis_clientes.fechacreacion',
                    'sis_clientes.fechamodificacion',
                    'sis_licencias.modulopractico',
                    'sis_licencias.modulocontrol',
                    'sis_licencias.modulocontable'
                )
                    ->join('sis_licencias', 'sis_licencias.sis_clientesid', 'sis_clientes.sis_clientesid')
                    ->get();
            } else {
                $clientes = Clientes::select(
                    'sis_clientes.sis_clientesid',
                    'sis_clientes.identificacion',
                    'sis_clientes.nombres',
                    'sis_clientes.telefono1',
                    'sis_clientes.telefono2',
                    'sis_clientes.correos',
                    'sis_licencias.tipo_licencia',
                    DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechainicia) as fechainicia'),
                    DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechacaduca) as fechacaduca'),
                    DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechaactulizaciones) as fechaactulizaciones'),
                    DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechaultimopago) as fechaultimopago'),
                    DB::RAW('DATEDIFF(sis_licencias.fechacaduca,NOW()) as diasvencer'),
                    'sis_licencias.numerocontrato',
                    'sis_licencias.precio',
                    'sis_licencias.periodo',
                    'sis_licencias.producto',
                    'sis_clientes.red_origen',
                    'sis_clientes.sis_distribuidoresid',
                    'sis_clientes.sis_vendedoresid',
                    'sis_clientes.sis_revendedoresid',
                    'sis_clientes.provinciasid',
                    'sis_licencias.empresas',
                    'sis_licencias.usuarios',
                    'sis_licencias.numeroequipos',
                    'sis_licencias.numeromoviles',
                    'sis_clientes.usuariocreacion',
                    'sis_clientes.usuariomodificacion',
                    'sis_clientes.fechacreacion',
                    'sis_clientes.fechamodificacion',
                    'sis_licencias.modulopractico',
                    'sis_licencias.modulocontrol',
                    'sis_licencias.modulocontable'
                )
                    ->leftJoin('sis_licencias', 'sis_licencias.sis_clientesid', 'sis_clientes.sis_clientesid')
                    ->where('sis_clientes.sis_distribuidoresid', Auth::user()->sis_distribuidoresid)
                    ->groupBy('sis_clientes.sis_clientesid')
                    ->get();

                foreach ($servidores as  $servidor) {
                    $url = $servidor->dominio . '/registros/consulta_cliente';
                    $resultado = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                        ->withOptions(["verify" => false])
                        ->post($url, ['sis_distribuidoresid' => Auth::user()->sis_distribuidoresid])
                        ->json();
                    if (isset($resultado['registro'])) {
                        $web = array_merge($web, $resultado['registro']);
                    }
                }

                $pc = Clientes::select(
                    'sis_clientes.sis_clientesid',
                    'sis_clientes.identificacion',
                    'sis_clientes.nombres',
                    'sis_clientes.telefono1',
                    'sis_clientes.telefono2',
                    'sis_clientes.correos',
                    'sis_licencias.tipo_licencia',
                    DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechainicia) as fechainicia'),
                    DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechacaduca) as fechacaduca'),
                    DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechaactulizaciones) as fechaactulizaciones'),
                    DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechaultimopago) as fechaultimopago'),
                    DB::RAW('DATEDIFF(sis_licencias.fechacaduca,NOW()) as diasvencer'),
                    'sis_licencias.numerocontrato',
                    'sis_licencias.precio',
                    'sis_licencias.periodo',
                    'sis_licencias.producto',
                    'sis_clientes.red_origen',
                    'sis_clientes.sis_distribuidoresid',
                    'sis_clientes.sis_vendedoresid',
                    'sis_clientes.sis_revendedoresid',
                    'sis_clientes.provinciasid',
                    'sis_licencias.empresas',
                    'sis_licencias.usuarios',
                    'sis_licencias.numeroequipos',
                    'sis_licencias.numeromoviles',
                    'sis_clientes.usuariocreacion',
                    'sis_clientes.usuariomodificacion',
                    'sis_clientes.fechacreacion',
                    'sis_clientes.fechamodificacion',
                    'sis_licencias.modulopractico',
                    'sis_licencias.modulocontrol',
                    'sis_licencias.modulocontable'
                )
                    ->join('sis_licencias', 'sis_licencias.sis_clientesid', 'sis_clientes.sis_clientesid')
                    ->where('sis_clientes.sis_distribuidoresid', Auth::user()->sis_distribuidoresid)
                    ->get();
            }

            $diferencia = removeDuplicate($clientes->toArray(), $web, $pc->toArray(), 'sis_clientesid');
            $unir = array_merge($web, $pc->toArray());
            $temp = array_unique(array_column($unir,  'numerocontrato'));
            $unique_arr = array_intersect_key($unir, $temp);

            $final = collect(array_merge($unique_arr, $diferencia));

            //Filtrar por tipo fecha
            if ($tipo != null) {
                switch ($tipo) {
                    case '1':
                        $tipo_fecha = "fechainicia";
                        break;
                    case '2':
                        $tipo_fecha = "fechacaduca";
                        break;
                    case '3':
                        $tipo_fecha = "fechaactulizaciones";
                        break;
                }


                if ($fecha) {
                    $desde =  strtotime(explode(" / ", $fecha)[0]);
                    $hasta =  strtotime(explode(" / ", $fecha)[1]);
                    $final = $final->whereBetween($tipo_fecha, [$desde, $hasta]);
                }
            }

            if ($distribuidor != null) {
                $final = $final->where('sis_distribuidoresid', $distribuidor);
            }

            if ($vendedor != null) {
                $final = $final->where('sis_vendedoresid', $vendedor);
            }

            if ($origen != null) {
                $final = $final->where('red_origen', $origen);
            }

            if ($provinciasid != null) {
                $final = $final->where('provinciasid', $provinciasid);
            }

            if ($tipolicencia != 1) {
                switch ($tipolicencia) {
                        //web
                    case '2':
                        $final = $final->where('tipo_licencia', 1);
                        if ($producto != null) $final = $final->where('producto', $producto);
                        if ($periodo != null) $final = $final->where('periodo', $periodo);
                        break;
                        //pc
                    case '3':
                        $final = $final->where('tipo_licencia', 2);
                        if ($producto != null) {
                            switch ($producto) {
                                case '1':
                                    $final = $final->where('modulopractico', 1);
                                    break;
                                case '2':
                                    $final = $final->where('modulocontrol', 1);
                                    break;
                                case '3':
                                    $final = $final->where('modulocontable', 1);
                                    break;
                            }
                        }
                        break;
                }
            }

            return DataTables::of($final)
                ->editColumn('identificacion', function ($cliente) {
                    if (Auth::user()->tipo != 2 || (Auth::user()->tipo == 2 && (Auth::user()->sis_distribuidoresid == $cliente['sis_distribuidoresid']))) {
                        return '<a class="text-primary" href="' . route('clientes.editar', $cliente['sis_clientesid']) . '">' . $cliente['identificacion'] . ' </a>';
                    } else {
                        return $cliente['identificacion'];
                    }
                })
                ->editColumn('action', function ($cliente) {
                    if (Auth::user()->tipo != 2 || (Auth::user()->tipo == 2 && (Auth::user()->sis_distribuidoresid == $cliente['sis_distribuidoresid']))) {
                        return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('clientes.editar', $cliente['sis_clientesid']) . '" title="Editar"> <i class="la la-edit"></i> </a>';
                    }
                })
                ->editColumn('sis_distribuidoresid', function ($cliente) use ($distribuidores) {
                    $posicion = array_search($cliente['sis_distribuidoresid'], array_column($distribuidores, 'sis_distribuidoresid'));
                    return $distribuidores[$posicion]['razonsocial'];
                })
                ->editColumn('sis_vendedoresid', function ($cliente) use ($vendedores) {
                    $posicion = array_search($cliente['sis_vendedoresid'], array_column($vendedores, 'sis_revendedoresid'));
                    return $vendedores[$posicion]['razonsocial'];
                })
                ->editColumn('sis_revendedoresid', function ($cliente) use ($vendedores) {
                    $posicion = array_search($cliente['sis_revendedoresid'], array_column($vendedores, 'sis_revendedoresid'));
                    return $vendedores[$posicion]['razonsocial'];
                })
                ->editColumn('fechainicia', function ($cliente) {
                    return $cliente['fechainicia'] == null ? date('d-m-Y', strtotime(now()))  : date('d-m-Y', $cliente['fechainicia']);
                })
                ->editColumn('fechacaduca', function ($cliente) {
                    return $cliente['fechacaduca'] == null ? date('d-m-Y', strtotime(now()))  : date('d-m-Y', $cliente['fechacaduca']);
                })
                ->editColumn('fechaultimopago', function ($cliente) {
                    return $cliente['fechaultimopago'] == null ? date('d-m-Y', strtotime(now()))  : date('d-m-Y', $cliente['fechaultimopago']);
                })
                ->editColumn('fechaactulizaciones', function ($cliente) {
                    return $cliente['fechaactulizaciones'] == null ? date('d-m-Y', strtotime(now()))  : date('d-m-Y', $cliente['fechaactulizaciones']);
                })
                ->editColumn('tipo_licencia', function ($cliente) {
                    $licencia = "";
                    if ($cliente['tipo_licencia'] == 1) {
                        $licencia = "Web";
                    } elseif ($cliente['tipo_licencia'] == 2) {
                        $licencia = "PC";
                    }
                    return $licencia;
                })
                ->editColumn('telefono2', function ($cliente) {
                    $telefono = "";
                    if (Auth::user()->tipo != 2 || (Auth::user()->tipo == 2 && (Auth::user()->sis_distribuidoresid == $cliente['sis_distribuidoresid']))) {
                        $telefono = $cliente['telefono2'];
                    } else {
                        $telefono = "";
                    }
                    return $telefono;
                })
                ->editColumn('producto', function ($cliente) {
                    $producto = "";
                    if ($cliente['tipo_licencia'] == 1) {
                        switch ($cliente['producto']) {
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
                                $producto = "Perseo Lite";
                                break;
                            case '7':
                                $producto = "Total";
                                break;
                            case '8':
                                $producto = "Soy Contador Servicios";
                                break;
                        }
                    } else {
                        if ($cliente['modulopractico'] == 1) $producto = "Práctico";
                        if ($cliente['modulocontrol'] == 1) $producto = "Control";
                        if ($cliente['modulocontable'] == 1) $producto = "Contable";
                    }
                    return $producto;
                })
                ->editColumn('red_origen', function ($cliente) {
                    $origen = "";
                    switch ($cliente['red_origen']) {
                        case '1':
                            $producto = "PERSEO";
                            break;
                        case '2':
                            $producto = "CONTAFACIL";
                            break;
                        case '3':
                            $producto = "UIO-01";
                            break;
                        case '4':
                            $producto = "GYE-01";
                            break;
                        case '5':
                            $producto = "GYE-02";
                            break;
                        case '6':
                            $producto = "CUE-01";
                            break;
                        case '7':
                            $producto = "STO-01";
                            break;
                        case '8':
                            $producto = "UIO-02";
                            break;
                        case '9':
                            $producto = "GYE-03";
                            break;
                        case '10':
                            $producto = "CNV-01";
                            break;
                        case '11':
                            $producto = "MATRIZ";
                            break;
                    }
                    return $producto;
                })
                ->editColumn('provinciasid', function ($cliente) {
                    $provincia = "";
                    switch ($cliente['provinciasid']) {
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
                })
                ->editColumn('periodo', function ($cliente) {
                    $periodo = "";
                    if ($cliente['periodo'] == 1) {
                        $periodo = "Mensual";
                    } elseif ($cliente['periodo'] == 2) {
                        $periodo = "Anual";
                    }
                    return $periodo;
                })
                ->rawColumns(['action', 'identificacion'])
                ->make(true);
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

        return view('admin.clientes.crear', compact('cliente', 'distribuidores'));
    }

    public function guardar(Request $request)
    {
        //Validaciones
        $request->validate(
            [
                'identificacion' => ['required', 'unique:sis_clientes'],
                'nombres' => 'required',
                'direccion' => 'required',
                'correos' => ['required', 'email', new ValidarCorreo],
                'provinciasid' => 'required',
                'telefono1' => ['required', 'min:7|max:10'],
                'telefono2' => ['required', 'size:10', new ValidarCelular],
                'sis_distribuidoresid' => 'required',
                'sis_vendedoresid' => 'required',
                'sis_revendedoresid' => 'required',
                'red_origen' => 'required',
            ],
            [
                'identificacion.required' => 'Ingrese su cédula o RUC ',
                'identificacion.unique' => 'Su cédula o RUC ya se encuentra registrado',
                'nombres.required' => 'Ingrese los Nombres',
                'direccion.required' => 'Ingrese una Dirección',
                'correos.required' => 'Ingrese un Correo',
                'correos.email' => 'Ingrese un Correo válido',
                'provinciasid.required' => 'Seleccione una Provincia',
                'telefono1.required' => 'Ingrese un Número Convencional',
                'telefono1.min' => 'Mínimo 7 dígitos',
                'telefono1.max' => 'Máximo 10 dígitos',
                'telefono2.required' => 'Ingrese un Número Celular',
                'telefono2.size' => 'Ingrese 10 dígitos',
                'sis_distribuidoresid.required' => 'Seleccione un Distribuidor',
                'sis_vendedoresid.required' => 'Seleccione un Vendedor',
                'sis_revendedoresid.required' => 'Seleccione un Revendedor',
                'red_origen.required' => 'Seleccione un Origen',
            ],
        );

        //Asignacion masiva para los campos asignados en guarded o fillable en el modelo
        $request['fechacreacion'] = now();
        $request['usuariocreacion'] = Auth::user()->nombres;
        $request['validado'] = 1;


        DB::beginTransaction();
        try {
            $servidores = Servidores::where('estado', 1)->get();

            $cliente =   Clientes::create($request->all());

            $log = new Log();
            $log->usuario = Auth::user()->nombres;
            $log->pantalla = "Clientes";
            $log->tipooperacion = "Crear";
            $log->fecha = now();
            $log->detalle = $cliente;
            $log->save();
            $request['sis_clientesid'] = $cliente->sis_clientesid;

            foreach ($servidores as $servidor) {
                $url = $servidor->dominio . '/registros/crear_clientes';
                $crearCliente = Http::withHeaders(['Content-Type' => 'application/json; ', 'verify' => false])
                    ->withOptions(["verify" => false])
                    ->post($url, $request->all())
                    ->json();
                if (!isset($crearCliente['sis_clientes'])) {
                    DB::rollBack();
                    flash('Ocurrió un error vuelva a intentarlo')->warning();
                    return back();
                }
            }
            flash('Guardado Correctamente')->success();
            DB::commit();
            return redirect()->route('clientes.editar', $cliente->sis_clientesid);
        } catch (\Exception $e) {
            DB::rollBack();
            flash('Ocurrió un error vuelva a intentarlo')->warning();
            return back();
        }
    }

    public function editar(Request $request, clientes $cliente)
    {
        $distribuidores = Distribuidores::all();
        return view('admin.clientes.editar', compact('cliente', 'distribuidores'));
    }

    public function actualizar(Request $request, Clientes $cliente)
    {
        //Validaciones
        $request->validate(
            [
                'identificacion' => ['required', 'unique:sis_clientes,identificacion,' . $cliente->sis_clientesid . ',sis_clientesid'],
                'nombres' => 'required',
                'direccion' => 'required',
                'correos' => ['required', 'email', new ValidarCorreo],
                'provinciasid' => 'required',
                'telefono1' => ['required', 'min:7|max:10'],
                'telefono2' => ['required', 'size:10', new ValidarCelular],
                'sis_distribuidoresid' => 'required',
                'sis_vendedoresid' => 'required',
                'sis_revendedoresid' => 'required',
                'red_origen' => 'required',
            ],
            [
                'identificacion.required' => 'Ingrese su cédula o RUC ',
                'identificacion.unique' => 'Su cédula o RUC ya se encuentra registrado',
                'nombres.required' => 'Ingrese los Nombres',
                'direccion.required' => 'Ingrese una Dirección',
                'correos.required' => 'Ingrese un Correo',
                'correos.email' => 'Ingrese un Correo válido',
                'provinciasid.required' => 'Seleccione una Provincia',
                'telefono1.required' => 'Ingrese un Número Convencional',
                'telefono1.min' => 'Mínimo 7 dígitos',
                'telefono1.max' => 'Máximo 10 dígitos',
                'telefono2.required' => 'Ingrese un Número Celular',
                'telefono2.size' => 'Ingrese 10 dígitos',
                'sis_distribuidoresid.required' => 'Seleccione un Distribuidor',
                'sis_vendedoresid.required' => 'Seleccione un Vendedor',
                'sis_revendedoresid.required' => 'Seleccione un Revendedor',
                'red_origen.required' => 'Seleccione un Origen',
            ],
        );


        DB::beginTransaction();
        try {
            $servidores = Servidores::where('estado', 1)->get();

            $request['fechamodificacion'] =  now();
            $request['usuariomodificacion'] = Auth::user()->nombres;
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
