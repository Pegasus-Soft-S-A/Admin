<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;

class ValidarCelular implements Rule
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
        $abstractApiKey = env('API_CELULAR_ABSTRACT');
        //consultar api
        $url = 'https://phonevalidation.abstractapi.com/v1/?api_key=' . $abstractApiKey . '&phone=593' . $value;
        $celular = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->get($url)
            ->json();

        if ($celular['valid'] != true) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Ingrese un Celular Válido.';
    }
}
