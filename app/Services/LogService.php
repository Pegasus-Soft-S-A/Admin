<?php

namespace App\Services;

use App\Models\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LogService
{
    public static function crear(string $pantalla, mixed $detalle = null): void
    {
        static::registrar($pantalla, 'Crear', $detalle);
    }

    public static function modificar(string $pantalla, mixed $detalle = null): void
    {
        static::registrar($pantalla, 'Modificar', $detalle);
    }

    public static function eliminar(string $pantalla, mixed $detalle = null): void
    {
        static::registrar($pantalla, 'Eliminar', $detalle);
    }

    public static function registrar(string $pantalla, string $operacion, mixed $detalle = null, ?User $usuario = null): void
    {
        $nombreUsuario = $usuario ? $usuario->nombres : (Auth::user()?->nombres ?? 'API_SYSTEM');

        Log::create([
            'usuario' => $nombreUsuario,
            'pantalla' => $pantalla,
            'tipooperacion' => $operacion,
            'fecha' => now(),
            'detalle' => is_string($detalle) ? $detalle : json_encode($detalle),
        ]);
    }
}
