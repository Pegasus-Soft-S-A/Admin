<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;

class ValidarCorreo implements Rule
{
    private const DOMINIOS_CONFIABLES = ['gmail.com', 'yahoo.com', 'ferrymend.com'];

    private const RAZONES_VALIDAS = [
        'Deliverable',
        'Deliverable, Role',
        'Accept All, Role',
        'Accept All',
        'Unknown'
    ];

    private const RAZONES_REVISAR_DOMINIO = ['Bounce', 'Invalid'];

    public function passes($attribute, $value)
    {
        // Validar con Abstract API primero
        $resultadoAbstract = $this->validarConAbstractAPI($value);
        if ($resultadoAbstract === true) {
            return true;
        }

        // Si Abstract API no confirma como deliverable, validar con Debounce API
        if ($resultadoAbstract === 'formato_valido') {
            return $this->validarConDebounceAPI($value);
        }

        return false;
    }

    private function validarConAbstractAPI(string $email)
    {
        $respuesta = $this->realizarPeticionHTTP(
            'https://emailvalidation.abstractapi.com/v1/',
            ['api_key' => env('API_EMAIL_ABSTRACT'), 'email' => $email]
        );

        if (!$respuesta) {
            return false;
        }

        // Si es directamente deliverable, retornar true
        if (($respuesta['deliverability'] ?? '') === 'DELIVERABLE') {
            return true;
        }

        // Si tiene formato válido, continuar con la siguiente validación
        if (($respuesta['is_valid_format']['value'] ?? false) === true) {
            return 'formato_valido';
        }

        return false;
    }

    private function validarConDebounceAPI(string $email): bool
    {
        $respuesta = $this->realizarPeticionHTTP(
            'https://api.debounce.io/v1/',
            ['email' => $email, 'api' => env('API_EMAIL_DEBOUNCE')]
        );

        if (!$respuesta) {
            return false;
        }

        $reason = $respuesta['debounce']['reason'] ?? '';

        // Verificar razones directamente válidas
        if (in_array($reason, self::RAZONES_VALIDAS)) {
            return true;
        }

        // Para razones que requieren verificación de dominio
        if (in_array($reason, self::RAZONES_REVISAR_DOMINIO)) {
            return $this->esDominioConfiable($email);
        }

        return false;
    }

    private function realizarPeticionHTTP(string $url, array $parametros): ?array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8'
            ])
                ->withOptions(['verify' => false])
                ->get($url, $parametros);

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            // Log del error si es necesario
            return null;
        }
    }

    private function esDominioConfiable(string $email): bool
    {
        $dominio = substr(strrchr($email, "@"), 1);
        return in_array($dominio, self::DOMINIOS_CONFIABLES);
    }

    public function message(): string
    {
        return 'Ingrese un Correo Válido';
    }
}
