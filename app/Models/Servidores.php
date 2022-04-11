<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servidores extends Model
{
    use HasFactory;
    protected $table = 'sis_servidores';
    protected $primaryKey = 'sis_servidoresid';
    public $timestamps = false;
    protected $guarded = [];
}
