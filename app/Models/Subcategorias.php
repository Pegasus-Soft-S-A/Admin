<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategorias extends Model
{
    use HasFactory;
    protected $table = 'sis_subcategorias';
    protected $primaryKey = 'sis_subcategoriasid';
    public $timestamps = false;
    protected $guarded = [];
}
