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
        'iuran_wajib',
        'iuran_wajib_2',
        'no_bg',
        'id_user',
        'waktu_sesuai',
    ];

    protected $casts = [
        'waktu_sesuai' => 'datetime'
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
