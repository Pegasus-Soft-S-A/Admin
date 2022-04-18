<?php

namespace App\Http\Controllers;

use App\Models\Agrupados;
use App\Models\Clientes;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables as DataTables;

class agrupadosController extends Controller
{

    public function generarContrato()
    {
        $randomString = "";
        while (strlen($randomString) < 10) {
            $numero = rand(1, 9);
            $randomString = $randomString . $numero;
        }

        return $randomString;
    }

    public function index(Request $request)
    {

        if ($request->ajax()) {

            $data = Agrupados::select('sis_agrupados.sis_agrupadosid', 'sis_agrupados.codigo', 'sis_clientes.nombres as nombres', 'sis_agrupados.fechainicio', 'sis_agrupados.fechacaduca', 'sis_agrupados.precio', 'sis_agrupados.empresas')->join('sis_clientes', 'sis_clientes.sis_clientesid', 'sis_agrupados.sis_clientesid')->get();

            return DataTables::of($data)

                ->editColumn('descripcion', function ($agrupado) {
                    return '<a class="text-primary" href="' . route('agrupados.editar', $agrupado->sis_agrupadosid) . '">' . $agrupado->nombres . ' </a>';
                })

                ->editColumn('action', function ($agrupado) {
                    return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('agrupados.editar', $agrupado->sis_agrupadosid) . '"  title="Editar"> <i class="la la-edit"></i> </a>' .
                        '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-2 confirm-delete" href="javascript:void(0)" data-href="' . route('agrupados.eliminar', $agrupado->sis_agrupadosid) . '" title="Eliminar"> <i class="la la-trash"></i> </a>';
                })
                ->editColumn('fechainicio', function ($agrupado) {
                    return date('d-m-Y', strtotime($agrupado['fechainicio']));
                })
                ->editColumn('fechacaduca', function ($agrupado) {
                    return date('d-m-Y', strtotime($agrupado['fechacaduca']));
                })
                ->rawColumns(['action', 'descripcion'])
                ->make(true);
        }
        return view('admin.agrupados.index');
    }

    public function crear()
    {
        $agrupados = new Agrupados();
        $codigo = $this->generarContrato();
        $existe = Agrupados::where('codigo', $codigo)->get();

        //Mientras exista en la base el numero seguira generando hasta que sea unico
        while (count($existe) > 0) {
            $codigo = $this->generarContrato();
            $existe = Agrupados::where('codigo', $codigo)->get();
        }
        $agrupados->codigo = $codigo;
        $agrupados->fechainicio = date("d-m-Y", strtotime(date("d-m-Y")));
        $agrupados->fechacaduca = date("d-m-Y", strtotime(date("d-m-Y")));

        return view('admin.agrupados.crear', compact('agrupados'));
    }

    public function guardar(Request $request)
    {
        $request->validate(
            [
                'sis_clientesid' => 'required',
                'fechainicio' => 'required',
                'fechacaduca' => 'required',
                'precio' => 'required',
                'empresas' => 'required',

            ],
            [
                'sis_clientesid.required' => 'Escoja un cliente',
                'fechainicio.required' => 'Ingrese Fecha Inicio',
                'fechacaduca.required' => 'Ingrese Fecha Caduca',
                'precio.required' => 'Ingrese Precio',
                'empresas.required' => 'Ingrese Numero de Empresas',
            ],
        );

        $request['fechainicio'] = date('Y-m-d', strtotime($request->fechainicio));
        $request['fechacaduca'] = date('Y-m-d', strtotime($request->fechacaduca));
        $agrupados =   Agrupados::create($request->all());

        $log = new Log();
        $log->usuario = Auth::user()->nombres;
        $log->pantalla = "Agrupados";
        $log->tipooperacion = "Crear";
        $log->fecha = now();
        $log->detalle = $agrupados;
        $log->save();
        flash('Registro Creado Correctamente')->success();
        return redirect()->route('agrupados.editar', $agrupados->sis_agrupadosid);
    }

    public function editar(Agrupados $agrupados)
    {
        $agrupados->fechacaduca = date("d-m-Y", strtotime($agrupados->fechacaduca));
        $agrupados->fechainicio = date("d-m-Y", strtotime($agrupados->fechainicio));
        return view('admin.agrupados.editar', compact('agrupados'));
    }
    public function actualizar(Agrupados $agrupados, Request $request)
    {

        $request->validate(
            [
                'sis_clientesid' => 'required',
                'fechainicio' => 'required',
                'fechacaduca' => 'required',
                'precio' => 'required',
                'empresas' => 'required',

            ],
            [
                'sis_clientesid.required' => 'Escoja un cliente',
                'fechainicio.required' => 'Ingrese Fecha Inicio',
                'fechacaduca.required' => 'Ingrese Fecha Caduca',
                'precio.required' => 'Ingrese Precio',
                'empresas.required' => 'Ingrese Numero de empresas',
            ],
        );
        $request['fechainicio'] = date('Y-m-d', strtotime($request->fechainicio));;
        $request['fechacaduca'] = date('Y-m-d', strtotime($request->fechacaduca));

        $agrupados->update($request->all());

        $log = new Log();
        $log->usuario = Auth::user()->nombres;
        $log->pantalla = "Agrupados";
        $log->tipooperacion = "Modificar";
        $log->fecha = now();
        $log->detalle = $agrupados;
        $log->save();

        flash('Actualizado Correctamente')->success();
        return back();
    }
    public function eliminar(Agrupados $agrupados)
    {
        $agrupados->delete();

        $log = new Log();
        $log->usuario = Auth::user()->nombres;
        $log->pantalla = "Agrupados";
        $log->tipooperacion = "Eliminar";
        $log->fecha = now();
        $log->detalle = $agrupados;
        $log->save();

        flash("Eliminado Correctamente")->success();
        return back();
    }
}
