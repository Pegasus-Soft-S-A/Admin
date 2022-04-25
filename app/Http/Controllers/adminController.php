<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Servidores;
use App\Models\Subcategorias;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

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
            $url = $servidor->dominio . '/registros/consulta_licencia';

            $urlUsuario = $servidor->dominio . '/registros/consulta_usuario';

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
                        $producto = "Perseo Lite";
                        break;
                    case '7':
                        $producto = "Total";
                        break;
                    case '8':
                        $producto = "Soy Contador Servicios";
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
                        "nombre" => "Perseo Lite"
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
        $request->validate(
            [
                'imagen' => 'required',
            ],
            [
                'imagen.required' => 'Ingrese una imagen ',
            ],
        );


        if ($request->Hasfile('imagen')) {

            $imagen = $request->file("imagen");
            $ruta = public_path("assets/media/");

            if (copy($imagen->getRealPath(), $ruta . 'login-fondo.png')) {
                $imagen = 'login-fondo.png';
                flash('Imagen Guardada Correctamente')->success();
                return back();
            } else {
                flash('Ocurrió un error, vuelva a intentarlo')->warning();
                return back();
            }
        }
    }
}
