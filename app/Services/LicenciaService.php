<?php

namespace App\Services;

use App\Mail\EnviarLicencia;
use App\Models\{Licencias, Licenciasweb, Licenciasvps};
use Illuminate\Support\Facades\{Mail, Auth, Log};

class LicenciaService
{
    // === MÉTODO PRINCIPAL - REEMPLAZA EmailLicenciaService::enviarLicencia ===
    public static function procesar($accion, $licencia, $cliente, $request = null)
    {
        try {
            // DETECCIÓN AUTOMÁTICA DEL TIPO
            $tipo = self::detectarTipo($licencia, $request);

            // PREPARACIÓN AUTOMÁTICA DE DATOS
            $datos = self::prepararDatos($accion, $tipo, $licencia, $cliente, $request);

            // ENVÍO AUTOMÁTICO
            return self::enviarEmail($datos, $cliente);

        } catch (\Exception $e) {
            Log::error('Error en LicenciaService::procesar', [
                'accion' => $accion,
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // === DETECCIÓN AUTOMÁTICA DE TIPO ===
    private static function detectarTipo($licencia, $request = null)
    {
        // Por modelo de licencia
        if ($licencia instanceof Licencias) return 'pc';
        if ($licencia instanceof Licenciasvps) return 'vps';

        // Web - detectar si es Facturito
        if ($licencia instanceof Licenciasweb || is_array($licencia)) {
            if (isset($licencia->producto) && $licencia->producto == 12) return 'facturito';
            if (isset($licencia['producto']) && $licencia['producto'] == 12) return 'facturito';
            if ($request && isset($request['producto']) && $request['producto'] == 12) return 'facturito';
        }

        return 'web'; // Default
    }

    // === PREPARACIÓN DE DATOS PARA EL MAILABLE ===
    private static function prepararDatos($accion, $tipo, $licencia, $cliente, $request)
    {
        $configEmails = config('sistema.emails');
        // LÓGICA ESPECIAL: Para credenciales usar template específico
        $esCredenciales = str_contains($accion, 'credenciales');
        $template = $esCredenciales ? $configEmails['templates']['credenciales'] : $configEmails['templates'][$tipo];

        $productoNombre = $configEmails['productos_nombres'][$tipo];
        $subjectTemplate = $configEmails['subjects'][$accion];

        // Subject dinámico - SIN "Perseo Software" para Facturito
        $subject = str_replace('{producto}', $productoNombre, $subjectTemplate);
        if ($tipo !== 'facturito') {
            $subject .= ' - Perseo Software';
        }


        // DATOS BASE (compatibles con mailable actual)
        $datos = [
            'view' => $template,
            'subject' => $subject,
            'titulo' => self::obtenerTituloAccion($accion),
            'accion' => self::obtenerDescripcionAccion($accion),
            'producto' => $tipo,
            'cliente' => $cliente->nombres ?? '',
            'identificacion' => $cliente->identificacion ?? '',
            'numerocontrato' => $licencia['numerocontrato'] ?? $licencia->numerocontrato ?? '',
            'usuario' => Auth::user()->nombres ?? 'Sistema',
            'fecha' => date('Y-m-d H:i', strtotime(self::obtenerFechaSegunAccion($accion, $licencia))),
            'from' => env('MAIL_FROM_ADDRESS'),
        ];

        // CAMPOS ESPECÍFICOS PARA CREDENCIALES
        if ($esCredenciales) {
            $datos['tipo_credenciales'] = str_contains($accion, 'simples') ? 'simples' : 'completas';
            $datos['tipo_producto'] = $licencia['producto'] ?? $licencia->producto ?? $request['producto'] ?? 0;
        }

        // DATOS ESPECÍFICOS POR TIPO
        $datos = array_merge($datos, self::obtenerDatosEspecificos($tipo, $licencia, $cliente, $request));

        return $datos;
    }

    // === DATOS ESPECÍFICOS POR TIPO ===
    private static function obtenerDatosEspecificos($tipo, $licencia, $cliente, $request)
    {
        return match ($tipo) {
            'web', 'facturito' => self::datosWeb($licencia, $cliente, $request),
            'pc' => self::datosPC($licencia, $cliente),
            'vps' => self::datosVPS($licencia, $cliente),
            default => []
        };
    }

    private static function datosWeb($licencia, $cliente, $request)
    {
        return [
            'vendedor' => $cliente->razonsocial ?? '',
            'distribuidor' => $cliente->nombredistribuidor ?? '',
            'periodo' => self::obtenerPeriodo($request['producto'] ?? 0, $request['periodo'] ?? null),
            'fechainicia' => isset($licencia['fechainicia']) ? date("d-m-Y", strtotime($licencia['fechainicia'])) : date("d-m-Y"),
            'fechacaduca' => isset($licencia['fechacaduca']) ? date("d-m-Y", strtotime($licencia['fechacaduca'])) : date("d-m-Y"),
            'empresas' => $licencia['empresas'] ?? $licencia->empresas ?? 1,
            'numeromoviles' => $licencia['numeromoviles'] ?? $licencia->numeromoviles ?? 0,
            'usuarios' => $licencia['usuarios'] ?? $licencia->usuarios ?? 1,
            'numerosucursales' => $licencia['numerosucursales'] ?? 0,
            'tipo_producto' => $request['producto'],
            'correo' => $cliente->correos,
            // Módulos (convertir XML a objeto si es necesario)
            'modulos' => self::procesarModulos($licencia['modulos'] ?? $licencia->modulos ?? null)
        ];
    }

    private static function datosPC($licencia, $cliente)
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

    private static function datosVPS($licencia, $cliente)
    {
        return [
            'servidor_vps' => $licencia['servidor_vps'] ?? 'VPS-01',
            'ip' => $licencia['ip'] ?? $licencia['ipservidor'] ?? 'N/A',
            'fecha_corte_proveedor' => isset($licencia['fecha_corte_proveedor']) ? date("d-m-Y", strtotime($licencia['fecha_corte_proveedor'])) : date("d-m-Y"),
            'fecha_corte_cliente' => isset($licencia['fecha_corte_cliente']) ? date("d-m-Y", strtotime($licencia['fecha_corte_cliente'])) : date("d-m-Y"),
            'correo' => $cliente->correos,
            'usuario_vps' => $licencia['usuario'] ?? '',
        ];
    }

    // === ENVÍO DE EMAIL ===
    private static function enviarEmail($datos, $cliente)
    {
        // Preparar emails destinatarios (igual que el servicio original)
        $emails = self::prepararEmails($cliente);

        // Solo enviar si no está en modo local y hay emails
        //if (!config('sistema.local_mode') && !empty($emails)) {

        $mailable = new EnviarLicencia($datos);

        // Agregar attachments si existen
        self::agregarAttachments($mailable, $datos);

        // Enviar
        Mail::to($emails)->queue($mailable);
        //}

        return ['success' => true, 'message' => 'Email procesado correctamente'];
    }

    // === ATTACHMENTS ===
    private static function agregarAttachments($mailable, $datos)
    {
        // Solo agregar attachments para credenciales completas
        $esCredenciales = str_contains($datos['accion'], 'credenciales') &&
            !str_contains($datos['accion'], 'simples');

        if ($esCredenciales) {
            $attachments = config('sistema.emails.attachments.credenciales', []);

            foreach ($attachments as $attachment) {
                $path = str_replace('public_path', public_path(), $attachment);
                if (file_exists($path)) {
                    $mailable->attach($path);
                }
            }
        }
    }

    // === PREPARAR EMAILS DESTINATARIOS (Igual que original) ===
    private static function prepararEmails($cliente)
    {
        $emails = [
            $cliente->correos ?? '',
            $cliente->vendedor ?? '',
            'facturacion@perseo.ec'
        ];

        // Agregar emails del distribuidor
        if (!empty($cliente->distribuidor)) {
            $emailsDistribuidor = array_map('trim', explode(',', $cliente->distribuidor));
            $emails = array_merge($emails, $emailsDistribuidor);
        }

        // Agregar email del usuario autenticado
        try {
            if (Auth::check() && Auth::user()->correo) {
                $emails[] = Auth::user()->correo;
            }
        } catch (\Exception $e) {
            Log::info('Auth no disponible al preparar emails: ' . $e->getMessage());
        }

        // Filtrar emails vacíos y duplicados
        return array_unique(array_filter($emails, function ($email) {
            return !empty(trim($email)) && filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        }));
    }

    // === MÉTODOS AUXILIARES ===
    private static function obtenerFechaSegunAccion($accion, $licencia)
    {
        // Según la acción, usar la fecha correspondiente
        if (in_array($accion, ['modificado', 'renovacion_mensual', 'renovacion_anual', 'recarga_documentos'])) {
            // Para modificaciones/renovaciones, priorizar fechamodificacion
            $fecha = $licencia['fechamodificacion'] ?? $licencia->fechamodificacion ??
                $licencia['fechacreacion'] ?? $licencia->fechacreacion ??
                now()->format('Y-m-d H:i');
        } else {
            // Para creaciones/credenciales, priorizar fechacreacion
            $fecha = $licencia['fechacreacion'] ?? $licencia->fechacreacion ??
                $licencia['fechamodificacion'] ?? $licencia->fechamodificacion ??
                now()->format('Y-m-d H:i');
        }

        return $fecha;
    }

    private static function obtenerPeriodo($producto, $periodo)
    {
        if ($producto == 12) {
            // Validar que el período sea válido
            if (empty($periodo) || !is_numeric($periodo)) {
                return 'No definido';
            }

            $etiquetas = config('sistema.periodos.etiquetas.facturito');
            $mapa = config('sistema.periodos.web.facturito');

            // Validar que exista el mapeo
            if (!isset($mapa[$periodo])) {
                return 'No definido';
            }

            $tipoPeriodo = $mapa[$periodo];

            // Validar que exista la etiqueta
            if (!isset($etiquetas[$tipoPeriodo])) {
                return 'No definido';
            }

            return $etiquetas[$tipoPeriodo];
        }

        return $periodo == 1 ? 'Mensual' : 'Anual';
    }

    private static function procesarModulos($modulos)
    {
        if (is_string($modulos)) {
            // Es XML, convertir a objeto
            $xml = simplexml_load_string($modulos);
            return json_decode(json_encode($xml));
        }

        return $modulos; // Ya es objeto/array
    }

    private static function obtenerTituloAccion($accion)
    {
        return match ($accion) {
            'nuevo' => 'Nueva licencia:',
            'modificado' => 'Licencia modificada:',
            'renovacion_mensual' => 'Renovación mensual:',
            'renovacion_anual' => 'Renovación anual:',
            'recarga_documentos' => 'Recarga de documentos:',
            'credenciales' => 'Credenciales de acceso:',
            'credenciales_simples' => 'Recordatorio de credenciales:',
            default => ucfirst($accion) . ':'
        };
    }

    private static function obtenerDescripcionAccion($accion)
    {
        return match ($accion) {
            'nuevo' => 'Creación',
            'modificado' => 'Modificación',
            'renovacion_mensual', 'renovacion_anual' => 'Renovación',
            'recarga_documentos' => 'Recarga',
            'credenciales' => 'Envío de credenciales',
            'credenciales_simples' => 'Credenciales de acceso',
            default => ucfirst($accion)
        };
    }

    // === MÉTODO DE CREDENCIALES (Simplificado) ===
    public static function enviarCredenciales($clienteId, $productoId, $tipo = 'completas')
    {
        try {
            // Obtener cliente con relaciones
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

            if (!$cliente) {
                return ['success' => false, 'message' => 'Cliente no encontrado'];
            }

            // Crear licencia ficticia para envío de credenciales
            $licencia = ['producto' => $productoId];
            $request = ['producto' => $productoId];

            // Determinar acción
            $accion = $tipo === 'simples' ? 'credenciales_simples' : 'credenciales';

            // Procesar
            return self::procesar($accion, $licencia, $cliente, $request);

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
