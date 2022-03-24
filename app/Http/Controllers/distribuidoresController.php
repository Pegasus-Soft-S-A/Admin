<?php

namespace App\Http\Controllers;

use App\Models\Distribuidores;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables as DataTables;

class distribuidoresController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $data = Distribuidores::select('sis_distribuidoresid', 'razonsocial', 'nombrecomercial', 'correos');

            return DataTables::of($data)
                // ->addIndexColumn()
                ->editColumn('razonsocial', function ($distribuidor) {
                    return '<a class="text-primary" href="' . route('distribuidores.editar', $distribuidor->sis_distribuidoresid) . '">' . $distribuidor->razonsocial . ' </a>';
                })
                ->editColumn('action', function ($distribuidor) {
                    return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('distribuidores.editar', $distribuidor->sis_distribuidoresid) . '" title="Editar"> <i class="la la-edit"></i> </a>' .
                        '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-2 confirm-delete" href="javascript:void(0)" data-href="' . route('distribuidores.eliminar', $distribuidor->sis_distribuidoresid) . '" title="Eliminar"> <i class="la la-trash"></i> </a>';
                })
                ->rawColumns(['action', 'razonsocial'])
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
                'razonsocial' => 'required',
                'nombrecomercial' => 'required',
                'correos' => ['required'],
            ],
            [
                'razonsocial.required' => 'Ingrese una Razón Social',
                'nombrecomercial.required' => 'Ingrese un Nombre Comercial',
                'correos.required' => 'Ingrese un Correo',
                //'correos.*.email' => 'Ingrese un Correo válido',
            ],
        );

        //Asignacion masiva para los campos asignados en guarded o fillable en el modelo
        $distribuidor = Distribuidores::create($request->all());

        flash('Guardado Correctamente')->success();
        return view('admin.distribuidores.editar', compact('distribuidor'));
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
                'razonsocial' => 'required',
                'nombrecomercial' => 'required',
                'correos' => ['required'],
            ],
            [
                'razonsocial.required' => 'Ingrese una Razón Social',
                'nombrecomercial.required' => 'Ingrese un Nombre Comercial',
                'correos.required' => 'Ingrese un Correo',
                //'correos.*.email' => 'Ingrese un Correo válido',
            ],
        );

        //Asignacion masiva para los campos asignados en guarded o fillable en el modelo
        $distribuidor->update($request->all());

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
        flash("Eliminado Correctamente")->success();
        return back();
    }
}
