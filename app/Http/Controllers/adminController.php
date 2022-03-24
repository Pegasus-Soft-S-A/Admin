<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class adminController extends Controller
{

    public function login()
    {
        return view('admin.auth.login');
    }

    public function post_login(Request $request)
    {
        //Validacion
        $request->validate(
            [
                'identificacion' => 'required',
                'contrasena' => 'required',
            ],
            [
                'identificacion.required' => 'Ingrese su cédula o RUC ',
                'contrasena.required' => 'Ingrese su contraseña',
            ],
        );

        //Buscar usuario
        $usuario = User::where('identificacion', $request->identificacion)->first();
        if ($usuario) {
            if ($usuario->contrasena === encrypt_openssl($request->contrasena, "Perseo1232*")) {
                //Si tiene puesto check para recordar 
                if ($request->has('recordar')) {
                    Auth::login($usuario, true);
                } else {
                    Auth::login($usuario, false);
                }
                //Siempre que se hace login es recomendable regenerar las sesiones
                $request->session()->regenerate();
                return redirect()->route('clientes.index');
            } else {
                flash('Usuario o Contraseña Incorrectos')->error();
                return back();
            }
        } else {
            flash('Usuario o Contraseña Incorrectos')->error();
            return back();
        }
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::logout();
            return redirect()->route('login');
        } else {
            //Aqui va el logout del contador
        }
    }
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
