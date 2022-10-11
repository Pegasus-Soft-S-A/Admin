<?php

namespace App\Http\Controllers;

use App\Mail\enviarlicencia;
use App\Models\Agrupados;
use App\Models\Licencias;
use App\Models\Clientes;
use App\Models\Log;
use App\Models\Servidores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class licenciasController extends Controller
{

    public function index(Request $request, Clientes $cliente)
    {

        if ($request->ajax()) {

            $servidores = Servidores::where('estado', 1)->get();
            $web = [];

            foreach ($servidores as  $servidor) {
                $url = $servidor->dominio . '/registros/consulta_licencia';
                $resultado = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                    ->withOptions(["verify" => false])
                    ->post($url, ['sis_clientesid' => $cliente->sis_clientesid])
                    ->json();
                if (isset($resultado['licencias'])) {
                    $web = array_merge($web, $resultado['licencias']);
                }
            }

            $data = Licencias::select('sis_licenciasid', 'numerocontrato', 'tipo_licencia', 'fechacaduca', 'sis_clientesid', 'sis_servidoresid')
                ->where('sis_clientesid', $cliente->sis_clientesid)
                ->get();

            if ($web) {
                $unir = array_merge($web, $data->toArray());
            } else {
                $unir =  $data->toArray();
            }

            return DataTables::of($unir)

                ->editColumn('numerocontrato', function ($data) {
                    if ($data['tipo_licencia'] == 1) {
                        return '<a class="text-primary" href="' . route('licencias.Web.editar', [$data['sis_clientesid'], $data['sis_servidoresid'], $data['sis_licenciasid']]) . '">' . $data['numerocontrato'] . ' </a>';
                    } else {
                        return '<a class="text-primary" href="' . route('licencias.Pc.editar', [$data['sis_clientesid'], $data['sis_licenciasid']]) . '">' . $data['numerocontrato'] . ' </a>';
                    }
                })
                ->editColumn('action', function ($data) {
                    if ($data['tipo_licencia'] == 1) {
                        if (Auth::user()->tipo == 1) {
                            return '<a class="btn btn-icon btn-light btn-hover-primary btn-sm mr-2 actividad" href="javascript:void(0)" data-href="' . route('licencias.actividad', [$data['sis_servidoresid'], $data['sis_licenciasid']]) . '" title="Actividad"> <i class="la la-eye"></i> </a>' .
                                '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('licencias.Web.editar', [$data['sis_clientesid'], $data['sis_servidoresid'], $data['sis_licenciasid']]) . '" title="Editar"> <i class="la la-edit"></i> </a>' .
                                '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-2 confirm-delete" href="javascript:void(0)" data-href="' . route('licencias.Web.eliminar',  [$data['sis_servidoresid'], $data['sis_licenciasid']]) . '" title="Eliminar"> <i class="la la-trash"></i> </a>';
                        } else {
                            return '<a class="btn btn-icon btn-light btn-hover-primary btn-sm mr-2 actividad" href="javascript:void(0)" data-href="' . route('licencias.actividad', [$data['sis_servidoresid'], $data['sis_licenciasid']]) . '" title="Actividad"> <i class="la la-eye"></i> </a>' .
                                '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('licencias.Web.editar', [$data['sis_clientesid'], $data['sis_servidoresid'], $data['sis_licenciasid']]) . '" title="Editar"> <i class="la la-edit"></i> </a>';
                        }
                    } else {
                        if (Auth::user()->tipo == 1) {
                            return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('licencias.Pc.editar', [$data['sis_clientesid'], $data['sis_licenciasid']]) . '" title="Editar"> <i class="la la-edit"></i> </a>' .
                                '<a class="btn btn-icon btn-light btn-hover-danger btn-sm mr-2 confirm-delete" href="javascript:void(0)" data-href="' . route('licencias.Pc.eliminar', $data['sis_licenciasid']) . '" title="Eliminar"> <i class="la la-trash"></i> </a>';
                        } else {
                            return '<a class="btn btn-icon btn-light btn-hover-success btn-sm mr-2" href="' . route('licencias.Pc.editar', [$data['sis_clientesid'], $data['sis_licenciasid']]) . '" title="Editar"> <i class="la la-edit"></i> </a>';
                        }
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
            $numero = rand(1, 9);
            $randomString = $randomString . $numero;
        }

        return $randomString;
    }

    public function crearWeb(Clientes $cliente)
    {
        $licencia = new Licencias();
        $agrupados = Agrupados::select('sis_agrupados.sis_agrupadosid', 'sis_clientes.nombres', 'sis_agrupados.codigo')
            ->join('sis_clientes', 'sis_clientes.sis_clientesid', 'sis_agrupados.sis_clientesid')
            ->get();
        $servidores = Servidores::where('estado', 1)->get();
        $contrato = $this->generarContrato();
        $existepc = Licencias::where('numerocontrato', $contrato)->get();
        $existeweb = [];

        foreach ($servidores as $servidor) {
            $url = $servidor->dominio . '/registros/consulta_licencia';
            $existe = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                ->withOptions(["verify" => false])
                ->post($url, ['numerocontrato' => $contrato])
                ->json();
            if (isset($existe['licencias'])) {
                $existeweb = array_merge($existeweb, $existe['licencias']);
            }
        }

        //Mientras exista en la base el numero de contrato seguira generando hasta que sea unico
        while (count($existepc) > 0 || count($existeweb) > 0) {
            $contrato = $this->generarContrato();
            $existe = Licencias::where('numerocontrato', $contrato)->get();

            foreach ($servidores as $servidor) {
                $url = $servidor->dominio . '/registros/consulta_licencia';
                $existe = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                    ->withOptions(["verify" => false])
                    ->post($url, ['numerocontrato' => $contrato])
                    ->json();
                if (isset($existe['licencias'])) {
                    $existeweb = array_merge($existeweb, $existe['licencias']);
                }
            }
        }

        $licencia->numerocontrato = $contrato;
        $licencia->numerosucursales = 0;
        $licencia->empresas = 1;
        $licencia->sis_distribuidoresid = Auth::user()->sis_distribuidoresid;

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

        return view('admin.licencias.Web.crear', compact('cliente', 'licencia', 'modulos', 'servidores', 'agrupados'));
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
        $licencia->aplicaciones = " s";
        $licencia->fechaactulizaciones = date("d-m-Y", strtotime(date("d-m-Y") . "+ 1 month"));
        $licencia->sis_distribuidoresid = Auth::user()->sis_distribuidoresid;

        $contrato = $this->generarContrato();
        $existe = Licencias::where('numerocontrato', $contrato)->get();

        $url = 'https://perseo-data-c1.app/registros/consulta_licencia';
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

        return view('admin.licencias.PC.crear', compact('cliente', 'licencia', 'modulos'));
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
                'correopropietario.email' => 'Ingrese un Correo de Propietario v�lido',
                'correoadministrador.required' => 'Ingrese un Correo de Administrador',
                'correoadministrador.email' => 'Ingrese un Correo de adminisrador v�lido',
                'correocontador.required' => 'Ingrese un Correo de Contador',
                'correocontador.email' => 'Ingrese un Correo de Contador v�lido',
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
        $request['ipservidorremoto'] = $request->ipservidorremoto == '' ? '' : $request->ipservidorremoto;
        $request['motivobloqueo'] = $request->motivobloqueo == '' ? '' : $request->motivobloqueo;
        $request['mensaje'] = $request->mensaje == '' ? '' : $request->mensaje;
        $request['observacion'] = $request->observacion == '' ? '' : $request->observacion;
        $request['ipservidorremoto'] = $request->ipservidorremoto == '' ? '' : $request->ipservidorremoto;
        $request['numerogratis'] =  0;
        $request['tokenrespaldo'] =  $request['tokenrespaldo'] == "" ? "" : $request['tokenrespaldo'];
        $request['tipo_licencia'] =  2;

        $modulos = [];
        $modulos = [
            'nomina' => $request['nomina'] = $request->nomina == 'on' ? true : false,
            'activos' => $request['activos'] = $request->activos == 'on' ? true : false,
            'produccion' => $request['produccion'] = $request->produccion == 'on' ? true : false,
            'restaurante' => $request['restaurante'] = $request->restaurante == 'on' ? true : false,
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
            'cash_debito' => $request['cashdebito'] = $request->cashdebito == 'on' ? true : false,
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
            $request['cashdebito'],
            $request['equifax'],
            $request['tipo'],
        );
        $request['modulos'] = json_encode([$modulos]);
        $servidor = Servidores::where('estado', 1)->first();
        $urlLicencia = $servidor->dominio . '/registros/generador_licencia';

        $urlLicencia = Http::withHeaders(['Content-Type' => 'application/json; ', 'verify' => false])
            ->withOptions(["verify" => false])
            ->post($urlLicencia, $request->all())
            ->json();

        $request["key"] = $urlLicencia['licencia'];

        $licencia =   Licencias::create($request->all());

        $cliente = Clientes::select('sis_clientes.correos', 'sis_distribuidores.correos AS distribuidor', 'sis_clientes.nombres', 'sis_clientes.identificacion', 'sis_revendedores.correo AS vendedor')
            ->join('sis_distribuidores', 'sis_distribuidores.sis_distribuidoresid', 'sis_clientes.sis_distribuidoresid')
            ->join('sis_revendedores', 'sis_revendedores.sis_revendedoresid', 'sis_clientes.sis_vendedoresid')
            ->where('sis_clientesid', $licencia->sis_clientesid)
            ->first();

        $array['view'] = 'emails.licenciapc';
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['subject'] = 'Nuevo Registro Licencia Pc';
        $array['cliente'] =  $cliente->nombres;
        $array['identificacion'] = $cliente->identificacion;
        $array['correo'] = $cliente->correos;
        $array['numerocontrato'] = $licencia->numerocontrato;
        $array['identificador'] = $licencia->Identificador;
        $array['modulopractico'] = $licencia->modulopractico;
        $array['modulocontable'] = $licencia->modulocontable;
        $array['modulocontrol'] = $licencia->modulocontrol;
        $array['ipservidor'] = $licencia->ipservidor;
        $array['ipservidorremoto'] = $licencia->ipservidorremoto;
        $array['numeroequipos'] = $licencia->numeroequipos;
        $array['numeromoviles'] = $licencia->numeromoviles;
        $array['numerosucursales'] = $licencia->numerosucursales;
        $array['modulos'] = json_decode($licencia->modulos);
        $array['usuario'] = Auth::user()->nombres;
        $array['fecha'] = $request['fechacreacion'];
        $array['tipo'] = '2';

        $emails = explode(", ", $cliente->distribuidor);

        $emails = array_merge($emails,  [
            "comercializacion@perseo.ec",
            "facturacion@perseo.ec",
            $cliente->vendedor,
            Auth::user()->correo,
        ]);

        $emails = array_diff($emails, array(" ", 0, null));

        $log = new Log();
        $log->usuario = Auth::user()->nombres;
        $log->pantalla = "Licencia PC";
        $log->tipooperacion = "Crear";
        $log->fecha = now();
        $log->detalle = $licencia;
        $log->save();

        try {
            Mail::to($emails)->queue(new enviarlicencia($array));
        } catch (\Exception $e) {

            flash('Error enviando email')->error();
            return redirect()->route('licencias.Pc.editar', [$request['sis_clientesid'], $licencia->sis_licenciasid]);
        }

        flash('Guardado Correctamente')->success();
        return redirect()->route('licencias.Pc.editar', [$request['sis_clientesid'], $licencia->sis_licenciasid]);
    }

    public function guardarWeb(Request $request)
    {
        //Asignacion masiva para los campos asignados en guarded o fillable en el modelo
        $request['fechacreacion'] = date('Y-m-d H:i:s', strtotime(now()));
        $request['usuariocreacion'] = Auth::user()->nombres;
        $request['fechainicia'] = date('Ymd', strtotime($request->fechainicia));
        $request['fechacaduca'] = date('Ymd', strtotime($request->fechacaduca));
        $request['tipo_licencia'] =  1;
        $request['Identificador'] = $request['numerocontrato'];
        $request['fechaultimopago'] = $request['fechainicia'];

        switch ($request['producto']) {
                //Lite anterior
            case '6':
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
                break;
                //Lite
            case '9':
                $parametros_json = [];
                $parametros_json = [
                    'Documentos' => "30",
                    'Productos' => "100",
                    'Almacenes' => "1",
                    'Nomina' => "3",
                    'Produccion' => "3",
                    'Activos' => "3",
                    'Talleres' => "3",
                    'Garantias' => "3",
                ];
                $request['parametros_json'] = json_encode($parametros_json);
                break;
                //Emprendedor
            case '10':
                $parametros_json = [];
                $parametros_json = [
                    'Documentos' => "120",
                    'Productos' => "0",
                    'Almacenes' => "0",
                    'Nomina' => "0",
                    'Produccion' => "0",
                    'Activos' => "0",
                    'Talleres' => "0",
                    'Garantias' => "0",
                ];
                $request['parametros_json'] = json_encode($parametros_json);
                break;
                //Socio
            case '11':
                $parametros_json = [];
                $parametros_json = [
                    'Documentos' => "5",
                    'Productos' => "0",
                    'Almacenes' => "0",
                    'Nomina' => "0",
                    'Produccion' => "0",
                    'Activos' => "0",
                    'Talleres' => "0",
                    'Garantias' => "0",
                ];
                $request['parametros_json'] = json_encode($parametros_json);
                break;
            case '12':
                switch ($request->periodo) {
                        //Inicial
                    case '1':
                        $parametros_json = [];
                        $parametros_json = [
                            'Documentos' => "30",
                            'Productos' => "0",
                            'Almacenes' => "0",
                            'Nomina' => "0",
                            'Produccion' => "0",
                            'Activos' => "0",
                            'Talleres' => "0",
                            'Garantias' => "0",
                        ];
                        $request['parametros_json'] = json_encode($parametros_json);
                        break;
                        //Basico
                    case '2':
                        $parametros_json = [];
                        $parametros_json = [
                            'Documentos' => "100",
                            'Productos' => "0",
                            'Almacenes' => "0",
                            'Nomina' => "0",
                            'Produccion' => "0",
                            'Activos' => "0",
                            'Talleres' => "0",
                            'Garantias' => "0",
                        ];
                        $request['parametros_json'] = json_encode($parametros_json);
                        break;
                }
        }

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

        $servidor = Servidores::where('sis_servidoresid', $request->sis_servidoresid)->first();
        $url = $servidor->dominio . '/registros/crear_licencias';
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

            $cliente = Clientes::select('sis_clientes.correos', 'sis_clientes.nombres', 'sis_clientes.identificacion', 'sis_distribuidores.correos AS distribuidor', 'sis_revendedores.correo AS vendedor', 'revendedor.correo AS revendedor')
                ->join('sis_distribuidores', 'sis_distribuidores.sis_distribuidoresid', 'sis_clientes.sis_distribuidoresid')
                ->join('sis_revendedores', 'sis_revendedores.sis_revendedoresid', 'sis_clientes.sis_vendedoresid')
                ->join('sis_revendedores as revendedor', 'revendedor.sis_revendedoresid', 'sis_clientes.sis_vendedoresid')
                ->where('sis_clientesid', $request['sis_clientesid'])
                ->first();

            $array['view'] = 'emails.licenciaweb';
            $array['from'] = env('MAIL_FROM_ADDRESS');
            $array['subject'] = 'Nuevo Registro Licencia Web';
            $array['cliente'] = $cliente->nombres;
            $array['identificacion'] = $cliente->identificacion;
            $array['correos'] = $cliente->correos;
            $array['numerocontrato'] = $licencia['numerocontrato'];
            $array['producto'] = $licencia['producto'];
            $array['periodo'] = $licencia['periodo'] == 1 ? 'Mensual' : 'Anual';
            $array['fechainicia'] = date("d-m-Y", strtotime($licencia['fechainicia']));
            $array['fechacaduca'] =  date("d-m-Y", strtotime($licencia['fechacaduca']));
            $array['empresas'] = $licencia['empresas'];
            $array['numeromoviles'] = $licencia['numeromoviles'];
            $array['usuarios'] = $licencia['usuarios'];
            $transformar = simplexml_load_string($licencia['modulos']);
            $json = json_encode($transformar);
            $array['modulos'] = json_decode($json);
            $array['usuario'] = Auth::user()->nombres;
            $array['fecha'] = $licencia['fechacreacion'];
            $array['tipo'] = '1';

            $emails = explode(", ", $cliente->distribuidor);

            $emails = array_merge($emails,  [
                "comercializacion@perseo.ec",
                $cliente->vendedor,
                $cliente->revendedor,
                $cliente->cliente,
                Auth::user()->correo,
            ]);

            $emails = array_diff($emails, array(" ", 0, null));

            try {
                Mail::to($emails)->queue(new enviarlicencia($array));
            } catch (\Exception $e) {

                flash('Error enviando email')->error();
                return redirect()->route('licencias.Web.editar', [$request['sis_clientesid'], $request->sis_servidoresid, $licenciaId]);
            }

            flash('Guardado Correctamente')->success();
            return redirect()->route('licencias.Web.editar', [$request['sis_clientesid'], $request->sis_servidoresid, $licenciaId]);
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
        return view('admin.licencias.PC.editar', compact('cliente', 'licencia', 'modulos'));
    }

    public function editarWeb(Clientes $cliente, $servidorid, $licenciaid)
    {
        $agrupados = Agrupados::select('sis_agrupados.sis_agrupadosid', 'sis_clientes.nombres', 'sis_agrupados.codigo')
            ->join('sis_clientes', 'sis_clientes.sis_clientesid', 'sis_agrupados.sis_clientesid')
            ->get();
        $servidor = Servidores::where('sis_servidoresid', $servidorid)->first();
        $url = $servidor->dominio . '/registros/consulta_licencia';
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
        $servidores = Servidores::all();

        return view('admin.licencias.Web.editar', compact('cliente', 'licencia', 'modulos', 'servidores', 'agrupados'));
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
                'correopropietario.email' => 'Ingrese un Correo de Propietario v�lido',
                'correoadministrador.required' => 'Ingrese un Correo de Administrador',
                'correoadministrador.email' => 'Ingrese un Correo de adminisrador v�lido',
                'correocontador.required' => 'Ingrese un Correo de Contador',
                'correocontador.email' => 'Ingrese un Correo de Contador v�lido',
            ],
        );

        //En caso de renovar mensual, anual o actualizar 
        switch ($request->tipo) {
            case 'mes':
                $request['fechacaduca'] = date("Ymd", strtotime($request->fechacaduca . "+ 1 month"));
                $request['fechaactulizaciones'] = date('Ymd', strtotime($request->fechaactulizaciones . "+ 1 month"));
                $asunto = "Renovacion Mensual Perseo PC";
                break;
            case 'anual':
                $request['fechacaduca'] = date("Ymd", strtotime($request->fechacaduca . "+ 1 year"));
                $request['fechaactulizaciones'] = date('Y-m-d', strtotime($request->fechaactulizaciones));
                $asunto = "Renovacion Anual Perseo PC";
                break;
            case 'actualizacion':
                $request['fechacaduca'] = date("Ymd", strtotime($request->fechacaduca));
                $request['fechaactulizaciones'] = date('Y-m-d', strtotime($request->fechaactulizaciones . "+ 1 year"));
                $asunto = "Actualizacion Anual Perseo PC";
                break;
            default:
                $request['fechacaduca'] = date('Ymd', strtotime($request->fechacaduca));
                $request['fechaactulizaciones'] = date('Y-m-d', strtotime($request->fechaactulizaciones));
                $asunto = "Modificacion Registro Licencia Pc";
                break;
        }

        //Asignacion masiva para los campos asignados en guarded o fillable en el modelo
        $request['fechamodificacion'] = now();
        $request['usuariomodificacion'] = Auth::user()->nombres;

        $request['modulopractico'] = $request->modulopractico == 'on' ? 1 : 0;
        $request['modulocontrol'] = $request->modulocontrol == 'on' ? 1 : 0;
        $request['modulocontable'] = $request->modulocontable == 'on' ? 1 : 0;
        $request['actulizaciones'] = $request->actulizaciones == 'on' ? 1 : 0;
        $request['tokenrespaldo'] =  $request['tokenrespaldo'] == "" ? "" : $request['tokenrespaldo'];
        $request['ipservidorremoto'] = $request->ipservidorremoto == '' ? '' : $request->ipservidorremoto;
        $request['motivobloqueo'] = $request->motivobloqueo == '' ? '' : $request->motivobloqueo;
        $request['mensaje'] = $request->mensaje == '' ? '' : $request->mensaje;
        $request['observacion'] = $request->observacion == '' ? '' : $request->observacion;


        $modulos = [];
        $modulos = [
            'nomina' => $request['nomina'] = $request->nomina == 'on' ? true : false,
            'activos' => $request['activos'] = $request->activos == 'on' ? true : false,
            'produccion' => $request['produccion'] = $request->produccion == 'on' ? true : false,
            'restaurante' => $request['restaurante'] = $request->restaurante == 'on' ? true : false,
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
            'cash_debito' => $request['cashdebito'] = $request->cashdebito == 'on' ? true : false,
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
            $request['cashdebito'],
            $request['equifax'],
            $request['tipo'],
        );
        $request['modulos'] = json_encode([$modulos]);

        $urlLicencia = 'https://perseo-data-c1.app/registros/generador_licencia';

        $urlLicencia = Http::withHeaders(['Content-Type' => 'application/json; ', 'verify' => false])
            ->withOptions(["verify" => false])
            ->post($urlLicencia, $request->all())
            ->json();

        $request["key"] = $urlLicencia['licencia'];

        $licencia->update($request->all());

        $log = new Log();
        $log->usuario = Auth::user()->nombres;
        $log->pantalla = "Licencia PC";
        $log->tipooperacion = "Modificar";
        $log->fecha = now();
        $log->detalle = $licencia;
        $log->save();

        $cliente = Clientes::select('sis_clientes.correos', 'sis_distribuidores.correos AS distribuidor', 'sis_clientes.nombres', 'sis_clientes.identificacion', 'sis_revendedores.correo AS vendedor')
            ->join('sis_distribuidores', 'sis_distribuidores.sis_distribuidoresid', 'sis_clientes.sis_distribuidoresid')
            ->join('sis_revendedores', 'sis_revendedores.sis_revendedoresid', 'sis_clientes.sis_vendedoresid')
            ->where('sis_clientesid', $licencia->sis_clientesid)
            ->first();

        $array['view'] = 'emails.licenciapc';
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['subject'] = $asunto;
        $array['cliente'] =  $cliente->nombres;
        $array['identificacion'] = $cliente->identificacion;
        $array['correo'] = $cliente->correos;
        $array['numerocontrato'] = $licencia->numerocontrato;
        $array['identificador'] = $licencia->Identificador;
        $array['ipservidor'] = $licencia->ipservidor;
        $array['ipservidorremoto'] = $licencia->ipservidorremoto;
        $array['numeroequipos'] = $licencia->numeroequipos;
        $array['numeromoviles'] = $licencia->numeromoviles;
        $array['numerosucursales'] = $licencia->numerosucursales;
        $array['modulos'] = json_decode($licencia->modulos);
        $array['usuario'] = Auth::user()->nombres;
        $array['fecha'] = $request['fechamodificacion'];
        $array['tipo'] = '4';
        $array['modulopractico'] = $licencia->modulopractico;
        $array['modulocontable'] = $licencia->modulocontable;
        $array['modulocontrol'] = $licencia->modulocontrol;
        $array['fechaactulizaciones'] = $licencia->fechaactulizaciones;

        $emails = explode(", ", $cliente->distribuidor);

        $emails = array_merge($emails,  [
            "comercializacion@perseo.ec",
            "facturacion@perseo.ec",
            $cliente->vendedor,
            Auth::user()->correo,
        ]);

        $emails = array_diff($emails, array(" ", 0, null));

        try {
            Mail::to($emails)->queue(new enviarlicencia($array));
        } catch (\Exception $e) {
            dd($e);
            flash('Error enviando email')->error();
            return back();
        }

        flash('Actualizado Correctamente')->success();
        return back();
    }

    public function actualizarWeb(Request $request, $servidorid, $licenciaid)
    {
        $servidor = Servidores::where('sis_servidoresid', $servidorid)->first();
        $url = $servidor->dominio . '/registros/consulta_licencia';
        $licenciaConsulta = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->post($url, ['sis_licenciasid' => $licenciaid])
            ->json();
        $licenciaEnviar = $licenciaConsulta['licencias'][0];
        $licenciaArray = json_encode($licenciaEnviar);
        $licencia = json_decode($licenciaArray);
        $parametros_json = json_decode($licencia->parametros_json);
        //En caso de renovar mensual, anual o actualizar 
        switch ($request->tipo) {
            case 'mes':
                $request['fechacaduca'] = date("Ymd", strtotime($request->fechacaduca . "+ 1 month"));
                $asunto = 'Renovacion Mensual Licencia Web';
                $request['periodo'] = 1;
                break;
            case 'anual':
                $request['fechacaduca'] = date("Ymd", strtotime($request->fechacaduca . "+ 1 year"));
                $asunto = 'Renovacion Anual Licencia Web';
                $request['periodo'] = 2;
                break;
            case 'recargar':
                //$parametros_json = json_decode($licencia->parametros_json);
                $parametros_json->Documentos = $parametros_json->Documentos + 120;
                //$request['parametros_json'] = json_encode($parametros_json);
                $request['fechacaduca'] = date('Ymd', strtotime($request->fechacaduca));
                $asunto = $licencia->producto == 9 ? 'Recarga 120 Documentos Perseo Web Lite ' : 'Recarga 120 Documentos Perseo Web Emprendedor';
                break;
            case 'recargar240':
                //$parametros_json = json_decode($licencia->parametros_json);
                $parametros_json->Documentos = $parametros_json->Documentos + 240;
                //$request['parametros_json'] = json_encode($parametros_json);
                $request['fechacaduca'] = date('Ymd', strtotime($request->fechacaduca));
                $asunto =  'Recarga 240 Documentos Perseo Web Emprendedor';
                break;
            default:
                $request['fechacaduca'] = date('Ymd', strtotime($request->fechacaduca));
                $asunto = 'Modificar Licencia Web';
                break;
        }

        //Asignacion masiva para los campos asignados en guarded o fillable en el modelo
        $request['fechamodificacion'] = date('YmdHis', strtotime(now()));
        $request['usuariomodificacion'] = Auth::user()->nombres;
        $request['fechainicia'] = date('Ymd', strtotime($request->fechainicia));
        $request['parametros_json'] = json_encode($parametros_json);


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

        $request['sis_licenciasid'] = $licenciaid;

        $urlEditar = $servidor->dominio . '/registros/editar_licencia';
        $licenciaEditar = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->post($urlEditar, $request->all())
            ->json();

        $cliente = Clientes::select('sis_clientes.correos', 'sis_clientes.nombres', 'sis_clientes.identificacion', 'sis_distribuidores.correos AS distribuidor', 'sis_revendedores.correo AS vendedor', 'revendedor.correo AS revendedor', 'sis_revendedores.razonsocial')
            ->join('sis_distribuidores', 'sis_distribuidores.sis_distribuidoresid', 'sis_clientes.sis_distribuidoresid')
            ->join('sis_revendedores', 'sis_revendedores.sis_revendedoresid', 'sis_clientes.sis_vendedoresid')
            ->join('sis_revendedores as revendedor', 'revendedor.sis_revendedoresid', 'sis_clientes.sis_vendedoresid')
            ->where('sis_clientesid', $request['sis_clientesid'])
            ->first();

        if (isset($licenciaEditar['licencias'])) {
            $log = new Log();
            $log->usuario = Auth::user()->nombres;
            $log->pantalla = "Licencia Web";
            $log->tipooperacion = "Modificar";
            $log->fecha = now();
            $log->detalle = json_encode($request->all());
            $log->save();

            $array['view'] = 'emails.licenciaweb';
            $array['from'] = env('MAIL_FROM_ADDRESS');
            $array['subject'] = $asunto;
            $array['cliente'] = $cliente->nombres;
            $array['vendedor'] = $cliente->razonsocial;
            $array['identificacion'] = $cliente->identificacion;
            $array['correos'] = $cliente->correos;
            $array['numerocontrato'] = $request['numerocontrato'];
            $array['producto'] = $request['producto'];
            $array['periodo'] = $request['periodo'] == 1 ? 'Mensual' : 'Anual';
            $array['fechainicia'] = date("d-m-Y", strtotime($request['fechainicia']));
            $array['fechacaduca'] =  date("d-m-Y", strtotime($request['fechacaduca']));
            $array['empresas'] = $request['empresas'];
            $array['numeromoviles'] = $request['numeromoviles'];
            $array['usuarios'] = $request['usuarios'];
            $transformar = simplexml_load_string($request['modulos']);
            $json = json_encode($transformar);
            $array['modulos'] = json_decode($json);
            $array['usuario'] = Auth::user()->nombres;
            $array['fecha'] =  date("Y-m-d H:i:s", strtotime($request['fechamodificacion']));
            $array['tipo'] = '3';

            $emails = explode(", ", $cliente->distribuidor);

            $emails = array_merge($emails,  [
                "comercializacion@perseo.ec",
                "facturacion@perseo.ec",
                $cliente->vendedor,
                $cliente->revendedor,
                $cliente->cliente,
                Auth::user()->correo,
            ]);

            $emails = array_diff($emails, array(" ", 0, null));

            try {
                Mail::to($emails)->queue(new enviarlicencia($array));
            } catch (\Exception $e) {
                flash('Error enviando email')->error();
                return back();
            }

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

    public function eliminarWeb($servidorid, $licenciaid)
    {
        $servidor = Servidores::where('sis_servidoresid', $servidorid)->first();
        $url = $servidor->dominio . '/registros/eliminar_licencia';
        $urlConsulta = $servidor->dominio . '/registros/consulta_licencia';
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

    public function enviarEmail($clienteId)
    {
        $cliente = Clientes::select('sis_clientes.nombres', 'sis_clientes.identificacion', 'sis_clientes.correos', 'sis_distribuidores.correos as distribuidor')
            ->join('sis_distribuidores', 'sis_distribuidores.sis_distribuidoresid', 'sis_clientes.sis_distribuidoresid')
            ->where('sis_clientesid', $clienteId)
            ->first();

        if ($cliente != null) {
            $array['view'] = 'emails.envio_credenciales';
            $array['from'] = env('MAIL_FROM_ADDRESS');
            $array['subject'] = 'Envio Credenciales ';
            $array['nombre'] = $cliente->nombres;
            $array['usuario'] = $cliente->identificacion;
            $array['tipo'] = 5;

            $emails = explode(", ", $cliente->distribuidor);
            array_push($emails, $cliente->correos);
            $emails = array_diff($emails, array(" ", 0, null));

            try {
                Mail::to($emails)->queue(new enviarlicencia($array));
            } catch (\Exception $e) {

                flash('Error enviando email')->error();
                return back();
            }

            flash('Correo Enviado Correctamente')->success();
            return back();
        }
    }

    public function actividad($servidorid, $licenciaid)
    {
        $servidor = Servidores::where('sis_servidoresid', $servidorid)->first();

        $url = $servidor->dominio . '/registros/consulta_actividades';
        $resultado = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->post($url, ['sis_licenciasid' => $licenciaid])
            ->json();
        if (isset($resultado['actividades'])) {
            return with($resultado);
        } else {
            return with(["actividades" => []]);
        }
    }

    public function editarClave(Clientes $cliente, Servidores $servidor, $licenciaid)
    {
        $url = $servidor->dominio . '/registros/restaurar_clave_usuario';
        $usuario = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->post($url, ['sis_licenciasid' => $licenciaid, 'identificacion' => substr($cliente->identificacion, 0, 10)])
            ->json();
        if (isset($usuario['respuesta'])) {
            $resultado = ['mensaje' => "Clave Reseteada Correctamente", "tipo" => 'success'];
        } else {
            $resultado = ['mensaje' => $usuario['fault']['detail'], "tipo" => 'warning'];
        }
        return $resultado;
    }
}
