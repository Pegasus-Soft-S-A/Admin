<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revendedores extends Model
{
    use HasFactory;
    protected $table = 'sis_revendedores';
    protected $primaryKey = 'sis_revendedoresid';
    public $timestamps = false;
    protected $guarded = [];
}
