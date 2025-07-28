<?php

namespace App\Exports;

use App\Models\SP2D;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class Sp2dMingguanExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles, WithEvents
{
    protected $minggu;
    protected $bulan;
    protected $tahun;
    protected $totals = [];

    public function __construct($minggu, $bulan, $tahun)
    {
        $this->minggu = $minggu;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection(): Collection
    {
        $startOfMonth = Carbon::createFromDate($this->tahun, $this->bulan, 1);
        $startDate = $startOfMonth->copy()->addWeeks($this->minggu - 1)->startOfWeek();
        $endDate = $startDate->copy()->endOfWeek();

        $data = SP2D::with(['penerima', 'instansi'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $this->totals = [
            'brutto' => $data->sum('brutto'),
            'ppn' => $data->sum('ppn'),
            'pph_21' => $data->sum('pph_21'),
            'pph_22' => $data->sum('pph_22'),
            'pph_23' => $data->sum('pph_23'),
            'pph_4' => $data->sum('pph_4'),
            'netto' => $data->sum('netto'),
            'pfk' => $data->sum(
                fn($item) => ($item->ppn + $item->pph_21 + $item->pph_22 + $item->pph_23 + $item->pph_4)
            ),
        ];

        return $data->map(function ($item) {
            return [
                'Tanggal SP2D' => Carbon::parse($item->tanggal_sp2d)->format('d-m-Y'),
                'Nomor SP2D' => $item->nomor_sp2d,
                'Jenis SP2D' => $item->jenis_sp2d,
                'Instansi' => $item->instansi->nama_instansi ?? '-',
                'Penerima' => $item->penerima->nama_penerima ?? '-',
                'No Rekening' => $item->penerima->no_rek ?? '-',
                'Bruto' => $item->brutto,
                'PPN' => $item->ppn,
                'PPH 21' => $item->pph_21,
                'PPH 22' => $item->pph_22,
                'PPH 23' => $item->pph_23,
                'PPH 4' => $item->pph_4,
                'Netto' => $item->netto,
                'No BG' => $item->no_bg,
                'Tanggal Berkas Masuk' => Carbon::parse($item->created_at)->format('d-m-Y'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            [
                'Tanggal SP2D',
                'Nomor SP2D',
                'Jenis SP2D',
                'Nama CV/Penerima',
                'No Rekening',
                'Nama Instansi',
                'Bruto',
                'PPN',
                'PPH 21',
                'PPH 22',
                'PPH 23',
                'PPH 4',
                'Netto',
                'No BG',
                'Tanggal Berkas Masuk'
            ],
            [] // Baris kedua kosong buat di-merge nanti
        ];
    }

    public function title(): string
    {
        $carbon = Carbon::create($this->tahun, $this->bulan, 1);
        $namaBulan = $carbon->translatedFormat('F');
        return "Minggu ke-{$this->minggu} {$namaBulan} {$this->tahun}";
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => 'center', 'vertical' => 'center']],
            2 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => 'center', 'vertical' => 'center']],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $currencyFormat = '"Rp"#,##0.00';

                foreach (range('A', 'O') as $col) {
                    $sheet->mergeCells("{$col}1:{$col}2");
                    $sheet->getStyle("{$col}1")->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    ]);
                }

                $sheet->getStyle("F3:M{$highestRow}")
                    ->getNumberFormat()->setFormatCode($currencyFormat);

                $rowTotal = $highestRow + 2;
                $sheet->setCellValue("F{$rowTotal}", 'Total Keseluruhan');
                $sheet->setCellValue("G{$rowTotal}", $this->totals['brutto'] ?? 0);
                $sheet->setCellValue("H{$rowTotal}", $this->totals['ppn'] ?? 0);
                $sheet->setCellValue("I{$rowTotal}", $this->totals['pph_21'] ?? 0);
                $sheet->setCellValue("J{$rowTotal}", $this->totals['pph_22'] ?? 0);
                $sheet->setCellValue("K{$rowTotal}", $this->totals['pph_23'] ?? 0);
                $sheet->setCellValue("L{$rowTotal}", $this->totals['pph_4'] ?? 0);
                $sheet->setCellValue("M{$rowTotal}", $this->totals['netto'] ?? 0);

                foreach (range('F', 'M') as $col) {
                    $sheet->getStyle("{$col}{$rowTotal}")->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => 'center'],
                    ]);
                    $sheet->getStyle("{$col}{$rowTotal}")
                        ->getNumberFormat()->setFormatCode($currencyFormat);
                }

                // PFK
                $pfkRow = $rowTotal + 2;
                $sheet->setCellValue("F{$pfkRow}", 'Total PFK');
                $sheet->setCellValue("G{$pfkRow}", $this->totals['pfk'] ?? 0);
                $sheet->getStyle("F{$pfkRow}:G{$pfkRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                ]);
                $sheet->getStyle("G{$pfkRow}")
                    ->getNumberFormat()->setFormatCode($currencyFormat);
            },
        ];
    }
}
