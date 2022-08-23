<?php

namespace App\Http\Controllers;

use App\Mail\enviarlicencia;
use App\Models\Ciudades;
use App\Models\Clientes;
use App\Models\Licencias;
use App\Models\Log;
use App\Models\Servidores;
use App\Models\Subcategorias;
use App\Models\User;
use App\Rules\ValidarCelular;
use App\Rules\ValidarCorreo;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;
use AshAllenDesign\MailboxLayer\Facades\MailboxLayer;
use Illuminate\Support\Facades\Mail;

class adminController extends Controller
{

    public function login()
    {
        return view('admin.auth.login');
    }

    public function loginRedireccion()
    {
        return view('admin.auth.loginredireccion');
    }

    public function post_loginRedireccion(Request $request)
    {
        $identificacionIngresada = substr($request->identificacion, 0, 10);
        $servidores = Servidores::where('estado', 1)->get();
        $array = [];


        foreach ($servidores as  $servidor) {
            $urlUsuario = $servidor->dominio . '/registros/consulta_usuario';
            $url = $servidor->dominio . '/registros/consulta_licencia';

            $usuario = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                ->withOptions(["verify" => false])
                ->post($urlUsuario, ['identificacion' => $identificacionIngresada])
                ->json();


            if (isset($usuario['usuario'])) {
                $resultado = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                    ->withOptions(["verify" => false])
                    ->post($url, ['sis_clientesid' => $usuario['usuario'][0]['sis_clientesid']])
                    ->json();

                if (isset($resultado['licencias'])) {
                    $array[] = ["sis_servidoresid" => $servidor->sis_servidoresid, "descripcion" => $servidor->descripcion, "dominio" => $servidor->dominio . '/sistema?identificacion=' . $identificacionIngresada];
                }
            }
        }
        if (count($array) > 0) {
            return $array;
        } else {
            return 0;
        }
    }

    public function migrar()
    {
        $clientes = Clientes::all();
        $servidores = Servidores::all();
        return view('admin.migrar', compact('clientes', 'servidores'));
    }

    public function licencia($servidorid, $clienteid)
    {
        $servidor = Servidores::where('sis_servidoresid', $servidorid)->first();
        $url = $servidor->dominio . '/registros/consulta_licencia';
        $licencias = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->post($url, ['sis_clientesid' => $clienteid])
            ->json();

        if (isset($licencias["licencias"])) {
            foreach ($licencias["licencias"] as $key => $licencia) {
                switch ($licencia['producto']) {
                    case '2':
                        $producto = "Facturación";
                        break;
                    case '3':
                        $producto = "Servicios";
                        break;
                    case '4':
                        $producto = "Comercial";
                        break;
                    case '5':
                        $producto = "Soy Contador Comercial";
                        break;
                    case '6':
                        $producto = "Perseo Lite Anterior";
                        break;
                    case '7':
                        $producto = "Total";
                        break;
                    case '8':
                        $producto = "Soy Contador Servicios";
                        break;
                    case '9':
                        $producto = "Perseo Lite";
                        break;
                    case '10':
                        $producto = "Emprendedor";
                        break;
                    case '11':
                        $producto = "Socio Perseo";
                        break;
                }
                $licencias["licencias"][$key]['producto'] = $producto;
            }
        } else {
            $licencias = ['licencias' => [['sis_licenciasid' => 0, 'numerocontrato' => 'Cliente sin Licencia', 'producto' => '']]];
        }
        return with(["licencia" => $licencias["licencias"]]);
    }

