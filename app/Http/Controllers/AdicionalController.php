<?php

namespace App\Http\Controllers;

use App\Models\Adicionales;
use App\Models\Licencias;
use App\Models\Licenciasweb;
use App\Models\Servidores;
use App\Services\ExternalServerService;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdicionalController extends Controller
{
    private ExternalServerService $externalServerService;

    public function __construct(ExternalServerService $externalServerService)
    {
        $this->externalServerService = $externalServerService;
    }

    public function agregarAdicional(Request $request)
    {
        //  PRE-VERIFICACIÓN: Verificar disponibilidad del servidor ANTES de empezar la transacción
        $licencia = $this->obtenerLicencia($request->numerocontrato);
        if (!$licencia) {
            return response()->json(['success' => false, 'message' => 'No se encontró licencia'], 404);
        }

        //  VERIFICAR SERVIDOR EXTERNO (solo para licencias Web)
        if (!$this->esLicenciaPC($licencia)) {
            $servidor = Servidores::where('sis_servidoresid', $licencia->sis_servidoresid)->first();

            if ($servidor && !$this->externalServerService->checkServerAvailability($servidor)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El servidor no está disponible en este momento. Intente más tarde.'
                ], 503);
            }
        }

        DB::beginTransaction();

        try {
            $request->validate([
                'numerocontrato' => 'required|string|max:50',
                'fechainicia' => 'required|date',
                'fechacaduca' => 'required|date|after:fechainicia',
                'tipo_adicional' => 'required|integer|in:1,2,3,4,5',
                'tipo_licencia' => 'required|integer|in:1,2,3',
                'periodo' => 'required|integer|in:1,2,3',
                'cantidad' => 'required|integer|min:1|max:100'
            ]);

            // Validación dinámica usando configuración
            $validacion = $this->validarAdicionalDinamico($request, $licencia);
            if (!$validacion['valido']) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => $validacion['mensaje']]);
            }

            // Procesar adicional
            $adicional = $this->procesarAdicional($request, $licencia);

            // Actualizar campo usando configuración dinámica
            $this->actualizarCampoLicenciaDinamico($licencia, $request->tipo_adicional, $request->cantidad);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Recurso adicional procesado correctamente",
                'data' => $adicional,
                'licencia_actualizada' => $this->obtenerCamposActualizados($licencia)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al agregar adicional: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Ocurrió un error interno del servidor'], 500);
        }
    }

    //  Validación dinámica usando configuración
    private function validarAdicionalDinamico($request, $licencia)
    {
        $tiposConfig = config('sistema.tipos_adicionales', []);
        $tipoConfig = $tiposConfig[$request->tipo_adicional] ?? null;

        if (!$tipoConfig) {
            return ['valido' => false, 'mensaje' => 'Tipo de adicional no válido'];
        }

        // Detectar módulo/producto dinámicamente según tipo de licencia
        if ($this->esLicenciaPC($licencia)) {
            return $this->validarAdicionalPC($request, $licencia, $tipoConfig);
        } else {
            return $this->validarAdicionalWeb($request, $licencia, $tipoConfig);
        }
    }

    //  Validación específica para PC
    private function validarAdicionalPC($request, $licencia, $tipoConfig)
    {
        $moduloActual = $this->detectarModuloPC($licencia);
        $modulosConfig = config('sistema.productos.pc.modulos_principales', []);
        $adicionalesPermitidos = $modulosConfig[$moduloActual]['adicionales'] ?? [];

        if (!in_array($request->tipo_adicional, $adicionalesPermitidos)) {
            return [
                'valido' => false,
                'mensaje' => "Este tipo de recurso no está disponible para el módulo {$moduloActual}"
            ];
        }

        return ['valido' => true, 'mensaje' => ''];
    }

    //  Validación específica para Web
    private function validarAdicionalWeb($request, $licencia, $tipoConfig)
    {
        $productoConfig = config("sistema.productos.web.{$licencia->producto}", []);
        $adicionalesPermitidos = $productoConfig['adicionales'] ?? [];

        if (!in_array($request->tipo_adicional, $adicionalesPermitidos)) {
            return [
                'valido' => false,
                'mensaje' => "Este tipo de recurso no está disponible para este producto"
            ];
        }

        return ['valido' => true, 'mensaje' => ''];
    }

    //  Detectar si es licencia PC
    private function esLicenciaPC($licencia)
    {
        return $licencia instanceof Licencias ||
            (isset($licencia->tipo_licencia) && $licencia->tipo_licencia == 2);
    }

    //  Detectar módulo PC dinámicamente
    private function detectarModuloPC($licencia)
    {
        $mapaModulos = [
            'modulonube' => 'nube',
            'modulocontable' => 'contable',
            'modulocontrol' => 'control',
            'modulopractico' => 'practico'
        ];

        foreach ($mapaModulos as $campo => $modulo) {
            if (isset($licencia->{$campo}) && $licencia->{$campo} == 1) {
                return $modulo;
            }
        }

        return 'practico'; // Por defecto
    }

    //  Calcular precio usando configuración dinámica
    private function calcularPrecioDinamico($request, $licencia)
    {
        $tipoConfig = config("sistema.tipos_adicionales.{$request->tipo_adicional}");
        $strategy = $tipoConfig['precio_strategy'] ?? 'simple';

        return match ($strategy) {
            'simple' => $this->calcularPrecioSimple($tipoConfig['precios'], $licencia),
            'nube' => $this->calcularPrecioNube($tipoConfig['precios'], $licencia),
            default => 0
        };
    }

    private function calcularPrecioSimple($precios, $licencia)
    {
        $esPC = $licencia instanceof Licencias;
        $periodo = ($licencia->periodo ?? 2) == 1 ? 'mensual' : 'anual';
        $tipoLicencia = $esPC ? 'pc' : 'web';

        return $precios[$tipoLicencia][$periodo] ?? 0;
    }

    private function calcularPrecioNube($precios, $licencia)
    {
        $tipoNube = ($licencia->tipo_nube ?? 1) == 1 ? 'prime' : 'contaplus';
        $nivel = 'nivel' . ($licencia->nivel_nube ?? 1);
        $periodo = ($licencia->periodo ?? 2) == 1 ? 'mensual' : 'anual';

        return $precios[$tipoNube][$nivel][$periodo] ?? 0;
    }

    //  Actualizar campo con lógica diferenciada PC/Web
    private function actualizarCampoLicenciaDinamico($licencia, $tipoAdicional, $cantidadAgregar)
    {
        $tiposConfig = config('sistema.tipos_adicionales', []);
        $tipoConfig = $tiposConfig[$tipoAdicional] ?? null;

        if (!$tipoConfig) {
            throw new \Exception("No se encontró configuración para el tipo adicional {$tipoAdicional}");
        }

        // 1. ACTUALIZAR CAMPO PRINCIPAL
        $campoPrincipal = $tipoConfig['campo_licencia'];
        $valorActual = $licencia->{$campoPrincipal} ?? 0;
        $nuevoValor = $valorActual + $cantidadAgregar;
        $licencia->{$campoPrincipal} = $nuevoValor;

        Log::info("Campo principal {$campoPrincipal} actualizado", [
            'licencia' => $licencia->numerocontrato,
            'valor_anterior' => $valorActual,
            'cantidad_agregada' => $cantidadAgregar,
            'nuevo_valor' => $nuevoValor
        ]);

        // 2. ACTUALIZAR CAMPOS RELACIONADOS (misma cantidad)
        if (isset($tipoConfig['campos_relacionados'])) {
            foreach ($tipoConfig['campos_relacionados'] as $campoRelacionado) {
                $valorActualRelacionado = $licencia->{$campoRelacionado} ?? 0;
                $nuevoValorRelacionado = $valorActualRelacionado + $cantidadAgregar; // ✅ Misma cantidad
                $licencia->{$campoRelacionado} = $nuevoValorRelacionado;

                Log::info("Campo relacionado {$campoRelacionado} actualizado", [
                    'licencia' => $licencia->numerocontrato,
                    'valor_anterior' => $valorActualRelacionado,
                    'cantidad_agregada' => $cantidadAgregar,
                    'nuevo_valor' => $nuevoValorRelacionado
                ]);
            }
        }

        $licencia->save();

        // Resto del método permanece igual...
        if (!$this->esLicenciaPC($licencia)) {
            $this->actualizarServidorExterno($licencia);
        }

        LogService::modificar(
            $this->esLicenciaPC($licencia) ? 'Licencia PC' : 'Licencia Web',
            $licencia->toArray()
        );
    }

    //  Actualizar servidor externo (SOLO PARA WEB)
    private function actualizarServidorExterno($licencia)
    {
        try {
            $servidor = Servidores::where('sis_servidoresid', $licencia->sis_servidoresid)->first();

            if (!$servidor) {
                throw new \Exception("No se encontró el servidor para la licencia web");
            }

            //  USAR EL SERVICIO en lugar de HTTP directo
            $resultado = $this->externalServerService->updateLicense($servidor, $licencia->toArray());

            if (!$resultado['success']) {
                throw new \Exception($resultado['error']);
            }

            Log::info("Servidor externo actualizado correctamente usando servicio", [
                'licencia' => $licencia->numerocontrato,
                'servidor' => $servidor->descripcion
            ]);

        } catch (\Exception $e) {
            // Re-lanzar para provocar rollback completo
            throw new \Exception("Error de conectividad con el servidor externo: " . $e->getMessage());
        }
    }

    //  Obtener campos actualizados dinámicamente
    private function obtenerCamposActualizados($licencia)
    {
        $tiposConfig = config('sistema.tipos_adicionales', []);
        $campos = [];

        // Siempre incluir campos básicos que pueden cambiar
        $camposBasicos = ['numeromoviles', 'numerosucursales', 'numeroequipos', 'usuarios_nube', 'empresas'];
        foreach ($camposBasicos as $campo) {
            if (isset($licencia->{$campo})) {
                $campos[$campo] = $licencia->{$campo};
            }
        }

        // Agregar campos específicos de la configuración
        foreach ($tiposConfig as $tipoId => $config) {
            // Campo principal
            if (isset($config['campo_licencia'])) {
                $campo = $config['campo_licencia'];
                $campos[$campo] = $licencia->{$campo} ?? 0;
            }

            // Campos relacionados
            if (isset($config['campos_relacionados'])) {
                foreach ($config['campos_relacionados'] as $campoRelacionado) {
                    $campos[$campoRelacionado] = $licencia->{$campoRelacionado} ?? 0;
                }
            }
        }

        return $campos;
    }

    //  Métodos auxiliares...
    private function obtenerLicencia($numerocontrato)
    {
        // Buscar primero en PC, luego en Web
        $licencia = Licencias::where('numerocontrato', $numerocontrato)->first();

        if (!$licencia) {
            $licencia = Licenciasweb::where('numerocontrato', $numerocontrato)->first();
        }

        return $licencia;
    }

    private function procesarAdicional($request, $licencia)
    {
        $adicionalExistente = Adicionales::where('numerocontrato', $request->numerocontrato)
            ->where('tipo_adicional', $request->tipo_adicional)
            ->where('tipo_licencia', $request->tipo_licencia)
            ->first();

        $precio = $this->calcularPrecioDinamico($request, $licencia);
        $precioTotal = $precio * $request->cantidad;

        if ($adicionalExistente) {
            // UPDATE: Sumar la nueva cantidad a la existente
            $adicionalExistente->cantidad += $request->cantidad;
            $adicionalExistente->precio += $precioTotal;
            $adicionalExistente->fechacaduca = date('Ymd', strtotime($request->fechacaduca));
            $adicionalExistente->save();

            //Registro de log
            LogService::modificar('Adicional', $request->all());

            return $adicionalExistente;
        } else {
            // CREATE: Crear nuevo registro
            $adicional = Adicionales::create([
                'numerocontrato' => $request->numerocontrato,
                'fechainicia' => $request->fechainicia,
                'fechacaduca' => date('Ymd', strtotime($request->fechacaduca)),
                'tipo_adicional' => $request->tipo_adicional,
                'tipo_licencia' => $request->tipo_licencia,
                'periodo' => $request->periodo,
                'cantidad' => $request->cantidad,
                'precio' => $precioTotal,
            ]);

            //Registro de log
            LogService::crear('Adicional', $request->all());

            return $adicional;
        }
    }

    //  Resto de métodos (obtenerAdicionales) permanecen igual...
    public function obtenerAdicionales(Request $request)
    {
        try {
            $request->validate([
                'numerocontrato' => 'required|string|max:50'
            ]);

            $licencia = $this->obtenerLicencia($request->numerocontrato);

            if (!$licencia) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró licencia'
                ]);
            }

            $adicionales = Adicionales::where('numerocontrato', $request->numerocontrato)
                ->select('tipo_adicional', 'cantidad', 'precio', 'periodo', 'fechacaduca')
                ->get();

            $adicionalesAgrupados = $adicionales->groupBy('tipo_adicional')->map(function ($grupo) {
                return [
                    'tipo_adicional' => $grupo->first()->tipo_adicional,
                    'cantidad' => $grupo->sum('cantidad'),
                    'precio_total' => $grupo->sum('precio'),
                    'periodo' => $grupo->first()->periodo,
                    'fechacaduca' => $grupo->first()->fechacaduca,
                    'registros' => $grupo->count()
                ];
            })->values();

            return response()->json([
                'success' => true,
                'adicionales' => $adicionalesAgrupados,
                'licencia_base' => $this->obtenerCamposActualizados($licencia)
            ]);

        } catch (\Exception $e) {
            Log::error("Error al obtener adicionales: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error interno del servidor'
            ], 500);
        }
    }

    // Proceso para API
    public function procesarAdicionalSimple(Request $request)
    {
        $request->validate([
            'numerocontrato' => 'required',
            'tipo_adicional' => 'required|integer',
            'cantidad' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();

        try {
            $licencia = Licencias::where('numerocontrato', $request->numerocontrato)->first()
                ?? Licenciasweb::where('numerocontrato', $request->numerocontrato)->first();

            if (!$licencia) {
                return response()->json(['success' => false, 'message' => 'Licencia no encontrada'], 404);
            }

            $tipoConfig = config("sistema.tipos_adicionales.{$request->tipo_adicional}");
            $campo = $tipoConfig['campo_licencia'];
            $esPC = $licencia instanceof Licencias;
            $periodo = $licencia->periodo ?? 2;

            $precioUnitario = $this->calcularPrecioDinamico($request, $licencia);
            $precioTotal = $precioUnitario * $request->cantidad;

            // Buscar adicional existente
            $adicional = Adicionales::where('numerocontrato', $request->numerocontrato)
                ->where('tipo_adicional', $request->tipo_adicional)
                ->where('tipo_licencia', $esPC ? 1 : 2)
                ->first();

            if ($adicional) {
                $adicional->cantidad += $request->cantidad;
                $adicional->precio += $precioTotal;
                $adicional->fechacaduca = $licencia->fechacaduca;
                $adicional->save();
            } else {
                $adicional = Adicionales::create([
                    'numerocontrato' => $request->numerocontrato,
                    'tipo_adicional' => $request->tipo_adicional,
                    'tipo_licencia' => $esPC ? 1 : 2,
                    'cantidad' => $request->cantidad,
                    'precio' => $precioTotal,
                    'fechainicia' => now()->format('Ymd'),
                    'fechacaduca' => $licencia->fechacaduca,
                    'periodo' => $periodo
                ]);
            }

            $licencia->increment($campo, $request->cantidad);

            if (!$esPC) {
                $servidor = Servidores::find($licencia->sis_servidoresid);
                if ($servidor) {
                    $this->externalServerService->updateLicense($servidor, $licencia->toArray());
                }
            }

            LogService::crear('Adicional API', $request->all());
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Adicional procesado correctamente',
                'licencia' => $licencia->fresh(),
                'adicional' => $adicional->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
