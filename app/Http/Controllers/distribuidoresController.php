<?php

namespace App\Http\Controllers;

use App\Models\Distribuidores;
use App\Models\Log;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables as DataTables;

class distribuidoresController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $data = Distribuidores::select('sis_distribuidoresid', 'identificacion', 'razonsocial', 'nombrecomercial', 'correos');

            return DataTables::of($data)
                // ->addIndexColumn()
                ->editColumn('identificacion', function ($distribuidor) {
                    return '<a class="text-primary" href="' . route('distribuidores.editar', $distribuidor->sis_distribuidoresid) . '">' . $distribuidor->identificacion . ' </a>';
                })
                ->editColumn('action', function ($distribuidor) {
                    return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('distribuidores.editar', $distribuidor->sis_distribuidoresid) . '" title="Editar"> <i class="la la-edit"></i> </a>' .
                        '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-2 confirm-delete" href="javascript:void(0)" data-href="' . route('distribuidores.eliminar', $distribuidor->sis_distribuidoresid) . '" title="Eliminar"> <i class="la la-trash"></i> </a>';
                })
                ->rawColumns(['action', 'identificacion'])
                ->make(true);
        }

        return view('admin.distribuidores.index');
    }

    public function crear()
    {
        $distribuidor = new Distribuidores();
        return view('admin.distribuidores.crear', compact('distribuidor'));
    }

    public function guardar(Request $request)
    {
        //Validaciones
        $request->validate(
            [
                'identificacion' => ['required', 'unique:sis_distribuidores'],
                'razonsocial' => 'required',
                'nombrecomercial' => 'required',
                'correos' => ['required'],
            ],
            [
                'identificacion.required' => 'Ingrese su cédula o RUC ',
                'identificacion.unique' => 'Su cédula o RUC ya se encuentra registrado',
                'razonsocial.required' => 'Ingrese una Razón Social',
                'nombrecomercial.required' => 'Ingrese un Nombre Comercial',
                'correos.required' => 'Ingrese un Correo',
            ],
        );

        //Asignacion masiva para los campos asignados en guarded o fillable en el modelo
        $request['fechacreacion'] = now();
        $request['usuariocreacion'] = Auth::user()->nombres;
        $distribuidor =   Distribuidores::create($request->all());

        // $log = new Log();
        // $log->usuario = Auth::user()->nombres;
        // $log->pantalla = "Distribuidores";
        // $log->tipooperacion = "Crear";
        // $log->fecha = now();
        // $log->detalle = $distribuidor;
        // $log->save();

        flash('Guardado Correctamente')->success();
        return redirect()->route('distribuidores.editar', $distribuidor->sis_distribuidoresid);
    }

    public function editar(Distribuidores $distribuidor)
    {
        return view('admin.distribuidores.editar', compact('distribuidor'));
    }

    public function actualizar(Request $request, Distribuidores $distribuidor)
    {
        //Validaciones
        $request->validate(
            [
                'identificacion' => ['required', 'unique:sis_revendedores,identificacion,' . $distribuidor->sis_distribuidoresid . ',sis_distribuidoresid'],
                'razonsocial' => 'required',
                'nombrecomercial' => 'required',
                'correos' => ['required'],
            ],
            [
                'identificacion.required' => 'Ingrese su cédula o RUC ',
                'identificacion.unique' => 'Su cédula o RUC ya se encuentra registrado',
                'razonsocial.required' => 'Ingrese una Razón Social',
                'nombrecomercial.required' => 'Ingrese un Nombre Comercial',
                'correos.required' => 'Ingrese un Correo',
                //'correos.*.email' => 'Ingrese un Correo válido',
            ],
        );

        //Asignacion masiva para los campos asignados en guarded o fillable en el modelo
        $request['fechamodificacion'] = now();
        $request['usuariomodificacion'] = Auth::user()->nombres;
        $distribuidor->update($request->all());

        // $log = new Log();
        // $log->usuario = Auth::user()->nombres;
        // $log->pantalla = "Distribuidores";
        // $log->tipooperacion = "Modificar";
        // $log->fecha = now();
        // $log->detalle = $distribuidor;
        // $log->save();

        flash('Actualizado Correctamente')->success();
        return back();
    }

    public function eliminar(Distribuidores $distribuidor)
    {
        try {
            $distribuidor->delete();
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == '1451') {
                flash("Existen usuarios asociados al distribuidor")->error();
                return back();
            }
        }

        // $log = new Log();
        // $log->usuario = Auth::user()->nombres;
        // $log->pantalla = "Distribuidores";
        // $log->tipooperacion = "Eliminar";
        // $log->fecha = now();
        // $log->detalle = $distribuidor;
        // $log->save();

        flash("Eliminado Correctamente")->success();
        return back();
    }
}
