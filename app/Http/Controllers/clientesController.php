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

            $tipo = $request->tipofecha;
            $tipolicencia = $request->tipolicencia;
            $fecha = $request->fecha;
            $distribuidor = $request->distribuidor;
            $vendedor = $request->vendedor;
            $revendedor = $request->revendedor;
            $origen = $request->origen;
            $validado = $request->validado;
            $producto = $request->producto;
            $periodo = $request->periodo;
            $provinciasid = $request->provinciasid;
            $distribuidores = Distribuidores::pluck('sis_distribuidoresid', 'razonsocial')->toArray();
            $links = Links::all()->toArray();
            $grupos = Grupos::all()->toArray();
            $vendedores = Revendedores::all()->toArray();

            //Busqueda
            $search = $request->search['value'];
            if ($search <> null) {
                //Variable con los datos de la tabla
                $final = Clientes::Clientes(0, $search);
                //Total de registros
                $records = count(Session::get('data'));
            } else {
                //Variable con los datos de la tabla
                $merged = Session::get('data');
                //Total de registros
                $records = $merged->count();
                //Posicion inicial
                $start = $request->start;
                //Cantidad de registros a mostrar
                $limit = $request->length;
                //Resultado final del offset
                $final = $merged->slice($start, $limit);
                //Total de paginas
                $totalPages = ceil($records / $limit);
            }

            //Filtros
            if ($request->buscar_filtro == 1) {
                //Buscar en todos los campos
                $final = $merged;

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
                        case '4':
                            $tipo_fecha = "fechamodificacion";
                            break;
                    }

                    if ($fecha) {

                        $desde =  explode(" / ", $fecha)[0];
                        $hasta =  explode(" / ", $fecha)[1];

                        if ($tipo_fecha == "fechamodificacion") {
                            $desde = date('Y-m-d H:i:s', strtotime($desde));
                            $hasta = date('Y-m-d H:i:s', strtotime($hasta . ' +1 day -1 second'));
                        } else {
                            $desde = strtotime(date('Y-m-d', strtotime($desde)));
                            $hasta = strtotime(date('Y-m-d', strtotime($hasta)));
                        }

                        $final = $final->whereBetween($tipo_fecha, [$desde, $hasta]);
                    }
                }

                if ($distribuidor != null) {
                    $final = $final->where('sis_distribuidoresid', $distribuidor);
                }

                if ($vendedor != null) {
                    $final = $final->where('sis_vendedoresid', $vendedor);
                }

                if ($revendedor != null) {
                    $final = $final->where('sis_revendedoresid', $revendedor);
                }

                if ($origen != null) {
                    $final = $final->where('red_origen', $origen);
                }

                if ($validado != null) {
                    if ($validado == 1) {
                        $final = $final->where('validado', 1);
                    } else {
                        $final = $final->whereIn('validado', [0, null]);
                    }
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
                            //vps
                        case '4':
                            $final = $final->where('tipo_licencia', 3);
                            break;
                    }
                }
            }

            $datatable = DataTables::of($final)
                ->editColumn('validado', function ($cliente) {
                    $checked = $cliente->validado == 1 ? 'checked' : '';
                    return '<label class="checkbox checkbox-single checkbox-primary mb-0"><input type="checkbox" class="checkable" ' . $checked . ' disabled><span></span></label>';
                })

                ->editColumn('identificacion', function ($cliente) {
                    if (Auth::user()->tipo == 6 || (Auth::user()->tipo != 1 && Auth::user()->sis_distribuidoresid != $cliente->sis_distribuidoresid)) {
                        return $cliente->identificacion;
                    }
                    return '<a class="text-primary" href="' . route('clientes.editar', $cliente->sis_clientesid) . '">' . $cliente->identificacion . ' </a>';
                })
                ->editColumn('action', function ($cliente) {
                    if (Auth::user()->tipo != 6 && (Auth::user()->tipo == 1 || Auth::user()->sis_distribuidoresid == $cliente->sis_distribuidoresid)) {
                        return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('clientes.editar', $cliente->sis_clientesid) . '" title="Editar"> <i class="la la-edit"></i> </a>';
                    }
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
                ->editColumn('tipo_licencia', function ($cliente) {
                    $licencia = "";
                    switch ($cliente->tipo_licencia) {
                        case '1':
                            $licencia = "Web";
                            break;
                        case '2':
                            $licencia = "PC";
                            break;
                        case '3':
                            $licencia = "VPS";
                            break;
                    }

                    return $licencia;
                })
                ->editColumn('telefono2', function ($cliente) {
                    $telefono = "";
                    if (Auth::user()->tipo == 6 || Auth::user()->tipo == 1 || Auth::user()->sis_distribuidoresid == $cliente->sis_distribuidoresid) {
                        $telefono = $cliente->telefono2;
                    } else {
                        $telefono = "";
                    }
                    return $telefono;
                })
                ->editColumn('correos', function ($cliente) {
                    $correos = "";
                    if (Auth::user()->tipo == 6 || Auth::user()->tipo == 1 || Auth::user()->sis_distribuidoresid == $cliente->sis_distribuidoresid) {
                        $correos = $cliente->correos;
                    } else {
                        $correos = "";
                    }
                    return $correos;
                })
                ->editColumn('producto', function ($cliente) {
                    $producto = "";
                    if ($cliente->tipo_licencia == 1) {
                        switch ($cliente->producto) {
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
                        }
                    } else {
                        if ($cliente->modulopractico == 1) $producto = "Práctico";
                        if ($cliente->modulocontrol == 1) $producto = "Control";
                        if ($cliente->modulocontable == 1) $producto = "Contable";
                    }
                    return $producto;
                })
                ->editColumn('red_origen', function ($cliente) use ($links) {
                    $posicion = array_search($cliente->red_origen, array_column($links, 'sis_linksid'));
                    return $links[$posicion]['codigo'];
                })
                ->editColumn('provinciasid', function ($cliente) {

                    $provincia = "";
                    switch ($cliente->provinciasid) {
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
                    if ($cliente->periodo == 1) {
                        $periodo = "Mensual";
                    } elseif ($cliente->periodo == 2) {
                        $periodo = "Anual";
                    }
                    return $periodo;
                })
                ->rawColumns(['action', 'identificacion', 'validado']);

            if ($request->buscar_filtro == 1 || $search <> null) {
                $datatable = $datatable
                    ->with('recordsTotal', $records)
                    ->make(true);
            } else {
                $datatable = $datatable
                    ->setOffset($start)
                    ->with('recordsTotal', $records)
                    ->with('recordsFiltered', $records)
                    ->with('totalPages', $totalPages)
                    ->make(true);
            }

            return  $datatable;
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
