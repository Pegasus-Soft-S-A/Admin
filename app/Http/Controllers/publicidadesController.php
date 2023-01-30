<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Publicidades;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables as DataTables;
use Illuminate\Support\Facades\Auth;

class publicidadesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data =  Publicidades::all();

            return DataTables::of($data)

                ->editColumn('tipo', function ($publicidad) {
                    switch ($publicidad['tipo']) {
                        case '1':
                            $tipo = "Inicio";
                            break;
                        case '2':
                            $tipo = "Admin";
                            break;
                        case '3':
                            $tipo = "Registro";
                            break;
                    }
                    return '<a class="text-primary" href="' . route('publicidades.editar', $publicidad['sis_publicidadesid']) . '">' . $tipo . ' </a>';
                })
                ->editColumn('action', function ($publicidad) {
                    return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('publicidades.editar', $publicidad->sis_publicidadesid) . '"  title="Editar"> <i class="la la-edit"></i> </a>' .
                        '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-2 confirm-delete" href="javascript:void(0)" data-href="' . route('publicidades.eliminar', $publicidad->sis_publicidadesid) . '" title="Eliminar"> <i class="la la-trash"></i> </a>';
                })
                ->editColumn('fechainicio', function ($data) {
                    return date('d-m-Y', strtotime($data['fechainicio']));
                })
                ->editColumn('fechafin', function ($data) {
                    return date('d-m-Y', strtotime($data['fechafin']));
                })
                ->rawColumns(['action', 'tipo'])
                ->make(true);
        }
        return view('admin.publicidades.index');
    }

    public function crear()
    {
        $publicidades = new Publicidades();

        return view('admin.publicidades.crear', compact('publicidades'));
    }

    public function guardar(Request $request)
    {
        $request->validate(
            [
                'imagen' => 'required',
                'fechainicio' => 'required',
                'fechafin' => 'required',
            ],
            [
                'imagen.required' => 'Imagen requerida',
                'fechainicio.required' => 'Fecha inicio requerida',
                'fechafin.required' => 'Fecha fin requerida',
            ],
        );

        $publicidad = new Publicidades;
        $publicidad->tipo = $request->tipo;
        $publicidad->fechainicio = date('Ymd', strtotime($request->fechainicio));
        $publicidad->fechafin = date('Ymd', strtotime($request->fechafin));
        $publicidad->imagen = base64_encode(file_get_contents($request->file('imagen')));
        $publicidad->fechacreacion = now();
        $publicidad->usuariocreacion = Auth::user()->nombres;

        $publicidad->save();

        $log = new Log();
        $log->usuario = Auth::user()->nombres;
        $log->pantalla = "Publicidades";
        $log->tipooperacion = "Crear";
        $log->fecha = now();
        unset(
            $publicidad['imagen'],
        );
        $log->detalle = $publicidad;
        $log->save();

        flash('Publicidad creado correctamente')->success();
        return redirect()->route('publicidades.editar', $publicidad->sis_publicidadesid);
    }

    public function editar(Publicidades $publicidades)
    {
        $publicidades['fechainicio'] = date("d-m-Y", strtotime($publicidades['fechainicio']));
        $publicidades['fechafin'] = date("d-m-Y", strtotime($publicidades['fechafin']));
        return view('admin.publicidades.editar', compact('publicidades'));
    }

    public function actualizar($id, Request $request)
    {
        $request->validate(
            [
                //'imagen' => 'required',
                'fechainicio' => 'required',
                'fechafin' => 'required',
            ],
            [
                //'imagen.required' => 'Imagen requerida',
                'fechainicio.required' => 'Fecha inicio requerida',
                'fechafin.required' => 'Fecha fin requerida',
            ],
        );

        $publicidad = Publicidades::findOrFail($id);
        $publicidad->tipo = $request->tipo;
        $publicidad->fechainicio = date('Ymd', strtotime($request->fechainicio));
        $publicidad->fechafin = date('Ymd', strtotime($request->fechafin));

        if ($request->file('imagen')) {
            $publicidad->imagen = base64_encode(file_get_contents($request->file('imagen')));
        }

        $publicidad->fechamodificacion = now();
        $publicidad->usuariomodificacion = Auth::user()->nombres;
        $publicidad->save();

        $log = new Log();
        $log->usuario = Auth::user()->nombres;
        $log->pantalla = "Publicidades";
        $log->tipooperacion = "Modificar";
        $log->fecha = now();
        unset(
            $publicidad['imagen'],
        );
        $log->detalle = $publicidad;
        $log->save();

        flash('Actualizado Correctamente')->success();
        return back();
    }

    public function eliminar(Publicidades $publicidades)
    {
        $publicidades->delete();

        $log = new Log();
        $log->usuario = Auth::user()->nombres;
        $log->pantalla = "Publicidades";
        $log->tipooperacion = "Eliminar";
        $log->fecha = now();
        unset(
            $publicidades['imagen'],
        );
        $log->detalle = $publicidades;
        $log->save();

        flash("Eliminado Correctamente")->success();
        return back();
    }
}
