<?php

namespace App\Http\Controllers;

use App\Models\Licencias;
use App\Models\Clientes;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

class licenciasController extends Controller
{

    public function index(Request $request, Clientes $cliente)
    {

        if ($request->ajax()) {

            $url = 'http://localhost:8026/registros/consulta_licencia';
            $resultado = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                ->withOptions(["verify" => false])
                ->post($url, ['sis_clientesid' => $cliente->sis_clientesid])
                ->json();

            $data = Licencias::select('sis_licenciasid', 'numerocontrato', 'tipo_licencia', 'fechacaduca', 'sis_clientesid')
                ->where('sis_clientesid', $cliente->sis_clientesid)
                ->get();

            if (isset($resultado['licencias'])) {
                $unir = array_merge($resultado['licencias'], $data->toArray());
            } else {
                $unir =  $data->toArray();
            }

            return DataTables::of($unir)

                ->editColumn('numerocontrato', function ($data) {
                    if ($data['tipo_licencia'] == 1) {
                        return '<a class="text-primary" href="' . route('licencias.web.editar', [$data['sis_clientesid'], $data['sis_licenciasid']]) . '">' . $data['numerocontrato'] . ' </a>';
                    } else {
                        return '<a class="text-primary" href="' . route('licencias.pc.editar', [$data['sis_clientesid'], $data['sis_licenciasid']]) . '">' . $data['numerocontrato'] . ' </a>';
                    }
                })
                ->editColumn('action', function ($data) {
                    if ($data['tipo_licencia'] == 1) {
                        return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('licencias.web.editar', [$data['sis_clientesid'], $data['sis_licenciasid']]) . '" title="Editar"> <i class="la la-edit"></i> </a>' .
                            '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-2 confirm-delete" href="javascript:void(0)" data-href="' . route('licencias.web.eliminar', $data['sis_licenciasid']) . '" title="Eliminar"> <i class="la la-trash"></i> </a>';
                    } else {
                        return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('licencias.pc.editar', [$data['sis_clientesid'], $data['sis_licenciasid']]) . '" title="Editar"> <i class="la la-edit"></i> </a>' .
                            '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-2 confirm-delete" href="javascript:void(0)" data-href="' . route('licencias.pc.eliminar', $data['sis_licenciasid']) . '" title="Eliminar"> <i class="la la-trash"></i> </a>';
                    }
                })
                ->editColumn('tipo_licencia', function ($data) {
                    if ($data['tipo_licencia'] == 1) {
                        return 'Perseo Web';
                    } else {
                        return 'Perseo PC';
                    }
                })
                ->editColumn('fechacaduca', function ($data) {
                    return date('d-m-Y', strtotime($data['fechacaduca']));
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
        //$licencia->fechainicia = date("d-m-Y", strtotime(date("d-m-Y")));
        // $licencia->fechacaduca = date("d-m-Y", strtotime(date("d-m-Y") . "+ 1 month"));
        $contrato = $this->generarContrato();
        $existe = Licencias::where('numerocontrato', $contrato)->get();

        $url = 'http://localhost:8026/registros/consulta_licencia';
        $existeWeb = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->post($url, ['numerocontrato' => $contrato])
            ->json();



        //Mientras exista en la base el numero de contrato seguira generando hasta que sea unico
        while (count($existe) > 0 || isset($existeWeb['licencias'])) {
            $contrato = $this->generarContrato();
            $existe = Licencias::where('numerocontrato', $contrato)->get();

            $existeWeb = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                ->withOptions(["verify" => false])
                ->post($url, ['numerocontrato' => $contrato])
                ->json();
        }

        $licencia->numerocontrato = $contrato;
        $licencia->numerosucursales = 0;
        $licencia->empresas = 1;
        $modulos = [
            'nomina' => false,
            'activos' => false,
            'produccion' => false,
            'restaurantes' => false,
            'talleres' => false,
            'garantias' => false,
            'ecommerce' => false,
        ];

        $modulos = json_encode($modulos);
        $modulos = json_decode($modulos);

        return view('admin.licencias.web.crear', compact('cliente', 'licencia', 'modulos'));
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
        $licencia->aplicaciones = "";
        $licencia->fechaactulizaciones = date("d-m-Y", strtotime(date("d-m-Y") . "+ 1 month"));

        $contrato = $this->generarContrato();
        $existe = Licencias::where('numerocontrato', $contrato)->get();

        //Mientras exista en la base el numero de contrato seguira generando hasta que sea unico
        while (count($existe) > 0) {
            $contrato = $this->generarContrato();
            $existe = Licencias::where('numerocontrato', $contrato)->get();
        }

        $licencia->numerocontrato = $contrato;
        $modulos = [];
        $modulos = [
            'nomina' => false,
            'activos' => false,
            'produccion' => false,
            'restaurante' => false,
            'talleres' => false,
            'garantias' => false,
            'operadoras' => false,
            'encomiendas' => false,
            'crm_cartera' => false,
            'tienda_perseo_publico' => false,
            'tienda_perseo_distribuidor' => false,
            'perseo_hybrid' => false,
            'tienda_woocommerce' => false,
            'api_whatsapp' => false,
            'cash_manager' => false,
            'reporte_equifax' => false,
        ];

        $modulos = json_encode([$modulos]);
        $modulos = json_decode($modulos);

        return view('admin.licencias.pc.crear', compact('cliente', 'licencia', 'modulos'));
    }

    public function guardarPC(Request $request)
    {
        //Validaciones
        $request->validate(
            [
                'Identificador' => ['required', 'unique:sis_licencias'],
                'correopropietario' => ['required', 'email'],
                'correoadministrador' => ['required', 'email'],
                'correocontador' => ['required', 'email'],
            ],
            [
                'Identificador.required' => 'Ingrese un Identificador',
                'Identificador.unique' => 'El identificador ya se encuentra registrado',
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
        $request['tokenrespaldo'] =  $request['tokenrespaldo'] == "" ? "" : $request['tokenrespaldo'];
        $request['key'] =  $request['key'] == "" ? "" : $request['key'];
        $request['tipo_licencia'] =  2;
        $request['sis_distribuidoresid'] =  Auth::user()->sis_distribuidoresid;

        $modulos = [];
        $modulos = [
            'nomina' => $request['nomina'] = $request->nomina == 'on' ? true : false,
            'activos' => $request['activos'] = $request->activos == 'on' ? true : false,
            'produccion' => $request['produccion'] = $request->produccion == 'on' ? true : false,
            'restaurante' => $request['restaurantes'] = $request->restaurantes == 'on' ? true : false,
            'talleres' => $request['talleres'] = $request->talleres == 'on' ? true : false,
            'garantias' => $request['garantias'] = $request->garantias == 'on' ? true : false,
            'operadoras' => $request['tvcable'] = $request->tvcable == 'on' ? true : false,
            'encomiendas' => $request['encomiendas'] = $request->encomiendas == 'on' ? true : false,
            'crm_cartera' => $request['crmcartera'] = $request->crmcartera == 'on' ? true : false,
            'tienda_perseo_distribuidor' => $request['integraciones'] = $request->integraciones == 'on' ? true : false,
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
            $request['restaurante'],
            $request['talleres'],
            $request['garantias'],
            $request['tvcable'],
            $request['encomiendas'],
            $request['crmcartera'],
            $request['tienda'],
            $request['integraciones'],
            $request['hybrid'],
            $request['woocomerce'],
            $request['apiwhatsapp'],
            $request['cashmanager'],
            $request['equifax'],
        );
        $request['modulos'] = json_encode([$modulos]);
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

    public function guardarWeb(Request $request)
    {
        //Asignacion masiva para los campos asignados en guarded o fillable en el modelo
        $request['fechacreacion'] = date('Y-m-d H:i:s', strtotime(now()));
        $request['usuariocreacion'] = Auth::user()->nombres;
        $request['fechainicia'] = date('Ymd', strtotime($request->fechainicia));
        $request['fechacaduca'] = date('Ymd', strtotime($request->fechacaduca));
        $request['tipo_licencia'] =  1;
        $request['sis_distribuidoresid'] =  Auth::user()->sis_distribuidoresid;
        $request['Identificador'] = $request['numerocontrato'];
        $request['fechaultimopago'] = $request['fechainicia'];

        if ($request['producto'] == "6") {
            $parametros_json = [];
            $parametros_json = [
                'Documentos' => "120",
                'Productos' => "500",
                'Almacenes' => "1",
                'Nomina' => "3",
                'Produccion' => "3",
                'Activos' => "3",
                'Talleres' => "3",
                'Garantias' => "3",
            ];
            $request['parametros_json'] = json_encode($parametros_json);
        }

        $xw = xmlwriter_open_memory();
        //xmlwriter_set_indent($xw, 1);
        //xmlwriter_set_indent_string($xw, ' ');
        xmlwriter_start_document($xw, '1.0', 'UTF-8');
        xmlwriter_start_element($xw, 'modulos');

        xmlwriter_start_element($xw, 'nomina');
        xmlwriter_text($xw, $request->nomina == 'on' ? 1 : 0);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'activos');
        xmlwriter_text($xw, $request->activos == 'on' ? 1 : 0);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'produccion');
        xmlwriter_text($xw, $request->produccion == 'on' ? 1 : 0);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'restaurantes');
        xmlwriter_text($xw, $request->restaurantes == 'on' ? 1 : 0);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'talleres');
        xmlwriter_text($xw, $request->talleres == 'on' ? 1 : 0);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'garantias');
        xmlwriter_text($xw, $request->garantias == 'on' ? 1 : 0);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'ecommerce');
        xmlwriter_text($xw, $request->ecommerce == 'on' ? 1 : 0);
        xmlwriter_end_element($xw);

        xmlwriter_end_element($xw);
        xmlwriter_end_document($xw);

        $request['modulos'] = xmlwriter_output_memory($xw);

        unset(
            $request['nomina'],
            $request['activos'],
            $request['produccion'],
            $request['restaurantes'],
            $request['talleres'],
            $request['garantias'],
            $request['ecommerce'],
            $request['tipo'],
        );


        $url = 'http://localhost:8026/registros/crear_licencias';
        $licencia = $request->all();

        $crearLicenciaWeb = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->post($url, $licencia)
            ->json();


        if (isset($crearLicenciaWeb["licencias"])) {
            $licenciaId = $crearLicenciaWeb["licencias"][0]['sis_licenciasid'];
            $request['sis_licenciasid'] = $licenciaId;

            $log = new Log();
            $log->usuario = Auth::user()->nombres;
            $log->pantalla = "Licencia PC";
            $log->tipooperacion = "Crear";
            $log->fecha = now();
            $log->detalle = json_encode($request->all());
            $log->save();


            flash('Guardado Correctamente')->success();
            return redirect()->route('licencias.web.editar', [$request['sis_clientesid'], $licenciaId]);
        } else {
            flash('Ocurrió un error vuelva a intentarlo')->warning();
            return back();
        }
    }

