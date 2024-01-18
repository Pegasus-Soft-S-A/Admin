<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Links extends Model
{
    use HasFactory;
    protected $table = 'sis_links';
    protected $primaryKey = 'sis_linksid';
    public $timestamps = false;
    protected $guarded = [];
}
