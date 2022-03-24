<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;
    protected $table = 'sis_log_sistema';
    protected $primaryKey = 'sis_logs_sistemasid';
    public $timestamps = false;
    protected $guarded = [];
}
