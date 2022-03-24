<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuarios extends Model
{
    use HasFactory;
    protected $table = 'sis_distribuidores_usuarios';
    protected $primaryKey = 'sis_distribuidores_usuariosid';
    public $timestamps = false;
    protected $guarded = [];
}
