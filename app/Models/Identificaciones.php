<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Identificaciones extends Model
{
    use HasFactory;

    protected $connection = 'identificaciones';
    protected $table = 'identificaciones';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];
}
