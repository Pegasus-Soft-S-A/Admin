<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use \Rackbeat\UIAvatars\HasAvatar;

    protected $table = 'sis_distribuidores_usuarios';
    protected $primaryKey = 'sis_distribuidores_usuariosid';
    public $timestamps = false;
    protected $rememberTokenName = false;

    protected $hidden = [
        'contrasena'
    ];

    //Especificar que el campo clave es contrasena
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    public function getAvatarNameKey()
    {
        return 'nombres';
    }

    public function puedeCrearClientes()
    {
        $permitidos = config('tipos_usuario.permisos.crear_clientes', []);
        return in_array($this->tipo, $permitidos);
    }

    public function puedeGuardarClientes()
    {
        $permitidos = config('tipos_usuario.permisos.guardar_clientes', []);
        return in_array($this->tipo, $permitidos);
    }

    public function puedeCrearWeb()
    {
        $permitidos = config('tipos_usuario.permisos.crear_web', []);
        return in_array($this->tipo, $permitidos);
    }

    public function puedeResetearClaveWeb()
    {
        $permitidos = config('tipos_usuario.permisos.resetear_clave_web', []);
        return in_array($this->tipo, $permitidos);
    }

    public function puedeCrearPc()
    {
        $permitidos = config('tipos_usuario.permisos.crear_pc', []);
        return in_array($this->tipo, $permitidos);
    }

    public function puedeCrearVps()
    {
        $permitidos = config('tipos_usuario.permisos.crear_vps', []);
        return in_array($this->tipo, $permitidos);
    }
}
