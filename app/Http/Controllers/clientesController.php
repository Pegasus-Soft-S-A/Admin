<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Distribuidores;
use App\Models\Log;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables as DataTables;

class clientesController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $data = Clientes::select('sis_clientesid', 'identificacion', 'nombres', 'telefono2');

            return DataTables::of($data)
                // ->addIndexColumn()
                ->editColumn('identificacion', function ($cliente) {
                    return '<a class="text-primary" href="' . route('clientes.editar', $cliente->sis_clientesid) . '">' . $cliente->identificacion . ' </a>';
                })
                ->editColumn('action', function ($cliente) {
                    return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('clientes.editar', $cliente->sis_clientesid) . '" title="Editar"> <i class="la la-edit"></i> </a>' .
                        '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-2 confirm-delete" href="javascript:void(0)" data-href="' . route('clientes.eliminar', $cliente->sis_clientesid) . '" title="Eliminar"> <i class="la la-trash"></i> </a>';
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

    public function editar(clientes $cliente)
    {
        $distribuidores = Distribuidores::all();
        return view('admin.clientes.editar', compact('cliente', 'distribuidores'));
    }
}
