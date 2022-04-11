<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Servidores;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables as DataTables;
use Illuminate\Support\Facades\Auth;

class servidoresController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Servidores::all();
            return DataTables::of($data)

                ->editColumn('descripcion', function ($servidor) {
                    return '<a class="text-primary" href="' . route('servidores.editar', $servidor->sis_servidoresid) . '">' . $servidor->descripcion . ' </a>';
                })

                ->editColumn('action', function ($servidor) {
                    return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('servidores.editar', $servidor->sis_servidoresid) . '"  title="Editar"> <i class="la la-edit"></i> </a>' .
                        '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-2 confirm-delete" href="javascript:void(0)" data-href="' . route('servidores.eliminar', $servidor->sis_servidoresid) . '" title="Eliminar"> <i class="la la-trash"></i> </a>';
                })

                ->rawColumns(['action', 'descripcion'])
                ->make(true);
        }
        return view('admin.servidores.index');
    }

    public function crear()
    {
        $servidores = new Servidores();
        return view('admin.servidores.crear', compact('servidores'));
    }

    public function guardar(Request $request)
    {

        $request->validate(
            [
                'descripcion' => 'required',
                'dominio' => 'required',

            ],
            [
                'descripcion.required' => 'Ingrese Descripción ',
                'dominio.required' => 'Ingrese Dominio',
            ],
        );


        $servidores =   Servidores::create($request->all());

        $log = new Log();
        $log->usuario = Auth::user()->nombres;
        $log->pantalla = "Servidores";
        $log->tipooperacion = "Crear";
        $log->fecha = now();
        $log->detalle = $servidores;
        $log->save();
        flash('Servidor creado correctamente')->success();
        return redirect()->route('servidores.editar', $servidores->sis_servidoresid);
    }

    public function editar(Servidores $servidores)
    {

        return view('admin.servidores.editar', compact('servidores'));
    }
    public function actualizar(Servidores $servidores, Request $request)
    {
        $request->validate(
            [
                'descripcion' => 'required',
                'dominio' => 'required',
            ],
            [
                'descripcion.required' => 'Ingrese su cédula o RUC ',
                'dominio.required' => 'Ingrese Nombres',
            ],
        );


        $servidores->update($request->all());

        $log = new Log();
        $log->usuario = Auth::user()->nombres;
        $log->pantalla = "Servidores";
        $log->tipooperacion = "Modificar";
        $log->fecha = now();
        $log->detalle = $servidores;
        $log->save();

        flash('Actualizado Correctamente')->success();
        return back();
    }
    public function eliminar(Servidores $servidores)
    {
        $servidores->delete();

        $log = new Log();
        $log->usuario = Auth::user()->nombres;
        $log->pantalla = "Servidores";
        $log->tipooperacion = "Eliminar";
        $log->fecha = now();
        $log->detalle = $servidores;
        $log->save();

        flash("Eliminado Correctamente")->success();
        return back();
    }
}
