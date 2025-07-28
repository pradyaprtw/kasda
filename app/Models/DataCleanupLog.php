<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataCleanupLog extends Model
{
    protected $table = 'data_cleanup_logs';

    public $timestamps = true;

    protected $dates = ['deleted_before', 'deleted_at'];
}
