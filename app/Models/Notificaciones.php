<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificaciones extends Model
{
    use HasFactory;
    protected $table = 'sis_notificaciones';
    protected $primaryKey = 'sis_notificacionesid';
    public $timestamps = false;
    protected $guarded = [];
}
