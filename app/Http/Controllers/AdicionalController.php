<?php

namespace App\Http\Controllers;

use App\Models\Adicionales;
use App\Models\Licencias;
use App\Models\Licenciasweb;
use App\Models\Servidores;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdicionalController extends Controller
{
    public function agregarAdicional(Request $request)
    {
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

            // Verificar que la licencia existe
            $licencia = $this->obtenerLicencia($request->numerocontrato);
            if (!$licencia) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'No se encontró licencia']);
            }

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

    // ✅ Validación dinámica usando configuración
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

    // ✅ Validación específica para PC
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

    // ✅ Validación específica para Web
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

    // ✅ Detectar si es licencia PC
    private function esLicenciaPC($licencia)
    {
        return $licencia instanceof Licencias ||
            (isset($licencia->tipo_licencia) && $licencia->tipo_licencia == 2);
    }

    // ✅ Detectar módulo PC dinámicamente
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

    // ✅ Calcular precio usando configuración dinámica
    private function calcularPrecioDinamico($request, $licencia)
    {
        $tiposConfig = config('sistema.tipos_adicionales', []);
        $tipoConfig = $tiposConfig[$request->tipo_adicional] ?? null;

        if (!$tipoConfig || !isset($tipoConfig['precios'])) {
            return 0;
        }

        $precios = $tipoConfig['precios'];

        if ($this->esLicenciaPC($licencia)) {
            return $this->calcularPrecioPC($precios, $request, $licencia);
        } else {
            return $this->calcularPrecioWeb($precios, $request, $licencia);
        }
    }

    // ✅ Calcular precio para PC
    private function calcularPrecioPC($precios, $request, $licencia)
    {
        if ($request->tipo_adicional == 4) { // Usuarios nube
            $configNube = config('sistema.productos.pc.modulos_principales.nube.precios', []);
            $tipoNube = ($licencia->tipo_nube ?? 1) == 1 ? 'prime' : 'contaplus';
            $nivelNube = 'nivel' . ($licencia->nivel_nube ?? 1);
            $periodo = $request->periodo == 1 ? 'mensual' : 'anual';

            return $configNube[$tipoNube][$nivelNube][$periodo] ?? 0;
        }

        // Otros tipos PC
        $periodo = $request->periodo == 1 ? 'mensual' : 'anual';
        return $precios['pc'][$periodo] ?? 0;
    }

    // ✅ Calcular precio para Web
    private function calcularPrecioWeb($precios, $request, $licencia)
    {
        $periodo = $request->periodo == 1 ? 'mensual' : 'anual';
        return $precios['web'][$periodo] ?? 0;
    }

    // ✅ Actualizar campo con lógica diferenciada PC/Web
    private function actualizarCampoLicenciaDinamico($licencia, $tipoAdicional, $cantidadAgregar)
    {
        // Obtener mapeo dinámicamente de configuración
        $tiposConfig = config('sistema.tipos_adicionales', []);
        $campo = $tiposConfig[$tipoAdicional]['campo_licencia'] ?? null;

        if (!$campo) {
            throw new \Exception("No se encontró mapeo para el tipo adicional {$tipoAdicional}");
        }

        // Actualizar campo localmente
        $valorActual = $licencia->{$campo} ?? 0;
        $nuevoValor = $valorActual + $cantidadAgregar;

        $licencia->{$campo} = $nuevoValor;
        $licencia->save();

        Log::info("Campo {$campo} actualizado localmente", [
            'licencia' => $licencia->numerocontrato,
            'tipo_licencia' => $this->esLicenciaPC($licencia) ? 'PC' : 'Web',
            'valor_anterior' => $valorActual,
            'cantidad_agregada' => $cantidadAgregar,
            'nuevo_valor' => $nuevoValor
        ]);

        // ✅ SOLO actualizar servidor externo si es licencia WEB
        if (!$this->esLicenciaPC($licencia)) {
            $this->actualizarServidorExterno($licencia);
        }

        // Log del servicio (aplica para ambos tipos)
        LogService::modificar(
            $this->esLicenciaPC($licencia) ? 'Licencia PC' : 'Licencia Web',
            $licencia->toArray()
        );
    }

    // ✅ Actualizar servidor externo (SOLO PARA WEB)
    private function actualizarServidorExterno($licencia)
    {
        try {
            $servidor = Servidores::where('sis_servidoresid', $licencia->sis_servidoresid)->first();

            if (!$servidor) {
                throw new \Exception("No se encontró el servidor para la licencia web");
            }

            $urlActualizar = $servidor->dominio . '/registros/editar_licencia';

            $respuesta = Http::withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8',
                'User-Agent' => 'Sistema-Licencias/1.0'
            ])
                ->withOptions([
                    'verify' => false,
                    'timeout' => 30,
                    'connect_timeout' => 10
                ])
                ->post($urlActualizar, $licencia->toArray());

            if (!$respuesta->successful()) {
                throw new \Exception("Error HTTP {$respuesta->status()}: {$respuesta->body()}");
            }

            $datosRespuesta = $respuesta->json();
            if (!isset($datosRespuesta['licencias'])) {
                throw new \Exception("Respuesta inválida del servidor externo: " . $respuesta->body());
            }

            Log::info("Servidor externo actualizado correctamente", [
                'licencia' => $licencia->numerocontrato,
                'servidor' => $servidor->descripcion
            ]);

        } catch (\Exception $e) {
            // Re-lanzar para provocar rollback completo
            throw new \Exception("Error de conectividad con el servidor externo: " . $e->getMessage());
        }
    }

    // ✅ Obtener campos actualizados dinámicamente
    private function obtenerCamposActualizados($licencia)
    {
        $tiposConfig = config('sistema.tipos_adicionales', []);
        $campos = [];

        foreach ($tiposConfig as $tipoId => $config) {
            if (isset($config['campo_licencia'])) {
                $campo = $config['campo_licencia'];
                $campos[$campo] = $licencia->{$campo} ?? 0;
            }
        }

        return $campos;
    }

    // ✅ Métodos auxiliares...
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

            return $adicionalExistente;
        } else {
            // CREATE: Crear nuevo registro
            return Adicionales::create([
                'numerocontrato' => $request->numerocontrato,
                'fechainicia' => $request->fechainicia,
                'fechacaduca' => date('Ymd', strtotime($request->fechacaduca)),
                'tipo_adicional' => $request->tipo_adicional,
                'tipo_licencia' => $request->tipo_licencia,
                'periodo' => $request->periodo,
                'cantidad' => $request->cantidad,
                'precio' => $precioTotal,
            ]);
        }
    }

    // ✅ Resto de métodos (obtenerAdicionales) permanecen igual...
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
}
