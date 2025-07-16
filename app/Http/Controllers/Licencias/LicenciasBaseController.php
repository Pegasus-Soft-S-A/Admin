<?php

namespace App\Http\Controllers\Licencias;

use App\Http\Controllers\Controller;
use App\Models\Adicionales;
use App\Models\Clientes;
use App\Services\ExternalServerService;
use App\Services\LogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

abstract class LicenciasBaseController extends Controller
{
    protected ExternalServerService $externalServerService;

    public function __construct(ExternalServerService $externalServerService)
    {
        $this->externalServerService = $externalServerService;
    }

    // Genera un número de contrato único
    protected function generarContrato(): string
    {
        do {
            $numeroContrato = (string)random_int(1000000000, 9999999999);

            $existe = \App\Models\Licencias::where('numerocontrato', $numeroContrato)->exists() ||
                \App\Models\Licenciasweb::where('numerocontrato', $numeroContrato)->exists() ||
                \App\Models\Licenciasvps::where('numerocontrato', $numeroContrato)->exists();
        } while ($existe);

        return $numeroContrato;
    }

    // Verificar dependencias antes de eliminar
    protected function verificarDependencias(string $numerocontrato): int
    {
        return Adicionales::where('numerocontrato', $numerocontrato)->count();
    }

    // Limpiar dependencias al eliminar
    protected function limpiarDependencias(string $numerocontrato): void
    {
        Adicionales::where('numerocontrato', $numerocontrato)->delete();
    }

    // Obtener datos del cliente para emails
    protected function obtenerDatosClienteEmail(int $clienteId): ?object
    {
        return Clientes::select(
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
    }

    // Preparar lista de emails para envío
    protected function prepararEmailsDestinatarios(object $cliente): array
    {
        $emails = explode(", ", $cliente->distribuidor ?? '');

        return array_filter(array_merge($emails, [
            "facturacion@perseo.ec",
            $cliente->vendedor ?? '',
            $cliente->correos ?? '',
            Auth::user()->correo ?? '',
        ]), fn($email) => !empty(trim($email)));
    }

    // Manejar respuesta para requests AJAX y normales
    protected function manejarRespuesta(bool $esAjax, bool $exito, string $mensaje, $datos = null)
    {
        if ($esAjax) {
            return $exito
                ? response()->json(['success' => true, 'message' => $mensaje] + ($datos ?? []))
                : response()->json(['success' => false, 'message' => $mensaje], 500);
        }

        if ($exito) {
            flash($mensaje)->success();
        } else {
            flash($mensaje)->error();
        }

        return back();
    }

    // Verificar permisos basado en configuración
    protected function verificarPermiso(string $categoria, string $permiso): bool
    {
        return puede($categoria, $permiso);
    }

    // Ejecutar creación con transacción y log específico
    protected function ejecutarCreacionConTransaccion(callable $operacion, string $tipoLog, array $datos): mixed
    {
        DB::beginTransaction();

        try {
            $resultado = $operacion();
            LogService::crear($tipoLog, $datos);
            DB::commit();
            return $resultado;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // Ejecutar actualización con transacción y log específico
    protected function ejecutarActualizacionConTransaccion(callable $operacion, string $tipoLog): mixed
    {
        DB::beginTransaction();

        try {
            $resultado = $operacion();

            // Usar los datos DESPUÉS de la actualización
            LogService::modificar($tipoLog, $resultado->toArray());

            DB::commit();
            return $resultado;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // Ejecutar eliminación con transacción y log específico
    protected function ejecutarEliminacionConTransaccion(callable $operacion, string $tipoLog, array $datosOriginales): mixed
    {
        DB::beginTransaction();

        try {
            $resultado = $operacion();
            LogService::eliminar($tipoLog, $datosOriginales);
            DB::commit();
            return $resultado;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
