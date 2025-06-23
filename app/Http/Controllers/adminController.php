<?php

namespace App\Http\Controllers;

use App\Mail\enviarlicencia;
use App\Models\Ciudades;
use App\Models\Clientes;
use App\Models\Licencias;
use App\Models\Licenciasvps;
use App\Models\Licenciasweb;
use App\Models\Links;
use App\Models\Log;
use App\Models\Publicidades;
use App\Models\Servidores;
use App\Models\Subcategorias;
use App\Models\User;
use App\Models\Usuarios;
use App\Rules\UniqueSimilar;
use App\Rules\ValidarCelular;
use App\Rules\ValidarCorreo;
use App\Services\LogService;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;
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

    public function soporte()
    {
        return view('admin.auth.soporte');
    }

    public function post_soporte(Request $request)
    {
        $request->validate(
            [
                'numerocontrato' => 'required',
            ],
            [
                'numerocontrato.required' => 'Ingrese numero de contrato',
            ],
        );

        $licencia = Licenciasweb::where('numerocontrato', $request->numerocontrato)->first();

        if (!$licencia) {
            flash('Número de contrato no encontrado')->error();
            return back();
        }

        $servidor = Servidores::where('sis_servidoresid', $licencia->sis_servidoresid)->first();

        $search = ['+', '/', '='];  // Caracteres que quieres reemplazar
        $replace = ['.', '_', '-']; // Valores de reemplazo correspondientes

        $encriptado = str_replace($search, $replace, encrypt_openssl(date("Ymd"), "Perseo1232*"));

        $url = $servidor->dominio . '/sistema?contrato=' . $licencia->numerocontrato . '&token=' . $encriptado;

        return back()->with(['url' => $url]);
    }

    public function post_loginRedireccion(Request $request)
    {
        $identificacionIngresada = substr($request->identificacion, 0, 10);

        $path = public_path('/assets/servidores.json');
        $datos_servidores = file_get_contents($path);
        $servidores = json_decode($datos_servidores);

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
                    if ($servidor->sis_servidoresid == 2) {
                        $array[] = ["sis_servidoresid" => $servidor->sis_servidoresid, "descripcion" => $servidor->descripcion, "dominio" => $servidor->dominio . '/facturito?identificacion=' . $identificacionIngresada];
                    } else {
                        $array[] = ["sis_servidoresid" => $servidor->sis_servidoresid, "descripcion" => $servidor->descripcion, "dominio" => $servidor->dominio . '/sistema?identificacion=' . $identificacionIngresada];
                    }
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

        $usuario = User::where('identificacion', $request->identificacion)->first();
        if ($usuario) {
            if ($usuario->estado == 0) {
                flash('Usuario Inactivo')->error();
                return back();
            }

            if ($usuario->contrasena === encrypt_openssl($request->contrasena, "Perseo1232*")) {
                // Verificar la fortaleza de la contraseña actual
                $isPasswordStrong = $this->isPasswordStrong($request->contrasena);

                if (!$isPasswordStrong) {
                    Auth::login($usuario, true);
                    flash('Tu contraseña actual no cumple con los requisitos de seguridad. Por favor, cambia tu contraseña.')->warning();
                    return redirect()->route('usuarios.cambiar_clave'); // Asumiendo que tienes una ruta para cambiar contraseña
                }

                // Proceso de login
                if ($request->has('recordar')) {
                    Auth::login($usuario, true);
                } else {
                    Auth::login($usuario, false);
                }
                $request->session()->regenerate();
                return $this->redirectUserBasedOnType($usuario->tipo);
            } else {
                flash('Usuario o Contraseña Incorrectos')->error();
                return back();
            }
        } else {
            flash('Usuario o Contraseña Incorrectos')->error();
            return back();
        }
    }

    private function redirectUserBasedOnType($tipo)
    {
        if ($tipo == 5) {
            return redirect()->route('notificaciones.index');
        }
        return redirect()->route('clientes.index');
    }

    private function isPasswordStrong($password)
    {
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*#?&.,_-]{8,}$/';
        return preg_match($pattern, $password);
    }

    public function cambiar_clave()
    {
        return view('admin.auth.cambiarclave');
    }

    public function updatePassword(Request $request)
    {
        $request->validate(
            [
                'password' => 'required|string|min:8|confirmed|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/',
            ],
            [
                'password.required' => 'Ingrese su nueva contraseña',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres',
                'password.confirmed' => 'Las contraseñas no coinciden',
                'password.regex' => 'La contraseña debe contener al menos una letra mayúscula, una letra minúscula y un número',
            ]
        );

        $user = Usuarios::find(Auth::user()->sis_distribuidores_usuariosid);
        $user->contrasena = encrypt_openssl($request->password, "Perseo1232*");
        $user->save();

        flash('Tu contraseña ha sido actualizada con éxito.')->success();
        return $this->redirectUserBasedOnType($user->tipo);
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
                    ["id" => "", "nombre" => "Todos"],
                    ["id" => "2", "nombre" => "Facturación"],
                    ["id" => "3", "nombre" => "Servicios"],
                    ["id" => "4", "nombre" => "Comercial"],
                    ["id" => "5", "nombre" => "Soy Contador"],
                    ["id" => "7", "nombre" => "Total"],
                    ["id" => "6", "nombre" => "Perseo Lite Anterior"],
                    ["id" => "8", "nombre" => "Soy Contador Servicios"],
                    ["id" => "9", "nombre" => "Perseo Lite"],
                    ["id" => "10", "nombre" => "Emprendedor"],
                    ["id" => "11", "nombre" => "Socio Perseo"],
                    ["id" => "12", "nombre" => "Facturito"]
                ];
                break;
            case '3':
                $producto = [
                    ["id" => "", "nombre" => "Todos"],
                    ["id" => "1", "nombre" => "Práctico"],
                    ["id" => "2", "nombre" => "Control"],
                    ["id" => "3", "nombre" => "Contable"],
                    ["id" => "10", "nombre" => "Todos Nube"],
                    ["id" => "4", "nombre" => "Prime Nivel 1"],
                    ["id" => "5", "nombre" => "Prime Nivel 2"],
                    ["id" => "6", "nombre" => "Prime Nivel 3"],
                    ["id" => "7", "nombre" => "Contaplus Nivel 1"],
                    ["id" => "8", "nombre" => "Contaplus Nivel 2"],
                    ["id" => "9", "nombre" => "Contaplus Nivel 3"],
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
        $links = Links::where('estado', 1)->get();
        return view('admin.auth.registro', compact('identificacion', 'links'));
    }

    public function post_registro(Request $request)
    {
        $request->validate(
            [
                'identificacion' => ['required', new UniqueSimilar, 'size:13'],
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

        $link = Links::where('sis_linksid', $request['red_origen'])->first();

        $distribuidor = $link->sis_distribuidoresid;
        $assigned_id = $link->usuarioid;
        $source_id = $link->origenid;
        $descripcion = $link->descripcion;

        //Verificar si se debe registrar en bitrix
        if ($link->registra_bitrix == 1) {

            switch ($request['grupo']) {
                case '1':
                    $grupo = '599';
                    break;
                case '2':
                    $grupo = '601';
                    break;
                case '3':
                    $grupo = '603';
                    break;
                case '4':
                    $grupo = '605';
                    break;
                case '5':
                    $grupo = '607';
                    break;
                case '6':
                    $grupo = '609';
                    break;
                case '7':
                    $grupo = '611';
                    break;
                case '8':
                    $grupo = '613';
                    break;
                case '9':
                    $grupo = '615';
                    break;
                case '10':
                    $grupo = '617';
                    break;
                case '11':
                    $grupo = '621';
                    break;
                case '12':
                    $grupo = '623';
                    break;
                default:
                    $grupo = '599';
                    break;
            }

            // Preparar el teléfono y otros campos como en tu ejemplo original
            $telefono = "+593" . substr($request['telefono2'], 1, 9);

            // Base URL de la API
            $baseUrl = 'https://perseo-soft.bitrix24.es/rest/5507/9mgnss30ssjdu1ay/crm.deal.add.json';

            // Construir los parámetros de consulta como un array asociativo
            $queryParams = [
                'FIELDS[ASSIGNED_BY_ID]' => $assigned_id,
                'FIELDS[TITLE]' => $request['nombres'],
                'FIELDS[COMPANY_TITLE]' => $request['nombres'],
                'FIELDS[SOURCE_ID]' => $source_id,
                'FIELDS[SOURCE_DESCRIPTION]' => $descripcion,
                'FIELDS[NAME]' => $request['nombres'],
                'FIELDS[ADDRESS]' => $request['direccion'],
                'FIELDS[PHONE][0][VALUE]' => $telefono,
                'FIELDS[PHONE][0][VALUE_TYPE]' => 'WORK',
                'FIELDS[EMAIL][0][VALUE]' => $request['correos'],
                'FIELDS[EMAIL][0][VALUE_TYPE]' => 'WORK',
                'FIELDS[UF_CRM_1656951427626]' => $request['texto_ciudad'],
                'FIELDS[UF_CRM_1668442025742]' => $grupo,
            ];

            // Realizar la solicitud GET
            $response = Http::withOptions(["verify" => false])
                ->get($baseUrl, $queryParams)
                ->json();

            // Verificar si hay un error en la respuesta
            if (array_key_exists('error', $response)) {
                // Manejar el error
            }
        }

        //Asignacion masiva para los campos asignados en guarded o fillable en el modelo
        $request['fechacreacion'] = now();
        $request['usuariocreacion'] = "Perseo Lite";
        $request['tipoidentificacion'] = "R";
        $request['sis_distribuidoresid'] = $distribuidor;
        $request['sis_revendedoresid'] = 1;
        $request['sis_vendedoresid'] = 405;
        $request['validado'] = 1;
        unset(
            $request['texto_ciudad'],
        );
        DB::beginTransaction();
        try {
            $servidores = Servidores::all();

            //VERIFICAR DISPONIBILIDAD DE TODOS LOS SERVIDORES
            $servidoresNoDisponibles = $this->verificarDisponibilidadServidores($servidores);

            if (!empty($servidoresNoDisponibles)) {
                flash('Ocurrio un error, el servidor no está disponible')->warning();
                return back()->withInput();
            }


            $cliente =   Clientes::create($request->all());

            $clientes_creados = []; // variable para almacenar los clientes creados en los servidores remotos

            // Verificar si se creó el cliente en el servidor local
            if (!$cliente) {
                flash('Ocurrió un error al crear el cliente')->warning();
                DB::rollBack();
                return back();
            }

            $request['sis_clientesid'] = $cliente->sis_clientesid;

            // Insertar el cliente en cada uno de los servidores remotos
            foreach ($servidores as $servidor) {
                $url = $servidor->dominio . '/registros/crear_clientes';
                $crearCliente = Http::withHeaders(['Content-Type' => 'application/json; ', 'verify' => false])
                    ->withOptions(["verify" => false])
                    ->post($url, $request->all())
                    ->json();

                if (isset($crearCliente['sis_clientes'])) {
                    $clientes_creados[] = [
                        'dominio' => $servidor->dominio,
                        'sis_clientesid' => $crearCliente["sis_clientes"][0]['sis_clientesid']
                    ];
                } else {
                    foreach ($clientes_creados as $registro) {
                        $url = $registro['dominio'] . '/registros/eliminar_cliente';
                        $eliminarCliente = Http::withHeaders(['Content-Type' => 'application/json; ', 'verify' => false])
                            ->withOptions(["verify" => false])
                            ->post($url, ["sis_clientesid" => $registro['sis_clientesid']])
                            ->json();
                    }

                    flash('Ocurrió un error al crear el cliente, intentelo nuevamente')->warning();
                    DB::rollBack();
                    return back();
                }
            }

            $log = new Log();
            LogService::crear('Licencia Web', $request->all());
            $log->usuario = "Perseo Lite";
            $log->pantalla = "Clientes";
            $log->tipooperacion = "Crear";
            $log->fecha = now();
            $log->detalle = $cliente;
            $log->save();

            //Verificar que no se exista el numero de contrato
            $contrato = $this->generarContrato();

            $parametros_json = [];
            $parametros_json = [
                'Documentos' => "100000",
                'Productos' => "100000",
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
                "fechacaduca" => date("Ymd", strtotime(date("Ymd") . "+ 1 month")),
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

                $licencia['sis_licenciasid'] = $crearLicenciaWeb["licencias"][0]['sis_licenciasid'];
                Licenciasweb::create($licencia);

                $array['view'] = 'emails.registro_demos';
                $array['from'] = env('MAIL_FROM_ADDRESS');
                $array['subject'] = 'Registro Demo';
                $array['nombre'] =  $cliente->nombres;
                $array['usuario'] =  substr($cliente->identificacion, 0, 10);
                $array['clave'] = '123';
                $array['tipo'] = '6';

                try {
                    // Enviar email solo en producción
                    if (config('app.env') !== 'local') {
                        Mail::to($cliente->correos)->queue(new enviarlicencia($array));
                    }
                } catch (\Exception $e) {
                    flash('Error enviando email')->error();
                    return back();
                }
                $links = Links::where('estado', 1)->get();
                flash('Registrado Correctamente')->success();
                return view('admin.auth.registro')->with(['identificacion' => substr($cliente->identificacion, 0, 10), 'links' => $links]);
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

    // Verifica la disponibilidad de todos los servidores y devuelve una lista de los que no están disponibles
    private function verificarDisponibilidadServidores($servidores)
    {
        $servidoresNoDisponibles = [];

        foreach ($servidores as $servidor) {
            if (!$this->verificarDisponibilidadServidor($servidor)) {
                $servidoresNoDisponibles[] = $servidor->descripcion;
            }
        }

        return $servidoresNoDisponibles;
    }

    // Verifica si un servidor está disponible (responde a HEAD)
    private function verificarDisponibilidadServidor($servidor)
    {
        try {
            $response = Http::timeout(6)
                ->withOptions(['verify' => false])
                ->head($servidor->dominio);

            // 200, 301, 302, 403, 404 son respuestas válidas (servidor responde)
            // 500+ significa servidor con problemas
            return $response->status() < 500;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function generarContrato()
    {
        do {
            $numeroContrato = (string) random_int(1000000000, 9999999999);

            $existe = Licencias::where('numerocontrato', $numeroContrato)->exists() ||
                Licenciasweb::where('numerocontrato', $numeroContrato)->exists() ||
                Licenciasvps::where('numerocontrato', $numeroContrato)->exists();
        } while ($existe);

        return $numeroContrato;
    }

    public function recuperarciudades(Request $request)
    {
        $ciudades = Ciudades::where('ciudadesid', 'like', $request->id . '%')->get();
        return $ciudades;
    }
}
