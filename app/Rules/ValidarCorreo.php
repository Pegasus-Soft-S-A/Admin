<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;

class ValidarCorreo implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Obtener claves de API desde las variables de entorno
        $abstractApiKey = env('API_EMAIL_ABSTRACT');
        $debounceApiKey = env('API_EMAIL_DEBOUNCE');

        // Construir URL para la primera API (Abstract API)
        $urlAbstract = 'https://emailvalidation.abstractapi.com/v1/?api_key=' . $abstractApiKey . '&email=' . $value;

        // Realizamos la primera petición a Abstract API
        $correo = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false])
            ->withOptions(["verify" => false])
            ->get($urlAbstract)
            ->json();

        // Validamos que el resultado sea el esperado antes de acceder a índices
        if (isset($correo['deliverability']) && $correo['deliverability'] == "DELIVERABLE") {
            return 1;
        }

        // Validamos el formato del correo antes de continuar
        if (isset($correo['is_valid_format']['value']) && $correo['is_valid_format']['value'] == true) {
            // Construir URL para la segunda API (Debounce API)
            $urlDebounce = 'https://api.debounce.io/v1/?email=' . rawurlencode($value) . '&api=' . $debounceApiKey;

            // Realizamos la segunda petición a Debounce API
            $correoDebounce = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false])
                ->withOptions(["verify" => false])
                ->get($urlDebounce)
                ->json();

            // Verificamos que la estructura esperada exista en la respuesta
            if (isset($correoDebounce['debounce']['reason'])) {
                $reason = $correoDebounce['debounce']['reason'];

                // Revisamos los valores válidos
                if (in_array($reason, ["Deliverable", "Deliverable, Role", "Accept All, Role", "Accept All", "Unknown"])) {
                    return 1;
                }

                // Si es "Bounce" o "Invalid", validamos contra dominios confiables
                if ($reason == "Bounce" || $reason == "Invalid") {
                    $dominios_validos = ['gmail.com', 'yahoo.com', 'ferrymend.com']; // Dominios válidos
                    $dominio_correo = substr(strrchr($value, "@"), 1); // Extraer el dominio del correo

                    if (in_array($dominio_correo, $dominios_validos)) {
                        return 1; // Consideramos válido si pertenece a un dominio confiable
                    }
                }
            }

            // Si no hay razón válida, devolvemos 0
            return 0;
        }

        // Si no tiene formato válido, devolvemos 0
        return 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Ingrese un Correo Válido';
    }
}