    public function editarPC(Clientes $cliente, Licencias $licencia)
    {
        $modulos = json_decode($licencia->modulos);
        $licencia->fechacaduca = date("d-m-Y", strtotime($licencia->fechacaduca));
        $licencia->fechaactulizaciones = date("d-m-Y", strtotime($licencia->fechaactulizaciones));
        return view('admin.licencias.pc.editar', compact('cliente', 'licencia', 'modulos'));
    }

    public function editarWeb(Clientes $cliente, $licenciaid)
    {
        $url = 'http://localhost:8026/registros/consulta_licencia';
        $licenciaConsulta = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->post($url, ['sis_licenciasid' => $licenciaid])
            ->json();

        $licenciaEnviar = $licenciaConsulta['licencias'][0];
        $licenciaEnviar['fechainicia'] = date("d-m-Y", strtotime($licenciaEnviar['fechainicia']));
        $licenciaEnviar['fechacaduca'] = date("d-m-Y", strtotime($licenciaEnviar['fechacaduca']));
        $licenciaEnviar['fechacreacion'] = date("Y-m-d H:i:s", strtotime($licenciaEnviar['fechacreacion']));

        if ($licenciaEnviar['fechamodificacion'] != "0000-00-00T00:00:00.000") {
            $licenciaEnviar['fechamodificacion'] = date("Y-m-d H:i:s", strtotime($licenciaEnviar['fechamodificacion']));
        } else {
            $licenciaEnviar['fechamodificacion'] = "";
        }

        $modulos = simplexml_load_string($licenciaEnviar['modulos']);
        $licenciaArray = json_encode($licenciaEnviar);
        $licencia = json_decode($licenciaArray);

        return view('admin.licencias.web.editar', compact('cliente', 'licencia', 'modulos'));
    }

