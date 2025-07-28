<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SP2D extends Model
{
    use HasFactory;

    protected $table = 'sp2d';
    protected $fillable = [
        'nomor_sp2d',
        'tanggal_sp2d',
        'jenis_sp2d',
        'keterangan',
        'id_penerima',
        'id_instansi',
        'brutto',
        'ppn',
        'pph_21',
        'pph_22',
        'pph_23',
        'pph_4',
        'no_bg',
        'id_user',
        'waktu_sesuai',
    ];

    protected $casts = [
        'waktu_sesuai' => 'datetime',
        'brutto' => 'decimal:2',
        'ppn' => 'decimal:2',
        'pph_21' => 'decimal:2',
        'pph_22' => 'decimal:2',
        'pph_23' => 'decimal:2',
        'pph_4' => 'decimal:2',
        'netto' => 'decimal:2',
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function penerima()
    {
        return $this->belongsTo(Penerima::class, 'id_penerima');
    }

    public function instansi()
    {
        return $this->belongsTo(Instansi::class, 'id_instansi');
    }

    public function logs()
    {
        return $this->hasMany(Logs::class, 'id_sp2d');
    }
}
