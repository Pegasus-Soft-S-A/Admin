<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Servidores;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables as DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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

    public function migrar(Request $request)
    {
        $dominioorigen = Servidores::select('dominio')->where('sis_servidoresid', $request['servidororigen'])->first();
        $urlorigen = $dominioorigen->dominio . '/registros/respaldar_empresa';
        $resultado = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->post($urlorigen, ['sis_licenciasid' => $request['licenciaorigen']])
            ->json();


        if (isset($resultado['licencia'])) {
            $decodificar = json_decode(json_encode($resultado));
            $dominiodestino = Servidores::select('dominio')->where('sis_servidoresid', $request['servidordestino'])->first();
            $urldestino = $dominiodestino->dominio . '/registros/restaurar_empresa';
            $resultado2 = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false])
                ->withOptions(["verify" => false])
                ->post($urldestino, ["licencia" => $decodificar->licencia, "respaldo_empresa" => $decodificar->respaldo_empresa, "sis_servidoresdestinoid" => (int) $request['servidordestino'], "sis_distribuidores_usuariosid" => Auth::user()->sis_distribuidores_usuariosid])
                ->json();

            if (isset($resultado2['licencias'])) {
                $urleliminarorigen = $dominioorigen->dominio . '/registros/eliminar_licencia';
                $eliminarLicencia = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                    ->withOptions(["verify" => false])
                    ->post($urleliminarorigen, ['sis_licenciasid' => $request['licenciaorigen']])
                    ->json();

                if (isset($eliminarLicencia['respuesta'])) {
                    return 1;
                } else {
                    return 4;
                }
            }

            return 2;
        } else {

            return 3;
        }
    }
}
