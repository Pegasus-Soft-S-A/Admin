<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Log;
use App\Models\Servidores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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


        $servidores = Servidores::create($request->all());

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

    // API
    public function servidores(Request $request)
    {
        $identificacionIngresada = substr($request->identificacion, 0, 10);
        $cliente = Clientes::select('sis_clientesid')->where(DB::raw('substr(identificacion, 1, 10)'), $identificacionIngresada)->get();

        if ($request->tipo == 1) {
            $servidores = Servidores::where('estado', 1)->where('sis_servidoresid', '!=', 2)->get();
        } else {
            $servidores = Servidores::where('estado', 1)->where('sis_servidoresid', 2)->get();
        }
        // $servidores = Servidores::where('estado', 1)->get();
        $array = [];

        foreach ($cliente as $usuario) {
            foreach ($servidores as $servidor) {
                $url = $servidor->dominio . '/registros/consulta_licencia';
                $resultado = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                    ->withOptions(["verify" => false])
                    ->post($url, ['sis_clientesid' => $usuario->sis_clientesid])
                    ->json();

                if (isset($resultado['licencias'])) {
                    $array[] = ["sis_servidoresid" => $servidor->sis_servidoresid, "descripcion" => $servidor->descripcion, "dominio" => $servidor->dominio];
                }
            }
        }

        if (count($array) > 0) {
            $servidoresJson = json_encode(["servidor" => $array]);
            return $servidoresJson;
        } else {
            return json_encode(["servidor" => 0]);
        }
    }

    public function servidores_activos()
    {
        $servidores = Servidores::where('estado', 1)
            ->where('sis_servidoresid', '!=', 3)
            ->get();
        return json_encode($servidores);
    }

    public function servidores_activos1()
    {
        $servidores = Servidores::where('estado', 1)
            ->get();
        return json_encode($servidores);
    }
}
