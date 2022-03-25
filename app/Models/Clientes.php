<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    use HasFactory;
    protected $table = 'sis_clientes';
    protected $primaryKey = 'sis_clientesid';
    public $timestamps = false;
    protected $guarded = [];
}
