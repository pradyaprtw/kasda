<?php

namespace App\Exports;

use App\Models\SP2D;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping; 

class Sp2dExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    /**
     * @var int
     */
    protected $created_at;

    public function __construct(string $created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * Menyiapkan query ke database.
     * Data akan difilter berdasarkan tanggal yang diterima.
     */
    public function query()
    {

        return SP2D::query()->with(['penerima', 'instansi'])
            ->whereDate('created_at', $this->created_at);
    }

    /**
     * Menentukan baris header untuk file Excel.
     */
    public function headings(): array
    {
        // Header untuk file Excel
        // Pastikan nama-nama ini sesuai dengan kolom yang ada di database SP2D

        return[
                'Nomor SP2D',
                'Tanggal SP2D',
                'Jenis SP2D',
                'Keterangan',
                'Nama CV/Penerima',
                'Nama Instansi',
                'Bruto',
                'PPN',
                'PPH 21',
                'PPH 22',
                'PPH 23',
                'PPH 4',
                'No BG',
                'Rekening',
                'Netto'
        ];
    }

    /**
     * Memetakan data dari setiap baris.
     * @param mixed $sp2d
     */
    public function map($sp2d): array
    {
        return [
           ' ' . $sp2d->nomor_sp2d,
            \Carbon\Carbon::parse($sp2d->tanggal_sp2d)->format('d-m-Y'),           
            $sp2d->jenis_sp2d,
            $sp2d->keterangan,
            $sp2d->penerima->nama_penerima ?? 'N/A', // ambil nama penerima, jika tidak ada tampilkan 'N/A'
            $sp2d->instansi->nama_instansi ?? 'N/A', // ambil nama instansi, jika tidak ada tampilkan 'N/A'
           ' ' . $sp2d->brutto,
            $sp2d->ppn,
            $sp2d->pph_21,
            $sp2d->pph_22,
            $sp2d->pph_23,
            $sp2d->pph_4,
            $sp2d->no_bg,
           ' ' . $sp2d->no_rek,
           ' ' . $sp2d->netto,
        ];  
    }

    
}
