<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class adminController extends Controller
{

    public function cambiarMenu(Request $request)
    {
        Session::put('menu', $request->estado);
    }

    public function recuperarPost(Request $request)
    {
        $url = 'https://www.perseo.app/datos/datos_consulta';
        $resultado = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8', 'Usuario' => 'Identificaciones', 'Clave' => 'IdentiFicaciones1232*', 'verify' => false,])
            ->withOptions(["verify" => false])
            ->post($url, ['identificacion' => $request->cedula])
            ->json();
        return $resultado;
    }
}
