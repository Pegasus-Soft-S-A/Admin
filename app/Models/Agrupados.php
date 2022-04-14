<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agrupados extends Model
{
    use HasFactory;
    protected $table = 'sis_agrupados';
    protected $primaryKey = 'sis_agrupadosid';
    public $timestamps = false;
    protected $guarded = [];
}
