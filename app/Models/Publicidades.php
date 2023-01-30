<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publicidades extends Model
{
    use HasFactory;
    protected $table = 'sis_publicidades';
    protected $primaryKey = 'sis_publicidadesid';
    public $timestamps = false;
    protected $guarded = [];
}
