<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Distribuidores;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\DataTables as DataTables;

class clientesController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $url = 'http://localhost:8026/registros/consulta_cliente';
            $resultado = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                ->withOptions(["verify" => false])
                ->post($url, ['sis_distribuidoresid' => '0'])
                ->json();

            $data = Clientes::select('sis_clientes.sis_clientesid', 'sis_clientes.tipoidentificacion', 'sis_clientes.identificacion', 'sis_clientes.nombres', 'sis_clientes.telefono1', 'sis_clientes.telefono2', 'sis_clientes.correos', 'sis_clientes.sis_distribuidoresid', 'sis_clientes.sis_vendedoresid', 'sis_clientes.sis_revendedoresid', 'sis_clientes.red_origen', 'sis_clientes.usuariocreacion', 'sis_clientes.usuariomodificacion', 'sis_clientes.fechacreacion', 'sis_clientes.fechamodificacion', 'sis_licencias.sis_licenciasid', 'sis_licencias.usuarios', 'sis_licencias.empresas', 'sis_licencias.fechainicia', 'sis_licencias.fechacaduca', 'sis_licencias.tipo_licencia', 'sis_licencias.producto', 'sis_licencias.numeromoviles', 'sis_licencias.numerocontrato',  'sis_licencias.modulopractico', 'sis_licencias.modulocontable', 'sis_licencias.modulocontrol')
                ->join('sis_licencias', 'sis_licencias.sis_clientesid', 'sis_clientes.sis_clientesid')
                ->get();

            $data2 = Clientes::select('sis_clientes.sis_clientesid', 'sis_clientes.tipoidentificacion', 'sis_clientes.identificacion', 'sis_clientes.nombres', 'sis_clientes.telefono1', 'sis_clientes.telefono2', 'sis_clientes.correos', 'sis_clientes.sis_distribuidoresid', 'sis_clientes.sis_vendedoresid', 'sis_clientes.sis_revendedoresid', 'sis_clientes.red_origen', 'sis_clientes.usuariocreacion', 'sis_clientes.usuariomodificacion', 'sis_clientes.fechacreacion', 'sis_clientes.fechamodificacion', 'sis_licencias.sis_licenciasid', 'sis_licencias.usuarios', 'sis_licencias.empresas', 'sis_licencias.fechainicia', 'sis_licencias.fechacaduca', 'sis_licencias.tipo_licencia', 'sis_licencias.producto', 'sis_licencias.numeromoviles', DB::raw('ifnull (sis_licencias.numerocontrato,0) as numerocontrato'),  'sis_licencias.modulopractico', 'sis_licencias.modulocontable', 'sis_licencias.modulocontrol')
                ->leftJoin('sis_licencias', 'sis_licencias.sis_clientesid', 'sis_clientes.sis_clientesid')
                ->whereNull('sis_licencias.sis_clientesid')
                ->get();

            //dd($data2[0]);
            $unir = array_merge($resultado['registro'], $data->toArray(), $data2->toArray());

            $temp = array_unique(array_column($unir,  'numerocontrato'));

            $unique_arr = array_intersect_key($unir, $temp);

            //dd($unique_arr);
            return DataTables::of($unique_arr)
                ->editColumn('identificacion', function ($cliente) {
                    return '<a class="text-primary" href="' . route('clientes.editar', $cliente['sis_clientesid']) . '">' . $cliente['identificacion'] . ' </a>';
                })
                ->editColumn('action', function ($cliente) {
                    return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('clientes.editar', $cliente['sis_clientesid']) . '" title="Editar"> <i class="la la-edit"></i> </a>' .
                        '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-2 confirm-delete" href="javascript:void(0)" data-href="' . route('clientes.eliminar', $cliente['sis_clientesid']) . '" title="Eliminar"> <i class="la la-trash"></i> </a>';
                })
                ->editColumn('fechainicia', function ($cliente) {
                    return $cliente['fechainicia'] == null ? date('d-m-Y', strtotime(now()))  : date('d-m-Y', strtotime($cliente['fechainicia']));
                })
                ->editColumn('fechacaduca', function ($cliente) {
                    return $cliente['fechacaduca'] == null ? date('d-m-Y', strtotime(now()))  : date('d-m-Y', strtotime($cliente['fechacaduca']));
                })
                ->editColumn('tipo_licencia', function ($cliente) {
                    return $cliente['tipo_licencia'] == 1 ? 'Web' : 'PC';
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

        return view('admin.clientes.index');
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
        $cliente =   Clientes::create($request->all());

        $log = new Log();
        $log->usuario = Auth::user()->nombres;
        $log->pantalla = "Clientes";
        $log->tipooperacion = "Crear";
        $log->fecha = now();
        $log->detalle = $cliente;
        $log->save();

        flash('Guardado Correctamente')->success();
        return redirect()->route('clientes.editar', $cliente->sis_clientesid);
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

        //Asignacion masiva para los campos asignados en guarded o fillable en el modelo
        $request['fechamodificacion'] = now();
        $request['usuariomodificacion'] = Auth::user()->nombres;
        $cliente->update($request->all());

        $log = new Log();
        $log->usuario = Auth::user()->nombres;
        $log->pantalla = "Clientes";
        $log->tipooperacion = "Modificar";
        $log->fecha = now();
        $log->detalle = $cliente;
        $log->save();

        flash('Actualizado Correctamente')->success();
        return back();
    }
}
