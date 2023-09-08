<?php

namespace App\Rules;

use App\Models\Revendedores;
use Illuminate\Contracts\Validation\Rule;

class IdentificacionRevendedor implements Rule
{
    protected $excluir;

    public function __construct($excluir = 0)
    {
        $this->excluir = $excluir;
    }

    public function passes($attribute, $value)
    {
        $identificacionIngresada = substr($value, 0, 10);

        $buscar = Revendedores::whereIn('identificacion', [
            $identificacionIngresada,
            $value,
            $value . '001'
        ]);

        if ($this->excluir != 0) {
            $buscar = $buscar->where('sis_revendedoresid', '<>', $this->excluir);
        }

        $buscar = $buscar->first();

        // Si la identificación no existe o es igual a la ingresada, devuelve verdadero
        if (!$buscar) {
            return true;
        } else {
            return false;
        }
    }

    public function message()
    {
        return 'Su cédula o RUC ya se encuentra registrado.';
    }
}
