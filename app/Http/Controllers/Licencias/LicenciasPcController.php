<?php

namespace App\Http\Controllers\Licencias;

use App\Mail\enviarlicencia;
use App\Models\Adicionales;
use App\Models\Clientes;
use App\Models\Licencias;
use App\Models\Servidores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class LicenciasPcController extends LicenciasBaseController
{
    public function crear(Clientes $cliente)
    {
        $licencia = new Licencias();
        $licencia->fechacaduca = date("d-m-Y", strtotime("+5 years"));
        $licencia->fechacaduca_soporte = date("d-m-Y", strtotime("+1 year"));
        $licencia->fechaactulizaciones = date("d-m-Y", strtotime("+1 month"));
        $licencia->numeroequipos = 1;
        $licencia->numeromoviles = 0;
        $licencia->numerosucursales = 0;
        $licencia->usuario = "perseo";
        $licencia->clave = "Invencible4050*";
        $licencia->ipservidor = "127.0.0.1";
        $licencia->puerto = "5588";
        $licencia->puertows = "80";
        $licencia->actulizaciones = 1;
        $licencia->aplicaciones = "s";
        $licencia->plan_soporte = 1;
        $licencia->sis_distribuidoresid = $cliente->sis_distribuidoresid;
        $licencia->numerocontrato = $this->generarContrato();
        $licencia->periodo = 3;

        $modulos = [
            'nomina' => false, 'activos' => false, 'produccion' => false,
            'restaurante' => false, 'talleres' => false, 'garantias' => false,
            'operadoras' => false, 'encomiendas' => false, 'crm_cartera' => false,
            'tienda_perseo_publico' => false, 'tienda_perseo_distribuidor' => false,
            'perseo_hybrid' => false, 'tienda_woocommerce' => false,
            'api_whatsapp' => false, 'cash_manager' => false, 'reporte_equifax' => false,
        ];

        return view('admin.licencias.PC.crear', compact('cliente', 'licencia', 'modulos'));
    }

    public function guardar(Request $request)
    {
        $this->validarDatosPC($request);

        if (!$request->modulopractico && !$request->modulocontrol && !$request->modulocontable && !$request->modulonube) {
            flash("Debe seleccionar al menos un sistema principal (Práctico, Control, Contable o Nube)")->error();
            return redirect()->back()->withInput();
        }

        $this->prepararDatosGuardar($request);

        try {
            $servidor = Servidores::where('sis_servidoresid', 4)->firstOrFail();

            $resultado = $this->externalServerService->generateLicense($servidor, $request->all());
            if (!$resultado['success']) {
                throw new \Exception($resultado['error']);
            }

            $request['key'] = $resultado['license_key'];

            $licencia = $this->ejecutarCreacionConTransaccion(
                fn() => Licencias::create($request->all()),
                'Licencia PC',
                $request->all()
            );

            $this->enviarEmailPC($licencia, 'Nuevo Registro Licencia PC', '2');

            flash('Guardado Correctamente')->success();
            return redirect()->route('licencias.Pc.editar', [$request['sis_clientesid'], $licencia->sis_licenciasid]);

        } catch (\Exception $e) {
            flash('Error al crear la licencia: ' . $e->getMessage())->error();
            return redirect()->back()->withInput();
        }
    }

    public function editar(Clientes $cliente, Licencias $licencia)
    {
        $modulos = json_decode($licencia->modulos);

        $empresas = empty($licencia->cantidadempresas)
            ? (object)['empresas_activas' => 0, 'empresas_inactivas' => 0]
            : json_decode($licencia->cantidadempresas);

        // Formatear fechas
        $licencia->fechacaduca = date("d-m-Y", strtotime($licencia->fechacaduca));
        $licencia->fechacaduca_soporte = date("d-m-Y", strtotime($licencia->fechacaduca_soporte));
        $licencia->fechaactulizaciones = date("d-m-Y", strtotime($licencia->fechaactulizaciones));
        $licencia->fecha_actualizacion_ejecutable = date("d-m-Y", strtotime($licencia->fecha_actualizacion_ejecutable));
        $licencia->fecha_respaldo = date("d-m-Y", strtotime($licencia->fecha_respaldo));

        // Obtener adicionales
        $adicionales = Adicionales::where('numerocontrato', $licencia->numerocontrato)->get();
        $tiposLicencia = config('sistema.tipos_productos');
        $tiposAdicional = config('sistema.tipos_venta_adicionales');

        $adicionales->transform(function ($adicional) use ($tiposAdicional, $tiposLicencia) {
            $adicional->tipo_adicional = $tiposAdicional[$adicional->tipo_adicional] ?? $adicional->tipo_adicional;
            $adicional->tipo_licencia = $tiposLicencia[$adicional->tipo_licencia] ?? $adicional->tipo_licencia;
            return $adicional;
        });

        return view('admin.licencias.PC.editar', compact('cliente', 'licencia', 'modulos', 'empresas', 'adicionales'));
    }

    public function actualizar(Request $request, Licencias $licencia)
    {
        $this->validarDatosPC($request, $licencia->sis_licenciasid);

        if (!$request->modulopractico && !$request->modulocontrol && !$request->modulocontable && !$request->modulonube) {
            flash("Debe seleccionar al menos un sistema principal (Práctico, Control, Contable o Nube)")->error();
            return redirect()->back()->withInput();
        }

        $asunto = $this->prepararDatosActualizar($request);

        try {
            $servidor = Servidores::where('sis_servidoresid', 4)->firstOrFail();
            $resultado = $this->externalServerService->generateLicense($servidor, $request->all());
            if (!$resultado['success']) {
                throw new \Exception($resultado['error']);
            }

            $request['key'] = $resultado['license_key'];

            $licenciaActualizada = $this->ejecutarActualizacionConTransaccion(function () use ($request, $licencia) {
                $licencia->update($request->all());
                return $licencia->fresh();
            }, 'Licencia PC');

            $this->enviarEmailPC($licenciaActualizada, $asunto, '4');

            flash('Actualizada Correctamente')->success();

        } catch (\Exception $e) {
            flash('Error al actualizar la licencia: ' . $e->getMessage())->error();
        }

        return back();
    }

    public function eliminar(Licencias $licencia)
    {
        $esAjax = request()->ajax() || request()->wantsJson();

        try {
            $adicionales = $this->verificarDependencias($licencia->numerocontrato);
            if ($adicionales > 0) {
                $mensaje = "No se puede eliminar la licencia porque tiene {$adicionales} recurso(s) adicional(es) asociado(s).";
                return $this->manejarRespuesta($esAjax, false, $mensaje);
            }

            $licenciaData = $licencia->toArray();

            $this->ejecutarEliminacionConTransaccion(function () use ($licencia) {
                $this->limpiarDependencias($licencia->numerocontrato);
                $licencia->delete();
                return true;
            }, 'Licencia PC', $licenciaData);

            return $this->manejarRespuesta($esAjax, true, 'Licencia eliminada correctamente');

        } catch (\Exception $e) {
            $mensaje = 'Error al eliminar la licencia: ' . $e->getMessage();
            return $this->manejarRespuesta($esAjax, false, $mensaje);
        }
    }

    // =====================================
    // MÉTODOS PRIVADOS
    // =====================================

    private function validarDatosPC(Request $request, ?int $licenciaId = null): void
    {
        $identificadorRule = ['required'];
        if ($licenciaId) {
            $identificadorRule[] = 'unique:sis_licencias,identificador,' . $licenciaId . ',sis_licenciasid';
        } else {
            $identificadorRule[] = 'unique:sis_licencias';
        }

        $request->validate([
            'Identificador' => $identificadorRule,
            'correopropietario' => ['required', 'email'],
            'correoadministrador' => ['required', 'email'],
            'correocontador' => ['required', 'email'],
        ], [
            'Identificador.required' => 'Ingrese un Identificador',
            'Identificador.unique' => 'El identificador ya se encuentra registrado',
            'correopropietario.required' => 'Ingrese un Correo de Propietario',
            'correopropietario.email' => 'Ingrese un Correo de Propietario válido',
            'correoadministrador.required' => 'Ingrese un Correo de Administrador',
            'correoadministrador.email' => 'Ingrese un Correo de Administrador válido',
            'correocontador.required' => 'Ingrese un Correo de Contador',
            'correocontador.email' => 'Ingrese un Correo de Contador válido',
        ]);

    }

    private function prepararDatosGuardar(Request $request): void
    {
        $fechaActual = now();
        $usuarioActual = Auth::user();

        // Verificar si el módulo nube está activo
        $moduloNubeActivo = $request->modulonube === 'on' || $request->modulonube == 1;
        if (!$moduloNubeActivo) {
            $request->merge(['usuarios' => 0]);
        }

        $request->merge([
            'fechacreacion' => $fechaActual,
            'fechainicia' => date('Y-m-d', strtotime($fechaActual)),
            'fechacaduca' => date('Y-m-d', strtotime($request->fechacaduca)),
            'fechacaduca_soporte' => date('Y-m-d', strtotime($request->fechacaduca_soporte)),
            'fechaactulizaciones' => date('Y-m-d', strtotime($request->fechaactulizaciones)),
            'fechaultimopago' => date('Y-m-d', strtotime($fechaActual)),
            'usuariocreacion' => $usuarioActual->nombres,
            'numerogratis' => 0,
            'tipo_licencia' => 2,
            'aplicaciones' => $request->aplicaciones_permisos,
        ]);

        $this->procesarModulos($request);
    }

    private function prepararDatosActualizar(Request $request): string
    {
        $fechaActual = now();
        $usuarioActual = Auth::user();

        $tiposActualizacion = [
            'mes' => ['fechacaduca' => '+ 1 month', 'actualizaciones' => '+ 1 month', 'asunto' => 'Renovación Mensual Perseo PC'],
            'anual' => ['fechacaduca' => '+ 1 year', 'actualizaciones' => '+ 1 year', 'asunto' => 'Renovación Anual Perseo PC'],
            'actualizacion' => ['fechacaduca' => null, 'actualizaciones' => '+ 1 year', 'asunto' => 'Actualización Anual Perseo PC']
        ];

        $tipo = $request->tipo ?? 'default';
        $config = $tiposActualizacion[$tipo] ?? null;

        if ($config) {
            $fechaCaduca = $config['fechacaduca']
                ? date("Y-m-d", strtotime($request->fechacaduca . ' ' . $config['fechacaduca']))
                : date('Y-m-d', strtotime($request->fechacaduca));
            $fechaActualizaciones = date('Y-m-d', strtotime($request->fechaactulizaciones . ' ' . $config['actualizaciones']));
            $asunto = $config['asunto'];
        } else {
            $fechaCaduca = date('Y-m-d', strtotime($request->fechacaduca));
            $fechaActualizaciones = date('Y-m-d', strtotime($request->fechaactulizaciones));
            $asunto = 'Modificación Registro Licencia PC';
        }

        $request->merge([
            'fechacaduca' => $fechaCaduca,
            'fechaactulizaciones' => $fechaActualizaciones,
            'fechamodificacion' => $fechaActual,
            'usuariomodificacion' => $usuarioActual->nombres,
            'fechacaduca_soporte' => date('Y-m-d', strtotime($request->fechacaduca_soporte)),
            'aplicaciones' => $request->aplicaciones_permisos,
        ]);

        $this->procesarModulos($request);
        return $asunto;
    }

    private function procesarModulos(Request $request): void
    {
        // Módulos principales
        $modulosPrincipales = ['modulopractico', 'modulocontrol', 'modulocontable', 'modulonube', 'actulizaciones', 'plan_soporte'];
        foreach ($modulosPrincipales as $modulo) {
            $request[$modulo] = $request->$modulo === 'on' ? 1 : 0;
        }

        // Módulos adicionales
        $modulosAdicionales = [
            'nomina' => 'nomina', 'activos' => 'activos', 'produccion' => 'produccion',
            'restaurante' => 'restaurante', 'talleres' => 'talleres', 'garantias' => 'garantias',
            'operadoras' => 'tvcable', 'encomiendas' => 'encomiendas', 'crm_cartera' => 'crmcartera',
            'tienda_perseo_distribuidor' => 'integraciones', 'tienda_perseo_publico' => 'tienda',
            'perseo_hybrid' => 'hybrid', 'tienda_woocommerce' => 'woocomerce',
            'api_whatsapp' => 'apiwhatsapp', 'cash_manager' => 'cashmanager',
            'cash_debito' => 'cashdebito', 'reporte_equifax' => 'equifax',
            'caja_ahorros' => 'ahorros', 'academico' => 'academico',
            'perseo_contador' => 'perseo_contador', 'api_urbano' => 'api_urbano',
        ];

        $modulos = [];
        $camposAEliminar = ['tipo', 'empresas_activas', 'empresas_inactivas', 'aplicaciones_permisos'];

        foreach ($modulosAdicionales as $moduloKey => $requestField) {
            $modulos[$moduloKey] = $request->$requestField === 'on';
            $request[$requestField] = $modulos[$moduloKey];
            $camposAEliminar[] = $requestField;
        }

        $request['modulos'] = json_encode([$modulos]);

        // Campos opcionales
        foreach (['tokenrespaldo', 'ipservidorremoto', 'motivobloqueo', 'mensaje', 'observacion'] as $campo) {
            $request[$campo] = $request->$campo ?? '';
        }

        // Eliminar campos temporales
        foreach ($camposAEliminar as $campo) {
            unset($request[$campo]);
        }
    }

    private function enviarEmailPC(Licencias $licencia, string $asunto, string $tipo): void
    {
        try {
            $cliente = $this->obtenerDatosClienteEmail($licencia->sis_clientesid);
            if (!$cliente) {
                \Log::warning('Cliente no encontrado para envío de email PC', ['licencia_id' => $licencia->sis_licenciasid]);
                return;
            }

            $datosEmail = [
                'view' => 'emails.licenciapc',
                'from' => env('MAIL_FROM_ADDRESS'),
                'subject' => $asunto,
                'cliente' => $cliente->nombres,
                'identificacion' => $cliente->identificacion,
                'correo' => $cliente->correos,
                'numerocontrato' => $licencia->numerocontrato,
                'identificador' => $licencia->Identificador,
                'modulopractico' => $licencia->modulopractico,
                'modulocontable' => $licencia->modulocontable,
                'modulocontrol' => $licencia->modulocontrol,
                'modulonube' => $licencia->modulonube,
                'tipo_nube' => $licencia->tipo_nube,
                'nivel_nube' => $licencia->nivel_nube,
                'ipservidor' => $licencia->ipservidor,
                'ipservidorremoto' => $licencia->ipservidorremoto,
                'numeroequipos' => $licencia->numeroequipos,
                'numeromoviles' => $licencia->numeromoviles,
                'numerosucursales' => $licencia->numerosucursales,
                'modulos' => json_decode($licencia->modulos),
                'usuario' => Auth::user()->nombres,
                'fecha' => $licencia->fechacreacion ?? $licencia->fechamodificacion,
                'tipo' => $tipo,
                'fechaactulizaciones' => $licencia->fechaactulizaciones,
            ];

            $emails = $this->prepararEmailsDestinatarios($cliente);

            if (config('app.env') !== 'local' && !empty($emails)) {
                Mail::to($emails)->queue(new enviarlicencia($datosEmail));
            }

        } catch (\Exception $e) {
            \Log::warning('Error enviando email PC: ' . $e->getMessage(), [
                'licencia_id' => $licencia->sis_licenciasid,
                'asunto' => $asunto
            ]);
            // No interrumpir el proceso principal
        }
    }
}
