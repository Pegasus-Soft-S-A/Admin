<?php

namespace App\Http\Controllers;

use App\Models\Licencias;
use App\Models\Clientes;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

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
        $licencia->numeromoviles = 0;
        $licencia->numerosucursales = 0;
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

    public function guardar(Request $request)
    {
        //Validaciones
        $request->validate(
            [
                'Identificador' => 'required',
                'correopropietario' => ['required', 'email'],
                'correoadministrador' => ['required', 'email'],
                'correocontador' => ['required', 'email'],
            ],
            [
                'Identificador.required' => 'Ingrese un Identificador',
                'correopropietario.required' => 'Ingrese un Correo de Propietario',
                'correopropietario.email' => 'Ingrese un Correo de Propietario válido',
                'correoadministrador.required' => 'Ingrese un Correo de Administrador',
                'correoadministrador.email' => 'Ingrese un Correo de adminisrador válido',
                'correocontador.required' => 'Ingrese un Correo de Contador',
                'correocontador.email' => 'Ingrese un Correo de Contador válido',
            ],
        );

        //Asignacion masiva para los campos asignados en guarded o fillable en el modelo
        $request['fechacreacion'] = now();
        $request['fechainicia'] = date('Y-m-d', strtotime(now()));
        $request['fechacaduca'] = date('Y-m-d', strtotime($request->fechacaduca));
        $request['fechaactulizaciones'] = date('Y-m-d', strtotime($request->fechaactulizaciones));
        $request['fechaultimopago'] = date('Y-m-d', strtotime(now()));
        $request['usuariocreacion'] = Auth::user()->nombres;
        $request['modulopractico'] = $request->modulopractico == 'on' ? 1 : 0;
        $request['modulocontrol'] = $request->modulocontrol == 'on' ? 1 : 0;
        $request['modulocontable'] = $request->modulocontable == 'on' ? 1 : 0;
        $request['actulizaciones'] = $request->actulizaciones == 'on' ? 1 : 0;
        $request['numerogratis'] =  0;
        $request['tokenrespaldo'] =  "";
        $request['tipo_licencia'] =  2;
        $request['sis_distribuidoresid'] =  Auth::user()->sis_distribuidoresid;

        $modulos = [];
        $modulos = [
            'nomina' => $request['nomina'] = $request->nomina == 'on' ? true : false,
            'activos' => $request['activos'] = $request->activos == 'on' ? true : false,
            'produccion' => $request['produccion'] = $request->produccion == 'on' ? true : false,
            'restaurantes' => $request['restaurantes'] = $request->restaurantes == 'on' ? true : false,
            'talleres' => $request['talleres'] = $request->talleres == 'on' ? true : false,
            'garantias' => $request['garantias'] = $request->garantias == 'on' ? true : false,
            'operadoras' => $request['tvcable'] = $request->tvcable == 'on' ? true : false,
            'encomiendas' => $request['encomiendas'] = $request->encomiendas == 'on' ? true : false,
            'crm_cartera' => $request['crmcartera'] = $request->crmcartera == 'on' ? true : false,
            'tienda_perseo_publico' => $request['tienda'] = $request->tienda == 'on' ? true : false,
            'perseo_hybrid' => $request['hybrid'] = $request->hybrid == 'on' ? true : false,
            'tienda_woocommerce' => $request['woocomerce'] = $request->woocomerce == 'on' ? true : false,
            'api_whatsapp' => $request['apiwhatsapp'] = $request->apiwhatsapp == 'on' ? true : false,
            'cash_manager' => $request['cashmanager'] = $request->cashmanager == 'on' ? true : false,
            'reporte_equifax' => $request['equifax'] = $request->equifax == 'on' ? true : false,
        ];
        unset(
            $request['nomina'],
            $request['activos'],
            $request['produccion'],
            $request['restaurantes'],
            $request['talleres'],
            $request['garantias'],
            $request['tvcable'],
            $request['encomiendas'],
            $request['crmcartera'],
            $request['tienda'],
            $request['hybrid'],
            $request['woocomerce'],
            $request['apiwhatsapp'],
            $request['cashmanager'],
            $request['equifax'],
        );
        $request['modulos'] = $modulos;
        $licencia =   Licencias::create($request->all());

        $log = new Log();
        $log->usuario = Auth::user()->nombres;
        $log->pantalla = "Licencia PC";
        $log->tipooperacion = "Crear";
        $log->fecha = now();
        $log->detalle = $licencia;
        $log->save();

        flash('Guardado Correctamente')->success();
        return redirect()->route('licencias.pc.editar', [$request['sis_clientesid'], $licencia->sis_licenciasid]);
    }

    public function editarPC(Clientes $cliente, Licencias $licencia)
    {
        return view('admin.licencias.pc.editar', compact('cliente', 'licencia'));
    }
}
