<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Distribuidores;
use App\Models\Licencias;
use App\Models\Log;
use App\Models\Revendedores;
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
        if ($request->ajax()) {

            $tipo = $request->tipofecha;
            $tipolicencia = $request->tipolicencia;
            $fecha = $request->fecha;
            $distribuidor = $request->distribuidor;
            $vendedor = $request->vendedor;
            $origen = $request->origen;
            $producto = $request->producto;
            $periodo = $request->periodo;

            if (Auth::user()->tipo == 1) {
                $clientes = Clientes::select('sis_clientes.sis_clientesid', 'sis_clientes.tipoidentificacion', 'sis_clientes.identificacion', 'sis_clientes.nombres', 'sis_clientes.telefono1', 'sis_clientes.telefono2', 'sis_clientes.correos', 'sis_clientes.sis_distribuidoresid', 'sis_clientes.sis_vendedoresid', 'sis_clientes.sis_revendedoresid', 'sis_clientes.red_origen', 'sis_clientes.usuariocreacion', 'sis_clientes.usuariomodificacion', 'sis_clientes.fechacreacion', 'sis_clientes.fechamodificacion', 'sis_licencias.sis_licenciasid', 'sis_licencias.usuarios', 'sis_licencias.empresas', DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechainicia) as fechainicia'), DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechacaduca) as fechacaduca'), DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechaactulizaciones) as fechaactulizaciones'), 'sis_licencias.tipo_licencia', 'sis_licencias.producto', 'sis_licencias.numeromoviles', 'sis_licencias.numerocontrato',  'sis_licencias.modulopractico', 'sis_licencias.modulocontable', 'sis_licencias.modulocontrol')
                    ->leftJoin('sis_licencias', 'sis_licencias.sis_clientesid', 'sis_clientes.sis_clientesid')
                    ->groupBy('sis_clientes.sis_clientesid')
                    ->get();

                $url = 'http://localhost:8026/registros/consulta_cliente';
                $web1 = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                    ->withOptions(["verify" => false])
                    ->post($url, ['sis_distribuidoresid' => '0'])
                    ->json();

                $pc = Clientes::select('sis_clientes.sis_clientesid', 'sis_clientes.tipoidentificacion', 'sis_clientes.identificacion', 'sis_clientes.nombres', 'sis_clientes.telefono1', 'sis_clientes.telefono2', 'sis_clientes.correos', 'sis_clientes.sis_distribuidoresid', 'sis_clientes.sis_vendedoresid', 'sis_clientes.sis_revendedoresid', 'sis_clientes.red_origen', 'sis_clientes.usuariocreacion', 'sis_clientes.usuariomodificacion', 'sis_clientes.fechacreacion', 'sis_clientes.fechamodificacion', 'sis_licencias.sis_licenciasid', 'sis_licencias.usuarios', 'sis_licencias.empresas', DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechainicia) as fechainicia'), DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechacaduca) as fechacaduca'), DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechaactulizaciones) as fechaactulizaciones'), 'sis_licencias.tipo_licencia', 'sis_licencias.producto', 'sis_licencias.numeromoviles', 'sis_licencias.numerocontrato',  'sis_licencias.modulopractico', 'sis_licencias.modulocontable', 'sis_licencias.modulocontrol')
                    ->join('sis_licencias', 'sis_licencias.sis_clientesid', 'sis_clientes.sis_clientesid')
                    ->get();
            } else {
                $clientes = Clientes::select('sis_clientes.sis_clientesid', 'sis_clientes.tipoidentificacion', 'sis_clientes.identificacion', 'sis_clientes.nombres', 'sis_clientes.telefono1', 'sis_clientes.telefono2', 'sis_clientes.correos', 'sis_clientes.sis_distribuidoresid', 'sis_clientes.sis_vendedoresid', 'sis_clientes.sis_revendedoresid', 'sis_clientes.red_origen', 'sis_clientes.usuariocreacion', 'sis_clientes.usuariomodificacion', 'sis_clientes.fechacreacion', 'sis_clientes.fechamodificacion', 'sis_licencias.sis_licenciasid', 'sis_licencias.usuarios', 'sis_licencias.empresas', DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechainicia) as fechainicia'), DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechacaduca) as fechacaduca'), DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechaactulizaciones) as fechaactulizaciones'), 'sis_licencias.tipo_licencia', 'sis_licencias.producto', 'sis_licencias.numeromoviles', 'sis_licencias.numerocontrato',  'sis_licencias.modulopractico', 'sis_licencias.modulocontable', 'sis_licencias.modulocontrol')
                    ->leftJoin('sis_licencias', 'sis_licencias.sis_clientesid', 'sis_clientes.sis_clientesid')
                    ->where('sis_clientes.sis_distribuidoresid', Auth::user()->sis_distribuidoresid)
                    ->groupBy('sis_clientes.sis_clientesid')
                    ->get();

                $url = 'http://localhost:8026/registros/consulta_cliente';
                $web1 = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                    ->withOptions(["verify" => false])
                    ->post($url, ['sis_distribuidoresid' => Auth::user()->sis_distribuidoresid])
                    ->json();

                $pc = Clientes::select('sis_clientes.sis_clientesid', 'sis_clientes.tipoidentificacion', 'sis_clientes.identificacion', 'sis_clientes.nombres', 'sis_clientes.telefono1', 'sis_clientes.telefono2', 'sis_clientes.correos', 'sis_clientes.sis_distribuidoresid', 'sis_clientes.sis_vendedoresid', 'sis_clientes.sis_revendedoresid', 'sis_clientes.red_origen', 'sis_clientes.usuariocreacion', 'sis_clientes.usuariomodificacion', 'sis_clientes.fechacreacion', 'sis_clientes.fechamodificacion', 'sis_licencias.sis_licenciasid', 'sis_licencias.usuarios', 'sis_licencias.empresas', DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechainicia) as fechainicia'), DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechacaduca) as fechacaduca'), DB::RAW('UNIX_TIMESTAMP(sis_licencias.fechaactulizaciones) as fechaactulizaciones'), 'sis_licencias.tipo_licencia', 'sis_licencias.producto', 'sis_licencias.numeromoviles', 'sis_licencias.numerocontrato',  'sis_licencias.modulopractico', 'sis_licencias.modulocontable', 'sis_licencias.modulocontrol')
                    ->join('sis_licencias', 'sis_licencias.sis_clientesid', 'sis_clientes.sis_clientesid')
                    ->where('sis_clientes.sis_distribuidoresid', Auth::user()->sis_distribuidoresid)
                    ->get();
            }

            $diferencia = removeDuplicate($clientes->toArray(), $web1['registro'], $pc->toArray(), 'sis_clientesid');

            $unir = array_merge($web1['registro'], $pc->toArray());
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

                //Si existe fecha en el filtro agrega condicion
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
                    return '<a class="text-primary" href="' . route('clientes.editar', $cliente['sis_clientesid']) . '">' . $cliente['identificacion'] . ' </a>';
                })
                ->editColumn('action', function ($cliente) {
                    if (Auth::user()->tipo == 1) {
                        return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('clientes.editar', $cliente['sis_clientesid']) . '" title="Editar"> <i class="la la-edit"></i> </a>' .
                            '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-2 confirm-delete" href="javascript:void(0)" data-href="' . route('clientes.eliminar', $cliente['sis_clientesid']) . '" title="Eliminar"> <i class="la la-trash"></i> </a>';
                    } else {
                        return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('clientes.editar', $cliente['sis_clientesid']) . '" title="Editar"> <i class="la la-edit"></i> </a>';
                    }
                })
                ->editColumn('fechainicia', function ($cliente) {
                    return $cliente['fechainicia'] == null ? date('d-m-Y', strtotime(now()))  : date('d-m-Y', $cliente['fechainicia']);
                })
                ->editColumn('fechacaduca', function ($cliente) {
                    return $cliente['fechacaduca'] == null ? date('d-m-Y', strtotime(now()))  : date('d-m-Y', $cliente['fechacaduca']);
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
                                $producto = "Demo";
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
                ->rawColumns(['action', 'identificacion'])
                ->make(true);
        }
    }

    public function crear()
    {
        $cliente = new Clientes();
        $distribuidores = Distribuidores::all();

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
                'correos' => ['required', 'email'],
                'provinciasid' => 'required',
                'telefono1' => ['required', 'min:7|max:10'],
                'telefono2' => ['required', 'size:10'],
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

        $url = 'http://localhost:8026/registros/crear_clientes';

        DB::beginTransaction();
        try {
            $cliente =   Clientes::create($request->all());
            $log = new Log();
            $log->usuario = Auth::user()->nombres;
            $log->pantalla = "Clientes";
            $log->tipooperacion = "Crear";
            $log->fecha = now();
            $log->detalle = $cliente;
            $log->save();
            $request['sis_clientesid'] = $cliente->sis_clientesid;

            $crearCliente = Http::withHeaders(['Content-Type' => 'application/json; ', 'verify' => false])
                ->withOptions(["verify" => false])
                ->post($url, $request->all())
                ->json();
            if (isset($crearCliente['sis_clientes'])) {
                flash('Guardado Correctamente')->success();
            } else {
                DB::rollBack();

                flash('Ocurrió un error vuelva a intentarlo')->warning();
                return back();
            }
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
                'correos' => ['required', 'email'],
                'provinciasid' => 'required',
                'telefono1' => ['required', 'min:7|max:10'],
                'telefono2' => ['required', 'size:10'],
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

        $urlEditar = 'http://localhost:8026/registros/editar_clientes';

        DB::beginTransaction();
        try {
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
            $clienteEditar = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                ->withOptions(["verify" => false])
                ->post($urlEditar, $request->all())
                ->json();

            if (isset($clienteEditar['sis_clientes'])) {
                flash('Guardado Correctamente')->success();
            } else {
                DB::rollBack();
                flash('Ocurrió un error vuelva a intentarlo')->warning();
                return back();
            }

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
            $buscarLicencias = Licencias::where('sis_clientesid', $cliente->sis_clientesid)->get();
            $url = 'http://localhost:8026/registros/eliminar_cliente';
            $eliminarCliente = Http::withHeaders(['Content-Type' => 'application/json; ', 'verify' => false])
                ->withOptions(["verify" => false])
                ->post($url, ["sis_clientesid" => $cliente->sis_clientesid])
                ->json();

            if (count($buscarLicencias) > 0) {
                for ($i = 0; $i < count($buscarLicencias); $i++) {
                    $buscarLicencias[$i]->delete();
                }
            }

            if (isset($eliminarCliente['respuesta'])) {
                $cliente->delete();
                $log = new Log();
                $log->usuario = Auth::user()->nombres;
                $log->pantalla = "Cliente";
                $log->tipooperacion = "Eliminar";
                $log->fecha = now();
                $log->detalle = $cliente;
                $log->save();
                flash('Eliminado Correctamente')->success();
            } else {
                DB::rollBack();
                flash('Ocurrió un error vuelva a intentarlo')->warning();
                return back();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            flash('Ocurrió un error vuelva a intentarlo')->warning();
        };
        return back();
    }
}
