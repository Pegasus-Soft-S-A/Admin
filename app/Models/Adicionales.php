<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adicionales extends Model
{
    protected $table = 'sis_licencias_adicionales';
    protected $primaryKey = 'sis_licencias_adicionalesid';
    public $timestamps = false;
    protected $guarded = [];
}
