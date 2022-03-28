<?php

namespace App\Http\Controllers;

use App\Models\Licencias;
use App\Models\Clientes;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Http;

class licenciasController extends Controller
{

    public function index(Request $request, Clientes $cliente)
    {

        if ($request->ajax()) {

            $data = Licencias::select('sis_licenciasid', 'numerocontrato', 'tipo_licencia', 'fechacaduca', 'sis_clientesid')
                ->where('sis_clientesid', $cliente->sis_clientesid);

            return DataTables::of($data)

                ->editColumn('numerocontrato', function ($data) {
                    if ($data->tipo_licencia == 1) {
                        return '<a class="text-primary" href="' . route('licencias.web.editar', [$data->sis_clientesid, $data->sis_licenciasid]) . '">' . $data->numerocontrato . ' </a>';
                    } else {
                        return '<a class="text-primary" href="' . route('licencias.pc.editar', [$data->sis_clientesid, $data->sis_licenciasid]) . '">' . $data->numerocontrato . ' </a>';
                    }
                })
                ->editColumn('action', function ($data) {
                    if ($data->tipo_licencia == 1) {
                        return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('licencias.web.editar', [$data->sis_clientesid, $data->sis_licenciasid]) . '" title="Editar"> <i class="la la-edit"></i> </a>' .
                            '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-2 confirm-delete" href="javascript:void(0)" data-href="' . route('licencias.eliminar', $data->sis_licenciasid) . '" title="Eliminar"> <i class="la la-trash"></i> </a>';
                    } else {
                        return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('licencias.pc.editar', [$data->sis_clientesid, $data->sis_licenciasid]) . '" title="Editar"> <i class="la la-edit"></i> </a>' .
                            '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-2 confirm-delete" href="javascript:void(0)" data-href="' . route('licencias.eliminar', $data->sis_licenciasid) . '" title="Eliminar"> <i class="la la-trash"></i> </a>';
                    }
                })
                ->editColumn('tipo_licencia', function ($data) {
                    if ($data->tipo_licencia == 1) {
                        return 'Perseo Web';
                    } else {
                        return 'Perseo PC';
                    }
                })
                ->rawColumns(['action', 'numerocontrato', 'tipo_licencia'])
                ->make(true);
        }
    }

    public function generarContrato()
    {
        $randomString = "";
        while (strlen($randomString) < 10) {
            $numero = rand(1, 10);
            $randomString = $randomString . $numero;
        }

        return $randomString;
    }

    public function crearWeb(Clientes $cliente)
    {
        $licencia = new Licencias();
        return view('admin.licencias.web.crear', compact('cliente', 'licencia'));
    }

    public function crearPC(Clientes $cliente)
    {
        $licencia = new Licencias();
        $licencia->fechacaduca = date("d-m-Y", strtotime(date("d-m-Y") . "+ 5 year"));
        $licencia->numeroequipos = 1;
        $licencia->usuario = "perseo";
        $licencia->clave = "Invencible4050*";
        $licencia->ipservidor = "127.0.0.1";
        $licencia->puerto = "5588";
        $licencia->actulizaciones = 1;
        $licencia->fechaactulizaciones = date("d-m-Y", strtotime(date("d-m-Y") . "+ 1 month"));

        $contrato = $this->generarContrato();
        $existe = Licencias::where('numerocontrato', $contrato)->get();

        //Mientras exista en la base el numero de contrato seguira generando hasta que sea unico
        while (count($existe) > 0) {
            $contrato = $this->generarContrato();
            $existe = Licencias::where('numerocontrato', $contrato)->get();
        }

        $licencia->numerocontrato = $contrato;

        return view('admin.licencias.pc.crear', compact('cliente', 'licencia'));
    }
}
