<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    use HasFactory;

    protected $table = 'log_aktivitas';

    const UPDATED_AT = null;
    
    protected $fillable = [
        'id_sp2d',
        'id_user',
        'aktivitas',
        'created_at',
    ];
}
