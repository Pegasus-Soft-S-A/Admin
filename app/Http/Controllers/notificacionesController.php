<?php

namespace App\Http\Controllers;

use App\Models\Distribuidores;
use App\Models\Log;
use App\Models\Notificaciones;
use App\Services\LogService;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables as DataTables;
use Illuminate\Support\Facades\Auth;

class notificacionesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Auth::user()->tipo == 1 ? Notificaciones::all() : Notificaciones::where("sis_distribuidores_usuariosid", Auth::user()->sis_distribuidores_usuariosid);

            return DataTables::of($data)
                ->editColumn('asunto', function ($notificacion) {
                    return '<a class="text-primary" href="' . route('notificaciones.editar', $notificacion->sis_notificacionesid) . '">' . $notificacion->asunto . ' </a>';
                })
                ->editColumn('action', function ($notificacion) {
                    return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('notificaciones.editar', $notificacion->sis_notificacionesid) . '"  title="Editar"> <i class="la la-edit"></i> </a>' .
                        '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-2 confirm-delete" href="javascript:void(0)" data-href="' . route('notificaciones.eliminar', $notificacion->sis_notificacionesid) . '" title="Eliminar"> <i class="la la-trash"></i> </a>';
                })
                ->editColumn('fecha_publicacion_desde', function ($data) {
                    return date('d-m-Y', strtotime($data['fecha_publicacion_desde']));
                })
                ->editColumn('fecha_publicacion_hasta', function ($data) {
                    return date('d-m-Y', strtotime($data['fecha_publicacion_hasta']));
                })
                ->rawColumns(['action', 'asunto'])
                ->make(true);
        }
        return view('admin.notificaciones.index');
    }

    public function crear()
    {
        $notificaciones = new Notificaciones();
        $distribuidores = Distribuidores::all();

        return view('admin.notificaciones.crear', compact('notificaciones', 'distribuidores'));
    }

    public function guardar(Request $request)
    {

        $request->validate(
            [
                'asunto' => 'required',
                'contenido' => 'required',

            ],
            [
                'asunto.required' => 'Ingrese Asunto ',
                'contenido.required' => 'Ingrese Contenido',
            ],
        );

        $request['sis_distribuidores_usuariosid'] = Auth::user()->sis_distribuidores_usuariosid;
        $request['fechacreacion'] = now();
        $request['usuariocreacion'] = Auth::user()->nombres;
        $request['fecha_publicacion_desde'] = date('Ymd', strtotime($request->fecha_publicacion_desde));
        $request['fecha_publicacion_hasta'] = date('Ymd', strtotime($request->fecha_publicacion_hasta));

        unset(
            $request['files'],
        );

        $notificaciones = Notificaciones::create($request->all());

        LogService::crear('Notificaciones', $notificaciones);

        flash('Notificacion creado correctamente')->success();
        return redirect()->route('notificaciones.editar', $notificaciones->sis_notificacionesid);
    }

    public function editar(Notificaciones $notificaciones)
    {

        $distribuidores = Distribuidores::all();
        $notificaciones['fecha_publicacion_desde'] = date("d-m-Y", strtotime($notificaciones['fecha_publicacion_desde']));
        $notificaciones['fecha_publicacion_hasta'] = date("d-m-Y", strtotime($notificaciones['fecha_publicacion_hasta']));

        return view('admin.notificaciones.editar', compact('notificaciones', 'distribuidores'));
    }

    public function actualizar(Notificaciones $notificaciones, Request $request)
    {
        $request->validate(
            [
                'asunto' => 'required',
                'contenido' => 'required',

            ],
            [
                'asunto.required' => 'Ingrese Asunto ',
                'contenido.required' => 'Ingrese Contenido',
            ],
        );

        $request['sis_distribuidores_usuariosid'] = Auth::user()->sis_distribuidores_usuariosid;
        $request['fechamodificacion'] = now();
        $request['usuariomodificacion'] = Auth::user()->nombres;
        $request['fecha_publicacion_desde'] = date('Ymd', strtotime($request->fecha_publicacion_desde));
        $request['fecha_publicacion_hasta'] = date('Ymd', strtotime($request->fecha_publicacion_hasta));

        unset(
            $request['files'],
        );

        $notificaciones->update($request->all());

        LogService::modificar('Notificaciones', $notificaciones);

        flash('Actualizado Correctamente')->success();
        return back();
    }

    public function eliminar(Notificaciones $notificaciones)
    {
        $notificaciones->delete();

        LogService::eliminar('Notificaciones', $notificaciones);

        flash("Eliminado Correctamente")->success();
        return back();
    }

    // API
    public function consulta_notificaciones(Request $request)
    {
        $notificaciones = Notificaciones::where(function ($query) use ($request) {
            $query->where('fecha_publicacion_desde', '<=', $request->inicio)
                ->where('fecha_publicacion_hasta', '>=', $request->inicio);
        })
            ->whereIn('tipo', [0, $request->tipo])
            ->whereIn('sis_distribuidoresid', [0, $request->distribuidor])
            ->get();

        return json_encode($notificaciones);
    }
}
