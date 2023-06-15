<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Licenciasvps extends Model
{
    use HasFactory;
    protected $table = 'sis_licencias_vps';
    protected $primaryKey = 'sis_licenciasid';
    public $timestamps = false;
    protected $guarded = [];
}