    public function actualizarPC(Request $request, Licencias $licencia)
    {
        //Validaciones
        $request->validate(
            [
                'Identificador' => ['required', 'unique:sis_licencias,identificador,' . $licencia->sis_licenciasid . ',sis_licenciasid'],
                'correopropietario' => ['required', 'email'],
                'correoadministrador' => ['required', 'email'],
                'correocontador' => ['required', 'email'],
            ],
            [
                'Identificador.required' => 'Ingrese un Identificador',
                'Identificador.unique' => 'El identificador ya se encuentra registrado',
                'correopropietario.required' => 'Ingrese un Correo de Propietario',
                'correopropietario.email' => 'Ingrese un Correo de Propietario válido',
                'correoadministrador.required' => 'Ingrese un Correo de Administrador',
                'correoadministrador.email' => 'Ingrese un Correo de adminisrador válido',
                'correocontador.required' => 'Ingrese un Correo de Contador',
                'correocontador.email' => 'Ingrese un Correo de Contador válido',
            ],
        );

        //Asignacion masiva para los campos asignados en guarded o fillable en el modelo
        $request['fechamodificacion'] = now();
        $request['usuariomodificacion'] = Auth::user()->nombres;
        $request['fechacaduca'] = date('Y-m-d', strtotime($request->fechacaduca));
        $request['fechaactulizaciones'] = date('Y-m-d', strtotime($request->fechaactulizaciones));

        $request['modulopractico'] = $request->modulopractico == 'on' ? 1 : 0;
        $request['modulocontrol'] = $request->modulocontrol == 'on' ? 1 : 0;
        $request['modulocontable'] = $request->modulocontable == 'on' ? 1 : 0;
        $request['actulizaciones'] = $request->actulizaciones == 'on' ? 1 : 0;
        $request['tokenrespaldo'] =  $request['tokenrespaldo'] == "" ? "" : $request['tokenrespaldo'];
        $request['key'] =  $request['key'] == "" ? "" : $request['key'];

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
            'tienda_perseo_distribuidor' => $request['integraciones'] = $request->integraciones == 'on' ? true : false,
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
            $request['integraciones'],
            $request['hybrid'],
            $request['woocomerce'],
            $request['apiwhatsapp'],
            $request['cashmanager'],
            $request['equifax'],
        );
        $request['modulos'] = json_encode([$modulos]);
        $licencia->update($request->all());

