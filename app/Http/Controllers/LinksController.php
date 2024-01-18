<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Distribuidores;
use App\Models\Links;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables as DataTables;

class LinksController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Links::all();

            return DataTables::of($data)

                ->editColumn('codigo', function ($link) {
                    return '<a class="text-primary" href="' . route('links.editar', $link->sis_linksid) . '">' . $link->codigo . ' </a>';
                })

                ->editColumn('action', function ($link) {
                    return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('links.editar', $link->sis_linksid) . '"  title="Editar"> <i class="la la-edit"></i> </a>' .
                        '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-2 confirm-delete" href="javascript:void(0)" data-href="' . route('links.eliminar', $link->sis_linksid) . '" title="Eliminar"> <i class="la la-trash"></i> </a>';
                })
                ->rawColumns(['action', 'codigo'])
                ->make(true);
        }
        return view('admin.links.index');
    }

    public function crear()
    {
        $links = new Links();
        $links->estado = 1;
        $distribuidores = Distribuidores::all();

        return view('admin.links.crear', compact('links', 'distribuidores'));
    }

    public function guardar(Request $request)
    {

        $request->validate(
            [
                'codigo' => 'required',
            ],
            [
                'codigo.required' => 'Ingrese Código ',
            ],
        );

        $request['fechacreacion'] = now();
        $request['usuariocreacion'] = Auth::user()->nombres;
        $request['registra_bitrix'] = $request->registra_bitrix == 'on' ? 1 : 0;
        $request['estado'] = $request->estado == 'on' ? 1 : 0;

        $links =   Links::create($request->all());

        $log = new Log();
        $log->usuario = Auth::user()->nombres;
        $log->pantalla = "Links";
        $log->tipooperacion = "Crear";
        $log->fecha = now();
        $log->detalle = $links;
        $log->save();

        flash('Link creado correctamente')->success();
        return redirect()->route('links.editar', $links->sis_linksid);
    }

    public function editar(Links $links)
    {

        $distribuidores = Distribuidores::all();

        return view('admin.links.editar', compact('links', 'distribuidores'));
    }

    public function actualizar(Links $links, Request $request)
    {
        $request->validate(
            [
                'codigo' => 'required',
            ],
            [
                'codigo.required' => 'Ingrese Código ',
            ],
        );

        $request['fechamodificacion'] = now();
        $request['usuariomodificacion'] = Auth::user()->nombres;
        $request['registra_bitrix'] = $request->registra_bitrix == 'on' ? 1 : 0;
        $request['estado'] = $request->estado == 'on' ? 1 : 0;

        $links->update($request->all());

        $log = new Log();
        $log->usuario = Auth::user()->nombres;
        $log->pantalla = "Links";
        $log->tipooperacion = "Modificar";
        $log->fecha = now();
        $log->detalle = $links;
        $log->save();

        flash('Actualizado Correctamente')->success();
        return back();
    }

    public function eliminar(Links $links)
    {
        $clientes = Clientes::where('red_origen', $links->sis_linksid)->get();

        if (count($clientes) > 0) {
            flash("No se puede eliminar, existen clientes asociados")->error();
            return back();
        }

        $links->delete();

        $log = new Log();
        $log->usuario = Auth::user()->nombres;
        $log->pantalla = "Links";
        $log->tipooperacion = "Eliminar";
        $log->fecha = now();
        $log->detalle = $links;
        $log->save();

        flash("Eliminado Correctamente")->success();
        return back();
    }
}
