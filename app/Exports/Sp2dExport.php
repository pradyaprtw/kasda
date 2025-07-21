<?php

namespace App\Exports;

use App\Models\SP2D;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class Sp2dExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents, WithTitle
{
    use Exportable;

    protected $created_at;

    public function __construct(string $created_at)
    {
        $this->created_at = $created_at;
    }

    public function title(): string
    {
        return Carbon::parse($this->created_at)->format('d-m-Y');
    }

    public function query()
    {
        return SP2D::query()->with(['penerima', 'instansi'])
            ->whereDate('created_at', $this->created_at);
    }

    public function headings(): array
    {
        return [
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
            'Netto',
        ];
    }

    public function map($sp2d): array
    {
        return [
            ' ' . $sp2d->nomor_sp2d,
            Carbon::parse($sp2d->tanggal_sp2d)->format('d-m-Y'),
            $sp2d->jenis_sp2d,
            $sp2d->keterangan,
            $sp2d->penerima->nama_penerima ?? 'N/A',
            $sp2d->instansi->nama_instansi ?? 'N/A',
            'Rp' . number_format((float)$sp2d->brutto, 2, ',', '.'),
            'Rp' . number_format((float)$sp2d->ppn, 2, ',', '.'),
            'Rp' . number_format((float)$sp2d->pph_21, 2, ',', '.'),
            'Rp' . number_format((float)$sp2d->pph_22, 2, ',', '.'),
            'Rp' . number_format((float)$sp2d->pph_23, 2, ',', '.'),
            'Rp' . number_format((float)$sp2d->pph_4, 2, ',', '.'),
            $sp2d->no_bg,
            ' ' . $sp2d->no_rek,
            'Rp' . number_format((float)$sp2d->netto, 2, ',', '.')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => '000000']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFC0CB'],
            ],
        ]);

        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:O' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow() + 3;

                // ==================== TOTAL NETTO ====================
                $sheet->setCellValue('N' . $lastRow, 'Total Netto Keseluruhan');
                $totalNetto = SP2D::whereDate('created_at', $this->created_at)->sum('netto');
                $sheet->setCellValue('O' . $lastRow, 'Rp' . number_format((float)$totalNetto, 2, ',', '.'));
                $sheet->getStyle('N' . $lastRow . ':O' . $lastRow)->applyFromArray($this->totalStyle());

                // ==================== TOTAL BRUTO ====================
                $lastRow += 3;
                $sheet->setCellValue('G' . $lastRow, 'Total Bruto Keseluruhan');
                $totalBruto = SP2D::whereDate('created_at', $this->created_at)->sum('brutto');
                $sheet->setCellValue('H' . $lastRow, 'Rp' . number_format((float)$totalBruto, 2, ',', '.'));
                $sheet->getStyle('G' . $lastRow . ':H' . $lastRow)->applyFromArray($this->totalStyle());

                // ==================== TOTAL BRUTO PFK ====================
                $lastRow += 3;
                $sheet->setCellValue('G' . $lastRow, 'Total PFK');
                $totalBrutoPFK = SP2D::whereDate('created_at', $this->created_at)
                    ->where('jenis_sp2d', 'PFK')
                    ->sum('brutto');
                $sheet->setCellValue('H' . $lastRow, 'Rp' . number_format((float)$totalBrutoPFK, 2, ',', '.'));
                $sheet->getStyle('G' . $lastRow . ':H' . $lastRow)->applyFromArray($this->totalStyle());

                // ==================== TOTAL PPN ====================
                $lastRow += 3;
                $sheet->setCellValue('I' . $lastRow, 'Total PPN Keseluruhan');
                $totalPPN = SP2D::whereDate('created_at', $this->created_at)->sum('ppn');
                $sheet->setCellValue('J' . $lastRow, 'Rp' . number_format((float)$totalPPN, 2, ',', '.'));
                $sheet->getStyle('I' . $lastRow . ':J' . $lastRow)->applyFromArray($this->totalStyle());

                // ==================== TOTAL PPH 21 ====================
                $lastRow += 2;
                $sheet->setCellValue('I' . $lastRow, 'Total PPH 21 Keseluruhan');
                $totalPPH21 = SP2D::whereDate('created_at', $this->created_at)->sum('pph_21');
                $sheet->setCellValue('J' . $lastRow, 'Rp' . number_format((float)$totalPPH21, 2, ',', '.'));
                $sheet->getStyle('I' . $lastRow . ':J' . $lastRow)->applyFromArray($this->totalStyle());

                // ==================== TOTAL PPH 22 ====================
                $lastRow++;
                $sheet->setCellValue('I' . $lastRow, 'Total PPH 22 Keseluruhan');
                $totalPPH22 = SP2D::whereDate('created_at', $this->created_at)->sum('pph_22');
                $sheet->setCellValue('J' . $lastRow, 'Rp' . number_format((float)$totalPPH22, 2, ',', '.'));
                $sheet->getStyle('I' . $lastRow . ':J' . $lastRow)->applyFromArray($this->totalStyle());

                // ==================== TOTAL PPH 23 ====================
                $lastRow++;
                $sheet->setCellValue('I' . $lastRow, 'Total PPH 23 Keseluruhan');
                $totalPPH23 = SP2D::whereDate('created_at', $this->created_at)->sum('pph_23');
                $sheet->setCellValue('J' . $lastRow, 'Rp' . number_format((float)$totalPPH23, 2, ',', '.'));
                $sheet->getStyle('I' . $lastRow . ':J' . $lastRow)->applyFromArray($this->totalStyle());

                // ==================== TOTAL PPH 4 ====================
                $lastRow++;
                $sheet->setCellValue('I' . $lastRow, 'Total PPH 4 Keseluruhan');
                $totalPPH4 = SP2D::whereDate('created_at', $this->created_at)->sum('pph_4');
                $sheet->setCellValue('J' . $lastRow, 'Rp' . number_format((float)$totalPPH4, 2, ',', '.'));
                $sheet->getStyle('I' . $lastRow . ':J' . $lastRow)->applyFromArray($this->totalStyle());
            },
        ];
    }

    private function totalStyle(): array
    {
        return [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE4E1'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
    }
}