        $log = new Log();
        $log->usuario = Auth::user()->nombres;
        $log->pantalla = "Licencia PC";
        $log->tipooperacion = "Modificar";
        $log->fecha = now();
        $log->detalle = $licencia;
        $log->save();

        flash('Actualizado Correctamente')->success();
        return back();
    }

    public function actualizarWeb(Request $request, $licenciaid)
    {

        $url = 'http://localhost:8026/registros/consulta_licencia';
        $licenciaConsulta = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->post($url, ['sis_licenciasid' => $licenciaid])
            ->json();
        $licenciaEnviar = $licenciaConsulta['licencias'][0];
        $licenciaArray = json_encode($licenciaEnviar);
        $licencia = json_decode($licenciaArray);
        //En caso de renovar mensual, anual o actualizar 
        switch ($request->tipo) {
            case 'mes':
                $request['fechacaduca'] = date("Ymd", strtotime($request->fechacaduca . "+ 1 month"));
                break;
            case 'anual':
                $request['fechacaduca'] = date("Ymd", strtotime($request->fechacaduca . "+ 1 year"));
                break;
            case 'recargar':
                $parametros_json = json_decode($licencia->parametros_json);
                $parametros_json->Documentos = $parametros_json->Documentos + 120;
                $request['parametros_json'] = json_encode($parametros_json);
                $request['fechacaduca'] = date('Ymd', strtotime($request->fechacaduca));
                break;
            default:
                $request['fechacaduca'] = date('Ymd', strtotime($request->fechacaduca));
                break;
        }

        //Asignacion masiva para los campos asignados en guarded o fillable en el modelo
        $request['fechamodificacion'] = date('YmdHis', strtotime(now()));
        $request['usuariomodificacion'] = Auth::user()->nombres;
        $request['fechainicia'] = date('Ymd', strtotime($request->fechainicia));


        $xw = xmlwriter_open_memory();
        xmlwriter_start_document($xw, '1.0', 'UTF-8');
        xmlwriter_start_element($xw, 'modulos');

        xmlwriter_start_element($xw, 'nomina');
        xmlwriter_text($xw, $request->nomina == 'on' ? 1 : 0);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'activos');
        xmlwriter_text($xw, $request->activos == 'on' ? 1 : 0);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'produccion');
        xmlwriter_text($xw, $request->produccion == 'on' ? 1 : 0);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'restaurantes');
        xmlwriter_text($xw, $request->restaurantes == 'on' ? 1 : 0);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'talleres');
        xmlwriter_text($xw, $request->talleres == 'on' ? 1 : 0);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'garantias');
        xmlwriter_text($xw, $request->garantias == 'on' ? 1 : 0);
        xmlwriter_end_element($xw);
        xmlwriter_start_element($xw, 'ecommerce');
        xmlwriter_text($xw, $request->ecommerce == 'on' ? 1 : 0);
        xmlwriter_end_element($xw);
        xmlwriter_end_element($xw);
        xmlwriter_end_document($xw);

        $request['modulos'] = xmlwriter_output_memory($xw);

        unset(
            $request['nomina'],
            $request['activos'],
            $request['produccion'],
            $request['restaurantes'],
            $request['talleres'],
            $request['garantias'],
            $request['ecommerce'],
            $request['tipo'],
        );


        /* $licencia->update($request->all()); */

        $request['sis_licenciasid'] = $licenciaid;



        $urlEditar = 'http://localhost:8026/registros/editar_licencia';
        $licenciaEditar = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->post($urlEditar, $request->all())
            ->json();

        if (isset($licenciaEditar['licencias'])) {
            $log = new Log();
            $log->usuario = Auth::user()->nombres;
            $log->pantalla = "Licencia Web";
            $log->tipooperacion = "Modificar";
            $log->fecha = now();
            $log->detalle = json_encode($request->all());
            $log->save();

            flash('Actualizado Correctamente')->success();
        } else {
            flash('Ocurrió un error vuelva a intentarlo')->warning();
        }

        return back();
    }

    public function eliminarPc(Licencias $licencia)
    {
        $licencia->delete();

        $log = new Log();
        $log->usuario = Auth::user()->nombres;
        $log->pantalla = "Licencia PC";
        $log->tipooperacion = "Eliminar";
        $log->fecha = now();
        $log->detalle = $licencia;
        $log->save();

        flash("Eliminado Correctamente")->success();
        return back();
    }

    public function eliminarWeb($licenciaid)
    {

        $url = 'http://localhost:8026/registros/eliminar_licencia';
        $urlConsulta = 'http://localhost:8026/registros/consulta_licencia';
        $licenciaConsulta = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->post($urlConsulta, ['sis_licenciasid' => $licenciaid])
            ->json();
        $eliminarLicencia = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->post($url, ['sis_licenciasid' => $licenciaid])
            ->json();

        if (isset($eliminarLicencia['respuesta'])) {
            $log = new Log();
            $log->usuario = Auth::user()->nombres;
            $log->pantalla = "Licencia Web";
            $log->tipooperacion = "Eliminar";
            $log->fecha = now();
            $log->detalle = json_encode($licenciaConsulta['licencias'][0]);
            $log->save();

            flash("Eliminado Correctamente")->success();
        } else {
            flash('Ocurrió un error vuelva a intentarlo')->warning();
        }

        return back();
    }
}
