<?php

// File: App\Exports\Sp2dExport.php
// [PERUBAHAN DILAKUKAN DI FILE INI]

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{
    Exportable,
    FromCollection,
    ShouldAutoSize,
    WithHeadings,
    WithStyles,
    WithEvents,
    WithTitle
};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\{Alignment, Border};
use Maatwebsite\Excel\Events\{AfterSheet, BeforeSheet};

class Sp2dExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles, WithEvents
{

    use Exportable;

    protected Collection $data;
    protected array $totals;
    protected string $date;

    public function __construct(Collection $data, array $totals, string $date)
    {
        $this->data = $data;
        $this->totals = $totals;
        $this->date = $date;
    }

    public function title(): string
    {
        return Carbon::parse($this->date)->format('d-m-Y');
    }

    public function collection(): Collection
    {
        // [PERBAIKAN] Mengembalikan angka murni (float) agar bisa diformat di Excel.
        // Hapus 'Rp' dan number_format().
        return $this->data->map(function ($item, $key) {
            $jumlah_potongan = $item->ppn + $item->pph_21 + $item->pph_22 + $item->pph_23 + $item->pph_4;

            return [
                'No' => $key + 1,
                'Tanggal SP2D' => Carbon::parse($item->tanggal_sp2d)->format('d-m-Y'),
                'Nomor SP2D' => ' ' . $item->nomor_sp2d, // Spasi untuk mencegah format angka otomatis
                'Jenis SP2D' => $item->jenis_sp2d,
                'Penerima' => $item->penerima->nama_penerima ?? 'N/A',
                'Instansi' => $item->instansi->nama_instansi ?? 'N/A',
                'Bruto' => (float)$item->brutto,
                'PPN' => (float)$item->ppn,
                'PPH 21' => (float)$item->pph_21,
                'PPH 22' => (float)$item->pph_22,
                'PPH 23' => (float)$item->pph_23,
                'PPH 4' => (float)$item->pph_4,
                'Jumlah Potongan' => (float)$jumlah_potongan,
                'Netto' => (float)$item->netto,
                'No BG' => $item->no_bg,
                'No Rekening' => ' ' . ($item->penerima->no_rek ?? 'N/A'), // Spasi untuk mencegah format angka otomatis
            ];
        });
    }


    public function headings(): array
    {
        // Mendefinisikan HANYA header tabel. Posisinya akan diatur di event.
        return [
            [],
            [],
            [
                'No', 'Tanggal SP2D', 'Nomor SP2D', 'Jenis SP2D', 'Nama CV/Penerima',
                'Nama Instansi', 'Bruto', 'PPN', 'PPH 21', 'PPH 22', 'PPH 23',
                'PPH 4', 'Jumlah Potongan', 'Netto', 'No BG', 'No Rekening',
            ],
            []
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Style untuk header di BARIS 4
        return [
            4 => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setTitle($this->title());

                // Judul Utama di baris 1-2
                $sheet->mergeCells("A1:P2");
                $sheet->setCellValue("A1", "REALISASI PENCAIRAN DANA SP2D NON GAJI TAHUN ANGGARAN " . date('Y'));
                $sheet->getStyle("A1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Menyisipkan baris kosong di baris ke-3
                $sheet->insertNewRowBefore(3, 1);
            },

            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Menyisipkan baris kosong di baris ke-5
                $sheet->insertNewRowBefore(5, 1);

                $highestDataRow = $sheet->getHighestDataRow();
                $currencyFormat = '"Rp"#,##0.00';

                $sheet->getColumnDimension('A')->setWidth(5);
                // Terapkan format mata uang ke kolom data (G sampai N) dari BARIS 6
                $sheet->getStyle("G6:N{$highestDataRow}")->getNumberFormat()->setFormatCode($currencyFormat);
                
                // Summary Rows
                $today = Carbon::parse($this->date);
                $yesterday = $today->copy()->subDay();
                $summaryStartRow = $highestDataRow + 2;
                $summaryRowsData = [
                    ['label' => 'Jumlah Tanggal ' . $today->format('d-m-Y'), 'data' => $this->totals['today'] ?? []],
                    ['label' => 'Jumlah sebelumnya ' . $yesterday->format('d-m-Y'), 'data' => $this->totals['yesterday'] ?? []],
                    ['label' => 'Jumlah s/d Tanggal ' . $today->format('d-m-Y'), 'data' => $this->totals['combined'] ?? []],
                ];

                $summaryRowCount = 0;
                foreach ($summaryRowsData as $row) {
                    if (empty($row['data'])) continue;

                    $currentRow = $summaryStartRow + $summaryRowCount;
                    $sheet->mergeCells("A{$currentRow}:F{$currentRow}");
                    $sheet->setCellValue("A{$currentRow}", $row['label']);

                    $sheet->getStyle("A{$currentRow}")->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                    ]);

                    // [PERBAIKAN] Mengisi sel total dengan angka murni
                    $col = 'G';
                    foreach (['brutto','ppn','pph_21','pph_22','pph_23','pph_4','jumlah_potongan','netto'] as $key) {
                        $sheet->setCellValue("{$col}{$currentRow}", $row['data'][$key] ?? 0);
                        $col++;
                    }
                    // Terapkan style dan format ke seluruh baris total
                    $sheet->getStyle("G{$currentRow}:N{$currentRow}")->getNumberFormat()->setFormatCode($currencyFormat);
                    $sheet->getStyle("G{$currentRow}:N{$currentRow}")->getFont()->setBold(true);

                    $summaryRowCount++;
                }

                $borderStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ]
                    ]
                ];

                // Sesuaikan border dengan layout baru
                $sheet->getStyle("A4:P4")->applyFromArray($borderStyle); // Header
                $sheet->getStyle("A6:P{$highestDataRow}")->applyFromArray($borderStyle); // Data

                if ($summaryRowCount > 0) {
                    $lastSummaryRow = $summaryStartRow + $summaryRowCount - 1;
                    $sheet->getStyle("A{$summaryStartRow}:P{$lastSummaryRow}")->applyFromArray($borderStyle);
                }

                $ttdStartRow = ($summaryRowCount > 0 ? ($summaryStartRow + $summaryRowCount - 1) : $highestDataRow) + 3;

                $sheet->mergeCells("M{$ttdStartRow}:P{$ttdStartRow}");
                $sheet->setCellValue("M{$ttdStartRow}", 'KEPALA UPTD KAS DAERAH');

                $sheet->mergeCells("M" . ($ttdStartRow + 4) . ":P" . ($ttdStartRow + 4));
                $sheet->setCellValue("M" . ($ttdStartRow + 4), 'RM. Surya Utama Murad');

                $sheet->mergeCells("M" . ($ttdStartRow + 5) . ":P" . ($ttdStartRow + 5));
                $sheet->setCellValue("M" . ($ttdStartRow + 5), 'NIP. 198302122009031001');

                $sheet->getStyle("M{$ttdStartRow}:P" . ($ttdStartRow + 5))->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            },
        ];
    }
}
