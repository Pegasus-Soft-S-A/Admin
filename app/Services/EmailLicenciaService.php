<?php

namespace App\Services;

use App\Mail\EnviarLicencia;
use App\Models\Licencias;
use App\Models\Licenciasvps;
use App\Models\Licenciasweb;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class EmailLicenciaService
{
    // ============================================
    // MÉTODO PRINCIPAL - DETECCIÓN AUTOMÁTICA TOTAL
    // ============================================

    public static function enviarLicencia($accion, $licencia, $cliente, $request = null)
    {
        // DETECCIÓN AUTOMÁTICA DEL PRODUCTO
        $producto = self::detectarProducto($licencia, $request);

        // PREPARACIÓN AUTOMÁTICA DE OPCIONES ESPECÍFICAS
        $opciones = self::prepararOpcionesAutomaticas($producto, $licencia, $cliente, $request);

        // ENVÍO AUTOMÁTICO
        self::procesarYEnviar($accion, $producto, $licencia, $cliente, $opciones);
    }

    // ============================================
    // DETECCIÓN AUTOMÁTICA DE PRODUCTO
    // ============================================

    private static function detectarProducto($licencia, $request = null)
    {
        if ($licencia instanceof Licencias) {
            return 'pc';                    // Modelo Licencias = PC
        }

        if ($licencia instanceof Licenciasweb) {
            // Verificar si es Facturito
            if (isset($licencia->producto) && $licencia->producto == 12) {
                return 'facturito';         // Por campo en licencia
            }
            return 'web';                   // Default Web
        }

        if ($licencia instanceof Licenciasvps) {
            return 'vps';                   // VPS
        }
    }

    // ============================================
    // PREPARACIÓN AUTOMÁTICA DE OPCIONES
    // ============================================

    private static function prepararOpcionesAutomaticas($producto, $licencia, $cliente, $request = null)
    {
        switch ($producto) {
            case 'web':
            case 'facturito':
                return self::prepararOpcionesWeb($licencia, $cliente, $request);

            case 'pc':
                return self::prepararOpcionesPC($licencia, $cliente);

            case 'vps':
                return self::prepararOpcionesVPS($licencia, $cliente);

            default:
                return [];
        }
    }

    private static function prepararOpcionesWeb($licencia, $cliente, $request)
    {
        return [
            'vendedor' => $cliente->razonsocial ?? '',
            'distribuidor' => $cliente->nombredistribuidor ?? '',
            'periodo' => $request ? self::obtenerPeriodo($request['producto'] ?? 0, $request['periodo'] ?? null) : 'N/A',
            'fechainicia' => isset($licencia['fechainicia']) ? date("d-m-Y", strtotime($licencia['fechainicia'])) : date("d-m-Y"),
            'fechacaduca' => isset($licencia['fechacaduca']) ? date("d-m-Y", strtotime($licencia['fechacaduca'])) : date("d-m-Y"),
            'empresas' => $licencia['empresas'] ?? 1,
            'numeromoviles' => $licencia['numeromoviles'] ?? 0,
            'usuarios' => $licencia['usuarios'] ?? 1,
            'numerosucursales' => $licencia['numerosucursales'] ?? 0,
            'modulos' => isset($licencia['modulos']) ? json_decode(json_encode(simplexml_load_string($licencia['modulos']))) : (object)[],
            'correo' => $cliente->correos,
            'tipo_producto' => $request['producto']
        ];
    }

    private static function prepararOpcionesPC($licencia, $cliente)
    {
        return [
            'identificador' => $licencia['Identificador'] ?? $licencia['identificador'] ?? '',
            'fechaactulizaciones' => $licencia['fechaactulizaciones'] ?? date("d-m-Y"),
            'ipservidor' => $licencia['ipservidor'] ?? '127.0.0.1',
            'ipservidorremoto' => $licencia['ipservidorremoto'] ?? '',
            'numeroequipos' => $licencia['numeroequipos'] ?? 1,
            'numeromoviles' => $licencia['numeromoviles'] ?? 0,
            'numerosucursales' => $licencia['numerosucursales'] ?? 0,
            'modulos' => isset($licencia['modulos']) ? json_decode($licencia['modulos']) : [],
            'modulopractico' => $licencia['modulopractico'] ?? 0,
            'modulocontable' => $licencia['modulocontable'] ?? 0,
            'modulocontrol' => $licencia['modulocontrol'] ?? 0,
            'modulonube' => $licencia['modulonube'] ?? 0,
            'tipo_nube' => $licencia['tipo_nube'] ?? null,
            'nivel_nube' => $licencia['nivel_nube'] ?? null,
            'correo' => $cliente->correos,
        ];
    }

    private static function prepararOpcionesVPS($licencia, $cliente)
    {
        return [
            'servidor_vps' => $licencia['servidor_vps'] ?? '',
            'plan_vps' => $licencia['plan_vps'] ?? '',
            'correo' => $cliente->correos,
        ];
    }

    // ============================================
    // PROCESAMIENTO Y ENVÍO
    // ============================================

    private static function procesarYEnviar($accion, $producto, $licencia, $cliente, $opciones)
    {
        // Obtener configuraciones
        $configAccion = config("sistema.emails.acciones.{$accion}");
        $configProducto = config("sistema.emails.productos.{$producto}");
        $subjectTemplate = config("sistema.emails.subjects.{$accion}");

        if (!$configAccion || !$configProducto || !$subjectTemplate) {
            \Log::warning("Configuración de email no encontrada", [
                'accion' => $accion,
                'producto' => $producto
            ]);
            return;
        }

        // Generar subject dinámico
        $subject = str_replace('{producto}', $configProducto['nombre'], $subjectTemplate) . ' - Perseo Software';

        // Preparar datos para el email
        $datos = [
            'view' => $configProducto['plantilla'],
            'subject' => $subject,
            'titulo' => $configAccion['titulo'],
            'accion' => $configAccion['accion'],
            'producto' => $producto,
            'cliente' => $cliente->nombres,
            'identificacion' => $cliente->identificacion,
            'numerocontrato' => $licencia['numerocontrato'] ?? '',
            'usuario' => Auth::user()->nombres,
            'fecha' => $licencia['fechacreacion'] ?? $licencia['fechamodificacion'] ?? now()->format('Y-m-d H:i:s'),
            'from' => env('MAIL_FROM_ADDRESS'),
            ...$opciones
        ];

        // Preparar emails destinatarios
        $emails = self::prepararEmails($cliente);

        // Enviar o simular según configuración
        if (config('sistema.local_mode') === false && !empty($emails)) {
            $mailable = new EnviarLicencia($datos);

            // Agregar attachments si existen para esta acción
            if ($attachments = config("sistema.emails.attachments.{$accion}")) {
                foreach ($attachments as $attachment) {
                    $path = str_replace('public_path', public_path(), $attachment);
                    if (file_exists($path)) {
                        $mailable->attach($path);
                    }
                }
            }

            Mail::to($emails)->queue($mailable);
        }
    }

    // ============================================
    // MÉTODOS AUXILIARES
    // ============================================

    private static function prepararEmails($cliente)
    {
        $emails = [
            $cliente->correos ?? '',
            $cliente->distribuidor ?? '',
            $cliente->vendedor ?? '',
            'facturacion@perseo.ec'
        ];

        // Agregar email del usuario autenticado si existe
        try {
            if (Auth::check() && Auth::user()->correo) {
                $emails[] = Auth::user()->correo;
            }
        } catch (\Exception $e) {
            \Log::info('Auth no disponible al preparar emails: ' . $e->getMessage());
        }

        // Filtrar emails vacíos y duplicados
        return array_unique(array_filter($emails, function ($email) {
            return !empty(trim($email)) && filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        }));
    }

    private static function obtenerPeriodo($producto, $periodo)
    {
        if ($producto == 12) {
            return ['1' => 'Inicial', '2' => 'Básico', '3' => 'Premium', '4' => 'Gratis'][$periodo] ?? 'No definido';
        }
        return $periodo == 1 ? 'Mensual' : 'Anual';
    }

    // ============================================
    // MÉTODO PARA CREDENCIALES (SIMPLIFICADO)
    // ============================================

    public static function enviarCredenciales($clienteId, $productoId, $tipoCredenciales = 'completas')
    {
        try {
            $cliente = \App\Models\Clientes::select(
                'sis_clientes.correos',
                'sis_clientes.nombres',
                'sis_clientes.identificacion',
                'sis_distribuidores.correos AS distribuidor',
                'sis_revendedores.correo AS vendedor'
            )
                ->join('sis_distribuidores', 'sis_distribuidores.sis_distribuidoresid', 'sis_clientes.sis_distribuidoresid')
                ->join('sis_revendedores', 'sis_revendedores.sis_revendedoresid', 'sis_clientes.sis_vendedoresid')
                ->where('sis_clientesid', $clienteId)
                ->first();

            //DETECTAR TIPO DE CREDENCIALES Y PRODUCTO
            $accion = $tipoCredenciales === 'simples' ? 'credenciales_simples' : 'credenciales';
            $sufijo = $tipoCredenciales === 'simples' ? '_simples' : '';

            $producto = match ($productoId) {
                12 => "credenciales{$sufijo}_facturito",
                default => "credenciales{$sufijo}_web"
            };

            // Obtener configuraciones desde config
            $configProducto = config("sistema.emails.productos.{$producto}");
            $configAccion = config("sistema.emails.acciones.{$accion}");
            $subject = str_replace('{producto}', $configProducto['nombre'], config("sistema.emails.subjects.{$accion}"));

            // DATOS ESPECÍFICOS SEGÚN TIPO
            $datos = [
                'view' => $configProducto['plantilla'],
                'subject' => $subject . ' - Perseo Software',
                'titulo' => $configAccion['titulo'],
                'accion' => $configAccion['accion'],
                'producto' => str_replace(['credenciales_simples_', 'credenciales_'], '', $producto),
                'cliente' => $cliente->nombres,
                'identificacion' => $cliente->identificacion,
                'from' => env('MAIL_FROM_ADDRESS'),
                'tipo_credenciales' => $tipoCredenciales,
                'tipo_producto' => $productoId,
            ];

            // Preparar emails destinatarios
            $emails = self::prepararEmails($cliente);

            if (config('sistema.local_mode') === false && !empty($emails)) {
                // Crear mailable
                $mailable = new EnviarLicencia($datos);

                // AGREGAR ATTACHMENTS SOLO SI SON CREDENCIALES COMPLETAS
                if ($tipoCredenciales === 'completas') {
                    if ($attachments = config("sistema.emails.attachments.{$accion}")) {
                        foreach ($attachments as $attachment) {
                            $path = str_replace('public_path', public_path(), $attachment);
                            if (file_exists($path)) {
                                $mailable->attach($path);
                            }
                        }
                    }
                }

                Mail::to($emails)->queue($mailable);
            }

            return ['success' => true, 'message' => 'Credenciales enviadas correctamente'];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