    public function post_login(Request $request)
    {
        //Validacion
        $request->validate(
            [
                'identificacion' => 'required',
                'contrasena' => 'required',
            ],
            [
                'identificacion.required' => 'Ingrese su cédula o RUC ',
                'contrasena.required' => 'Ingrese su contraseña',
            ],
        );

        //Buscar usuario
        $usuario = User::where('identificacion', $request->identificacion)->first();
        if ($usuario) {
            if ($usuario->contrasena === encrypt_openssl($request->contrasena, "Perseo1232*")) {
                //Si tiene puesto check para recordar 
                if ($request->has('recordar')) {
                    Auth::login($usuario, true);
                } else {
                    Auth::login($usuario, false);
                }
                //Siempre que se hace login es recomendable regenerar las sesiones
                $request->session()->regenerate();
                return redirect()->route('clientes.index');
            } else {
                flash('Usuario o Contraseña Incorrectos')->error();
                return back();
            }
        } else {
            flash('Usuario o Contraseña Incorrectos')->error();
            return back();
        }
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::logout();
            return redirect()->route('login');
        } else {
            //Aqui va el logout del contador
        }
    }

    public function cambiarMenu(Request $request)
    {
        Session::put('menu', $request->estado);
    }

    public function subcategorias(Request $request)
    {
        if ($request->ajax()) {

            $data  = Subcategorias::select('sis_categorias.categoriasdescripcion', 'sis_subcategorias.sis_subcategoriasid', 'sis_subcategorias.sis_categoriasid', 'sis_subcategorias.descripcionsubcategoria')
                ->join('sis_categorias', 'sis_subcategorias.sis_categoriasid', 'sis_categorias.sis_categoriasid')
                ->orderBy('sis_subcategorias.sis_subcategoriasid');

            return DataTables::of($data)

                ->editColumn('activo', function ($data) {
                    return '<label class="checkbox checkbox-outline checkbox-success">
                        <input type="checkbox" name=aplicaciones[] id="' . $data->sis_subcategoriasid . '" disabled />
                        <span></span>
                    </label>';
                })
                ->rawColumns(['activo'])
                ->make(true);
        }
    }

    public function productos($tipo)
    {
        switch ($tipo) {
            case '2':
                $producto = [
                    [
                        "id" => "",
                        "nombre" => "Todos"
                    ], [
                        "id" => "2",
                        "nombre" => "Facturación"
                    ], [
                        "id" => "3",
                        "nombre" => "Servicios"
                    ], [
                        "id" => "4",
                        "nombre" => "Comercial"
                    ], [
                        "id" => "5",
                        "nombre" => "Soy Contador"
                    ], [
                        "id" => "7",
                        "nombre" => "Total"
                    ], [
                        "id" => "6",
                        "nombre" => "Perseo Lite Anterior"
                    ], [
                        "id" => "8",
                        "nombre" => "Soy Contador Servicios"
                    ], [
                        "id" => "9",
                        "nombre" => "Perseo Lite"
                    ], [
                        "id" => "10",
                        "nombre" => "Emprendedor"
                    ], [
                        "id" => "11",
                        "nombre" => "Socio Perseo"
                    ]
                ];
                break;
            case '3':
                $producto = [
                    [
                        "id" => "",
                        "nombre" => "Todos"
                    ], [
                        "id" => "1",
                        "nombre" => "Práctico"
                    ], [
                        "id" => "2",
                        "nombre" => "Control"
                    ], [
                        "id" => "3",
                        "nombre" => "Contable"
                    ]
                ];
                break;
            default:
                $producto = [
                    [
                        "id" => "",
                        "nombre" => "Todos"
                    ]
                ];
                break;
        }

        return with(["producto" => $producto]);
    }

    public function publicidad()
    {
        return view('admin.publicidad.crear');
    }

    public function publicidadGuardar(Request $request)
    {

        $ruta = public_path("assets/media/");

        if ($request->Hasfile('imagen')) {

            $imagen = $request->file("imagen");
            if (copy($imagen->getRealPath(), $ruta . 'perseo-inicio.jpg')) {

                flash('Imagen Guardada Correctamente')->success();
            } else {
                flash('Ocurrió un error, vuelva a intentarlo')->warning();
            }
        }

        if ($request->Hasfile('imagen-admin')) {
            $imagen = $request->file("imagen-admin");


            if (copy($imagen->getRealPath(), $ruta . 'perseo-admin.jpg')) {
                flash('Imagen Guardada Correctamente')->success();
            } else {
                flash('Ocurrió un error, vuelva a intentarlo')->warning();
            }
        }


        if ($request->Hasfile('imagen-registro')) {

            $imagen = $request->file("imagen-registro");


            if (copy($imagen->getRealPath(), $ruta . 'perseo-registro.jpg')) {

                flash('Imagen Guardada Correctamente')->success();
            } else {
                flash('Ocurrió un error, vuelva a intentarlo')->warning();
            }
        }
        return back();
    }

    public function registro()
    {
        $identificacion = 0;
        return view('admin.auth.registro', compact('identificacion'));
    }

    public function post_registro(Request $request)
    {
        $request->validate(
            [
                'identificacion' => ['required', 'unique:sis_clientes', 'size:13'],
                'nombres' => 'required',
                'direccion' => 'required',
                'correos' => ['required', 'email', new ValidarCorreo],
                'provinciasid' => 'required',
                'ciudadesid' => 'required',
                'telefono2' => ['required', new ValidarCelular],
                'grupo' => 'required'
            ],
            [
                'identificacion.required' => 'Ingrese su cédula o RUC ',
                'identificacion.unique' => 'Su RUC ya se encuentra registrado',
                'identificacion.size' => 'Ingrese 13 dígitos',
                'nombres.required' => 'Ingrese una Razón Social',
                'direccion.required' => 'Ingrese una Dirección',
                'correos.required' => 'Ingrese un Correo',
                'correos.email' => 'Ingrese un Correo Válido',
                'provinciasid.required' => 'Seleccione una Provincia',
                'ciudadesid.required' => 'Seleccione una Ciudad',
                'telefono2.required' => 'Ingrese Whatsapp',
                'grupo.required' => 'Seleccione un Tipo de Negocio'
            ],
        );

        //Asignacion masiva para los campos asignados en guarded o fillable en el modelo
        $request['fechacreacion'] = now();
        $request['usuariocreacion'] = "Perseo Lite";
        $request['tipoidentificacion'] = "R";
        $request['sis_distribuidoresid'] = $request['red_origen'] == 2 ? 1 : 6;
        $request['sis_revendedoresid'] = 1;
        $request['sis_vendedoresid'] = 405;
        $request['validado'] = 1;


        DB::beginTransaction();
        try {
            $servidores = Servidores::where('estado', 1)->get();

            $cliente =   Clientes::create($request->all());
            $log = new Log();
            $log->usuario = "Perseo Lite";
            $log->pantalla = "Clientes";
            $log->tipooperacion = "Crear";
            $log->fecha = now();
            $log->detalle = $cliente;
            $log->save();
            $request['sis_clientesid'] = $cliente->sis_clientesid;

            //Crear clientes en todos los servidores
            foreach ($servidores as $servidor) {
                $url = $servidor->dominio . '/registros/crear_clientes';
                $crearCliente = Http::withHeaders(['Content-Type' => 'application/json; ', 'verify' => false])
                    ->withOptions(["verify" => false])
                    ->post($url, $request->all())
                    ->json();
                if (!isset($crearCliente['sis_clientes'])) {
                    DB::rollBack();
                    flash('Ocurrió un error vuelva a intentarlo')->warning();
                    return back();
                }
            }

            //Verificar que no se exista el numero de contrato
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

            //Registrar licencia en el servidor lite
            $url = 'https://perseo-data-c3.app/registros/crear_licencias';
            $licencia = [
                "sis_servidoresid" => 3,
                "sis_clientesid" => $cliente->sis_clientesid,
                "sis_distribuidoresid" => $cliente->sis_distribuidoresid,
                "tipo_licencia" => 1,
                "numerocontrato" => $contrato,
                "Identificador" => $contrato,
                "fechainicia" => date('Ymd', strtotime(now())),
                "fechacaduca" => date("Ymd", strtotime(date("Ymd") . "+ 1 year")),
                "empresas" => 1,
                "usuarios" => 6,
                "periodo" => 1,
                "producto" => 9,
                "estado" => 1,
                "precio" => 0,
                "numeromoviles" => 1,
                "fechaultimopago" => date('Ymd', strtotime(now())),
                "fechacreacion" => now(),
                "modulos" => '<modulos><nomina>1</nomina><activos>1</activos><produccion>1</produccion><restaurantes>1</restaurantes><talleres>1</talleres><garantias>1</garantias><ecommerce>1</ecommerce></modulos>',
                "parametros_json" => json_encode($parametros_json),
            ];
            $crearLicenciaWeb = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
                ->withOptions(["verify" => false])
                ->post($url, $licencia)
                ->json();

            if (isset($crearLicenciaWeb["licencias"])) {
                DB::commit();

                $array['view'] = 'emails.registro_demos';
                $array['from'] = env('MAIL_FROM_ADDRESS');
                $array['subject'] = 'Registro Demo';
                $array['nombre'] =  $cliente->nombres;
                $array['usuario'] =  substr($cliente->identificacion, 0, 10);
                $array['clave'] = '123';
                $array['tipo'] = '6';

                try {
                    Mail::to($cliente->correos)->queue(new enviarlicencia($array));
                } catch (\Exception $e) {
                    flash('Error enviando email')->error();
                    return back();
                }

                flash('Registrado Correctamente')->success();
                return view('admin.auth.registro')->with(['identificacion' => substr($cliente->identificacion, 0, 10)]);
            } else {
                DB::rollBack();
                flash('Ocurrió un error vuelva a intentarlo')->warning();
                return back();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            flash('Ocurrió un error vuelva a intentarlo')->warning();
            return back();
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

    public function recuperarciudades(Request $request)
    {
        $ciudades = Ciudades::where('ciudadesid', 'like', $request->id . '%')->get();
        return $ciudades;
    }
}
