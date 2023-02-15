<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Licenciasweb extends Model
{
    use HasFactory;
    protected $table = 'sis_licencias_web';
    protected $primaryKey = 'sis_licencias_webid';
    public $timestamps = false;
    protected $guarded = [];
}
