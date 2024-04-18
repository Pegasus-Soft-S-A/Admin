<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovilVersion extends Model
{
    protected $table = 'movil_versiones';
    protected $primaryKey = 'movil_versionesid';
    public $timestamps = false;
    protected $guarded = [];
}
