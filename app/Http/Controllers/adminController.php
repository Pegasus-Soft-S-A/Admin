<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Licencias\LicenciasBaseController;
use App\Http\Controllers\ClientesController;
use App\Mail\enviarlicencia;
use App\Models\Ciudades;
use App\Models\Clientes;
use App\Models\Links;
use App\Models\Publicidades;
use App\Models\Servidores;
use App\Models\Subcategorias;
use App\Models\User;
use App\Models\Usuarios;
use App\Rules\UniqueSimilar;
use App\Rules\ValidarCelular;
use App\Rules\ValidarCorreo;
use App\Services\ExternalServerService;
use App\Services\LicenciaService;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class adminController extends LicenciasBaseController
{
    protected ClientesController $clientesController;

    public function __construct(
        ExternalServerService $externalServerService,
        ClientesController    $clientesController
    )
    {
        parent::__construct($externalServerService);
        $this->clientesController = $clientesController;
    }

    // =======================================
    // MÉTODOS DE AUTENTICACIÓN
    // =======================================

    public function login()
    {
        return view('admin.auth.login');
    }

    public function loginRedireccion()
    {
        return view('admin.auth.loginredireccion');
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
                    return redirect()->route('usuarios.cambiar_clave');
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

    public function post_loginRedireccion(Request $request)
    {
        $identificacion = substr($request->identificacion, 0, 10);

        // Usar el servicio para buscar el cliente en todos los servidores disponibles
        $resultados = $this->externalServerService->findClientInServers($identificacion);

        if (empty($resultados)) {
            return 0;
        }

        // Construir array de servidores disponibles para el cliente
        return collect($resultados)->map(function ($resultado) use ($identificacion) {
            $servidor = $resultado['servidor'];

            return [
                'sis_servidoresid' => $servidor->sis_servidoresid,
                'descripcion' => $servidor->descripcion,
                'dominio' => $this->construirUrlAccesoServidor($servidor, $identificacion)
            ];
        })->toArray();
    }

    private function construirUrlAccesoServidor(Servidores $servidor, string $identificacion): string
    {
        // Determinar la aplicación según el servidor
        // Servidor 2 = Facturito, resto = Sistema
        $app = $servidor->sis_servidoresid == 2 ? 'facturito' : 'sistema';

        return "{$servidor->dominio}/{$app}?identificacion={$identificacion}";
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

    // =======================================
    // REGISTRO Y LICENCIAS DEMO
    // =======================================

    public function registro()
    {
        $identificacion = 0;
        $links = Links::where('estado', 1)->get();
        return view('admin.auth.registro', compact('identificacion', 'links'));
    }

    public function post_registro(Request $request)
    {
        // Validar datos
        $this->validarDatosRegistro($request);

        // Procesar link y preparar datos específicos
        $linkData = $this->procesarLink($request);

        // Preparar datos
        $this->prepararDatos($request, $linkData);;

        try {
            $servidores = Servidores::where('estado', 1)->get();

            // Transaccion local
            $cliente = $this->ejecutarCreacionConTransaccion(
                fn() => Clientes::create($request->all()),
                'Cliente',
                $request->all()
            );
            $request['sis_clientesid'] = $cliente->sis_clientesid;

            // Crear remotamente
            $resultado = $this->externalServerService->batchOperation(
                $servidores,
                'create_client',
                $request->all(),
                true
            );

            if (!$resultado['success']) {
                throw new \Exception($resultado['error']);
            }

            // Crear licencia demo
            $this->crearLicenciaDemo($cliente);
            if ($linkData['registra_bitrix']) {
                $this->registrarEnBitrix($request, $linkData);
            }

            $links = Links::where('estado', 1)->get();
            flash('Registrado Correctamente')->success();

            return view('admin.auth.registro')->with([
                'identificacion' => substr($cliente->identificacion, 0, 10),
                'links' => $links
            ]);

        } catch (\Exception $e) {
            flash('Error al crear cliente: ' . $e->getMessage())->error();
            return back()->withInput();
        }
    }

    // =======================================
    // SOPORTE
    // =======================================

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

        $licencia = \App\Models\Licenciasweb::where('numerocontrato', $request->numerocontrato)->first();

        if (!$licencia) {
            flash('Número de contrato no encontrado')->error();
            return back();
        }

        $servidor = Servidores::where('sis_servidoresid', $licencia->sis_servidoresid)->first();

        $search = ['+', '/', '='];
        $replace = ['.', '_', '-'];

        $encriptado = str_replace($search, $replace, encrypt_openssl(date("Ymd"), "Perseo1232*"));

        $url = $servidor->dominio . '/sistema?contrato=' . $licencia->numerocontrato . '&token=' . $encriptado;

        return back()->with(['url' => $url]);
    }

    // =======================================
    // UTILIDADES Y APIs
    // =======================================

    public function cambiarMenu(Request $request)
    {
        Session::put('menu', $request->estado);
    }

    public function subcategorias(Request $request)
    {
        if ($request->ajax()) {
            $data = Subcategorias::select('sis_categorias.categoriasdescripcion', 'sis_subcategorias.sis_subcategoriasid', 'sis_subcategorias.sis_categoriasid', 'sis_subcategorias.descripcionsubcategoria')
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
                    ["id" => "", "nombre" => "Todos"]
                ];
                break;
        }

        return with(["producto" => $producto]);
    }

    public function recuperarciudades(Request $request)
    {
        $ciudades = Ciudades::where('ciudadesid', 'like', $request->id . '%')->get();
        return $ciudades;
    }

    // =======================================
    // LICENCIAS
    // =======================================
    public function licencia($servidorid, $clienteid)
    {
        try {
            $servidor = Servidores::where('sis_servidoresid', $servidorid)->firstOrFail();

            // ✅ USAR ExternalServerService en lugar de HTTP directo
            $resultado = $this->externalServerService->queryLicense($servidor, [
                'sis_clientesid' => $clienteid
            ]);

            if ($resultado['success'] && isset($resultado['licenses'])) {
                $licencias = $resultado['licenses'];

                // Procesar productos (lógica original)
                foreach ($licencias as $key => $licencia) {
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
                        case '12':
                            $producto = "Facturito";
                            break;
                        default:
                            $producto = "Producto desconocido";
                            break;
                    }
                    $licencias[$key]['producto'] = $producto;
                }
            } else {
                $licencias = [['sis_licenciasid' => 0, 'numerocontrato' => 'Cliente sin Licencia', 'producto' => '']];
            }

            return with(["licencia" => $licencias]);

        } catch (\Exception $e) {
            Log::error("Error en AdminController.licencia(): " . $e->getMessage());
            return with(["licencia" => [['sis_licenciasid' => 0, 'numerocontrato' => 'Error al consultar', 'producto' => '']]]);
        }
    }

    // =======================================
    // MÉTODOS PRIVADOS PARA REGISTRO
    // =======================================
    private function validarDatosRegistro(Request $request)
    {
        $request->validate([
            'identificacion' => ['required', new UniqueSimilar, 'size:13'],
            'nombres' => 'required',
            'direccion' => 'required',
            'correos' => ['required', 'email', new ValidarCorreo],
            'provinciasid' => 'required',
            'ciudadesid' => 'required',
            'telefono2' => ['required', new ValidarCelular],
            'grupo' => 'required'
        ], [
            'identificacion.required' => 'Ingrese su cédula o RUC',
            'identificacion.size' => 'Ingrese 13 dígitos',
            'nombres.required' => 'Ingrese una Razón Social',
            'direccion.required' => 'Ingrese una Dirección',
            'correos.required' => 'Ingrese un Correo',
            'correos.email' => 'Ingrese un Correo Válido',
            'provinciasid.required' => 'Seleccione una Provincia',
            'ciudadesid.required' => 'Seleccione una Ciudad',
            'telefono2.required' => 'Ingrese Whatsapp',
            'grupo.required' => 'Seleccione un Tipo de Negocio'
        ]);
    }

    private function procesarLink(Request $request): array
    {
        $link = Links::where('sis_linksid', $request['red_origen'])->first();

        if (!$link) {
            throw new \Exception("Link no encontrado para red_origen: {$request['red_origen']}");
        }

        return [
            'distribuidor' => $link->sis_distribuidoresid,
            'assigned_id' => $link->usuarioid,
            'source_id' => $link->origenid,
            'descripcion' => $link->descripcion,
            'registra_bitrix' => $link->registra_bitrix == 1
        ];
    }

    private function prepararDatos(Request $request, array $linkData): void
    {
        $request['ciudadesid'] = str_pad($request->ciudadesid, '4', "0", STR_PAD_LEFT);
        $request['telefono2'] = $request['telefono2'] ?: "";
        $request['fechacreacion'] = now();
        // Solo datos específicos del registro que no maneja ClientesController
        $request->merge([
            'usuariocreacion' => "Perseo Lite",
            'tipoidentificacion' => "R",
            'sis_distribuidoresid' => $linkData['distribuidor'],
            'sis_revendedoresid' => 1,
            'sis_vendedoresid' => 405,
            'validado' => 1,
        ]);

        unset($request['texto_ciudad']);
    }

    private function registrarEnBitrix(Request $request, array $linkData): void
    {
        try {
            $gruposBitrix = [
                '1' => '599', '2' => '601', '3' => '603', '4' => '605', '5' => '607',
                '6' => '609', '7' => '611', '8' => '613', '9' => '615', '10' => '617',
                '11' => '621', '12' => '623'
            ];

            $grupo = $gruposBitrix[$request['grupo']] ?? '599';
            $telefono = "+593" . substr($request['telefono2'], 1, 9);

            $queryParams = [
                'FIELDS[ASSIGNED_BY_ID]' => $linkData['assigned_id'],
                'FIELDS[TITLE]' => $request['nombres'],
                'FIELDS[COMPANY_TITLE]' => $request['nombres'],
                'FIELDS[SOURCE_ID]' => $linkData['source_id'],
                'FIELDS[SOURCE_DESCRIPTION]' => $linkData['descripcion'],
                'FIELDS[NAME]' => $request['nombres'],
                'FIELDS[ADDRESS]' => $request['direccion'],
                'FIELDS[PHONE][0][VALUE]' => $telefono,
                'FIELDS[PHONE][0][VALUE_TYPE]' => 'WORK',
                'FIELDS[EMAIL][0][VALUE]' => $request['correos'],
                'FIELDS[EMAIL][0][VALUE_TYPE]' => 'WORK',
                'FIELDS[UF_CRM_1656951427626]' => $request['texto_ciudad'] ?? '',
                'FIELDS[UF_CRM_1668442025742]' => $grupo,
            ];

            // ✅ Llamar al método que ahora retorna array
            $resultado = $this->externalServerService->registrarEnBitrix($queryParams);

            // ✅ Validar que existe la clave antes de acceder
            if (isset($resultado['success']) && $resultado['success']) {
                \Log::info('Cliente registrado en Bitrix exitosamente', [
                    'cliente' => $request['nombres'],
                    'deal_id' => $resultado['deal_id'] ?? 'ID no disponible',
                    'message' => $resultado['message'] ?? 'Registro exitoso'
                ]);
            } else {
                \Log::warning('Fallo al registrar en Bitrix', [
                    'cliente' => $request['nombres'],
                    'error' => $resultado['error'] ?? 'Error desconocido',
                    'resultado_completo' => $resultado
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Error en registrarEnBitrix', [
                'cliente' => $request['nombres'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function crearLicenciaDemo(Clientes $cliente): void
    {
        try {
            $configProducto = config('sistema.productos.web.9'); // Perseo Lite
            $configDemo = $configProducto['demo'];

            // Preparar datos para la licencia demo
            $datosDemo = [
                'sis_clientesid' => $cliente->sis_clientesid,
                'sis_distribuidoresid' => $cliente->sis_distribuidoresid,
                'numerocontrato' => $this->generarContrato(),
                'producto' => 9,
                'periodo' => 1,
                'fechainicia' => now()->format('d-m-Y'),
                'fechacaduca' => now()->addDays($configDemo['dias_vigencia'])->format('d-m-Y'),
                'empresas' => $configProducto['empresas'],
                'usuarios' => $configProducto['usuarios'],
                'numeromoviles' => $configProducto['moviles'],
                'numerosucursales' => $configProducto['sucursales'],
                'sis_servidoresid' => $configProducto['servidor'],
                'precio' => $configProducto['mensual']['precio'],
                'estado' => 1,
            ];

            // Aplicar módulos desde configuración
            foreach ($configProducto['modulos'] as $modulo => $activo) {
                $datosDemo[$modulo] = $activo ? 'on' : '';
            }

            // Preparar datos para servidor externo
            $this->prepararDatosDemo($datosDemo, $configDemo);

            // Obtener servidor y crear licencia en servidor externo
            $servidor = Servidores::where('sis_servidoresid', $datosDemo['sis_servidoresid'])->firstOrFail();

            $resultado = $this->externalServerService->createLicense($servidor, $datosDemo);
            if (!$resultado['success']) {
                throw new \Exception($resultado['error']);
            }

            $datosDemo['sis_licenciasid'] = $resultado['license_id'];

            // Crear licencia local usando transacción
            $licencia = $this->ejecutarCreacionConTransaccion(
                fn() => \App\Models\Licenciasweb::create($datosDemo),
                'Licencia Web Demo',
                $datosDemo
            );

            // Enviar email de demo
            LicenciaService::enviarCredenciales($cliente->sis_clientesid, $licencia->producto);
        } catch (\Exception $e) {
            // Log el error pero no interrumpir el proceso principal
            \Log::error('Error creando licencia demo: ' . $e->getMessage(), [
                'cliente_id' => $cliente->sis_clientesid,
                'cliente' => $cliente->nombres
            ]);

            // Opcional: notificar al usuario que el cliente se creó pero la demo falló
            flash('Cliente registrado correctamente. Error al crear demo: contacte soporte.')->warning();
        }
    }

    private function prepararDatosDemo(array &$datosDemo, array $configDemo): void
    {
        $datosDemo = array_merge($datosDemo, [
            'fechacreacion' => now(),
            'usuariocreacion' => 'Perseo Lite',
            'fechainicia' => date('Ymd', strtotime($datosDemo['fechainicia'])),
            'fechacaduca' => date('Ymd', strtotime($datosDemo['fechacaduca'])),
            'fechaultimopago' => date('Ymd', strtotime(now())),
            'tipo_licencia' => 1,
            'Identificador' => $datosDemo['numerocontrato'],
            'parametros_json' => json_encode($configDemo['parametros']),
            'modulos' => $this->generarModulosXml($datosDemo),
        ]);

        // Eliminar campos temporales antes de guardar en BD
        $camposAEliminar = ['nomina', 'activos', 'produccion', 'restaurantes', 'talleres', 'garantias', 'ecommerce'];
        foreach ($camposAEliminar as $campo) {
            unset($datosDemo[$campo]);
        }
    }

    private function generarModulosXml(array $datosDemo): string
    {
        $xw = xmlwriter_open_memory();
        xmlwriter_start_document($xw, '1.0', 'UTF-8');
        xmlwriter_start_element($xw, 'modulos');

        $modulos = ['nomina', 'activos', 'produccion', 'restaurantes', 'talleres', 'garantias', 'ecommerce'];
        foreach ($modulos as $modulo) {
            xmlwriter_start_element($xw, $modulo);
            xmlwriter_text($xw, ($datosDemo[$modulo] === 'on') ? 1 : 0);
            xmlwriter_end_element($xw);
        }

        xmlwriter_end_element($xw);
        xmlwriter_end_document($xw);
        return xmlwriter_output_memory($xw);
    }

    // =======================================
    // MÉTODOS AUXILIARES
    // =======================================

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
}
