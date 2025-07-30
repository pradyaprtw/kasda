<?php

// File: App\Exports\Sp2dRekapExport.php

namespace App\Exports;

use App\Models\SP2D;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{
    Exportable,
    FromCollection,
    ShouldAutoSize,
    WithHeadings,
    WithStyles,
    WithEvents,
    WithTitle,
    WithColumnFormatting,
    WithStrictNullComparison
};
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\{Alignment, Border};
use Maatwebsite\Excel\Events\{AfterSheet, BeforeSheet};

class Sp2dRekapExport implements
    FromCollection,
    WithHeadings,
    WithTitle,
    ShouldAutoSize,
    WithStyles,
    WithEvents,
    WithColumnFormatting,
    WithStrictNullComparison
{
    use Exportable;

    protected $data;
    protected $bulan;
    protected $tahun;
    protected $totals;

    // Cache untuk data yang sudah diproses
    protected static $processedData = [];

    public function __construct($data, $bulan, $tahun, $totals)
    {
        $this->data = $data;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->totals = $totals;
    }

    public function collection(): Collection
    {
        // Gunakan cache jika data sudah diproses sebelumnya
        $cacheKey = md5(serialize($this->data->pluck('id')->toArray()));

        if (isset(self::$processedData[$cacheKey])) {
            return self::$processedData[$cacheKey];
        }

        // Proses data dalam batch dan gunakan array alih-alih collection untuk performa
        $processedData = [];
        $counter = 1;

        foreach ($this->data as $item) {
            // Pastikan semua field ada dan tidak null
            $tanggalSp2d = $item->tanggal_sp2d ? Carbon::parse($item->tanggal_sp2d)->format('d-m-Y') : 'N/A';
            $nomorSp2d = $item->nomor_sp2d ? ' ' . $item->nomor_sp2d : 'N/A';
            $jenisSp2d = $item->jenis_sp2d ?? 'N/A';
            $namaPenerima = optional($item->penerima)->nama_penerima ?? 'N/A';
            $namaInstansi = optional($item->instansi)->nama_instansi ?? 'N/A';
            $noBg = $item->no_bg ?? 'N/A';
            $noRek = optional($item->penerima)->no_rek ? ' ' . $item->penerima->no_rek : 'N/A';
            $tanggalMasuk = $item->created_at ? Carbon::parse($item->created_at)->format('d-m-Y') : 'N/A';

            // Hitung potongan dengan validasi null
            $ppn = (float)($item->ppn ?? 0);
            $pph21 = (float)($item->pph_21 ?? 0);
            $pph22 = (float)($item->pph_22 ?? 0);
            $pph23 = (float)($item->pph_23 ?? 0);
            $pph4 = (float)($item->pph_4 ?? 0);
            $jumlahPotongan = $ppn + $pph21 + $pph22 + $pph23 + $pph4;

            $processedData[] = [
                $counter++,
                $tanggalSp2d,
                $nomorSp2d,
                $jenisSp2d,
                $namaPenerima,
                $namaInstansi,
                (float)($item->brutto ?? 0),
                $ppn,
                $pph21,
                $pph22,
                $pph23,
                $pph4,
                $jumlahPotongan,
                (float)($item->netto ?? 0),
                $noBg,
                $noRek,
                $tanggalMasuk,
            ];
        }

        $collection = collect($processedData);

        // Simpan ke cache
        self::$processedData[$cacheKey] = $collection;

        return $collection;
    }

    public function headings(): array
    {
        return [
            [], // Row 1 - empty
            [

            ], // Row 2 - empty  
            [   // Row 3 - headers
                'No',
                'Tanggal SP2D',
                'Nomor SP2D',
                'Jenis SP2D',
                'Nama CV/Penerima',
                'Nama Instansi',
                'Bruto',
                'PPN',
                'PPH 21',
                'PPH 22',
                'PPH 23',
                'PPH 4',
                'Jumlah Potongan',
                'Netto',
                'No BG',
                'No Rekening',
                'Tanggal Berkas Masuk',
            ],
            [] // Row 4 - empty
        ];
    }

    public function title(): string
    {
        try {
            return Carbon::create()->month($this->bulan)->locale('id')->translatedFormat('F') . " {$this->tahun}";
        } catch (\Exception $e) {
            return "Bulan {$this->bulan} {$this->tahun}";
        }
    }

    // Pre-define column formatting untuk performa lebih baik
    public function columnFormats(): array
    {
        return [
            'G' => 'Rp#,##0',
            'H' => 'Rp#,##0',
            'I' => 'Rp#,##0',
            'J' => 'Rp#,##0',
            'K' => 'Rp#,##0',
            'L' => 'Rp#,##0',
            'M' => 'Rp#,##0',
            'N' => 'Rp#,##0',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            4 => [ // Row 3 adalah header sebenarnya (setelah 2 row kosong)
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

                // Set properties sekali saja
                $sheet->mergeCells("A1:Q2");
                $sheet->setCellValue("A1", "REALISASI PENCAIRAN DANA SP2D NON GAJI TAHUN ANGGARAN " . $this->tahun);

                // Apply style dalam satu operasi
                $sheet->getStyle("A1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
            },

            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Insert row setelah header untuk spacing
                $sheet->insertNewRowBefore(4, 1); // Insert setelah row header (row 3)
                $highestDataRow = $sheet->getHighestDataRow();

                // Set column width
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(15);
                $sheet->getColumnDimension('C')->setWidth(20);

                $summaryStartRow = $highestDataRow + 2;
                $currentRow = $summaryStartRow;

                try {
                    $currentMonthName = Carbon::create()->month($this->bulan)->locale('id')->translatedFormat('F');
                    $previousMonthName = Carbon::create()->month($this->bulan - 1)->locale('id')->translatedFormat('F');
                } catch (\Exception $e) {
                    $currentMonthName = "Bulan {$this->bulan}";
                    $previousMonthName = "Bulan " . ($this->bulan - 1);
                }

                // Prepare all summary data at once
                $summaryRows = [
                    'Jumlah ' . $currentMonthName . " {$this->tahun}" => $this->totals['current'] ?? [],
                    'Jumlah bulan sebelumnya (' . $previousMonthName . ')' => $this->totals['previous'] ?? [],
                    'Jumlah ' . $currentMonthName . ' + ' . $previousMonthName => $this->totals['cumulative'] ?? [],
                ];

                // Batch operations untuk summary
                foreach ($summaryRows as $label => $data) {
                    $this->addSummaryRow($sheet, $currentRow, $label, $data);
                    $currentRow++;
                }

                // PFK Total
                $pfkTotal = $this->totals['current']['pfk'] ?? 0;
                $this->addSummaryRow($sheet, $currentRow, "Jumlah PFK Bulan Ini", ['brutto' => $pfkTotal]);
                $currentRow++;

                $lastSummaryRow = $currentRow - 1;

                // Apply borders dalam satu operasi
                $this->applyBorders($sheet, $highestDataRow, $summaryStartRow, $lastSummaryRow);

                // Add signature section
                $this->addSignatureSection($sheet, $lastSummaryRow);
            },
        ];
    }

    // Helper method untuk menambah summary row
    private function addSummaryRow($sheet, $row, $label, $data)
    {
        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->setCellValue("A{$row}", $label);

        // Set multiple values at once dengan validasi
        $values = [
            "G{$row}" => (float)($data['brutto'] ?? 0),
            "H{$row}" => (float)($data['ppn'] ?? 0),
            "I{$row}" => (float)($data['pph_21'] ?? 0),
            "J{$row}" => (float)($data['pph_22'] ?? 0),
            "K{$row}" => (float)($data['pph_23'] ?? 0),
            "L{$row}" => (float)($data['pph_4'] ?? 0),
            "M{$row}" => (float)($data['jumlah_potongan'] ?? 0),
            "N{$row}" => (float)($data['netto'] ?? 0),
        ];

        foreach ($values as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Apply styles dalam satu operasi
        $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        // Apply currency format untuk kolom angka
        $sheet->getStyle("G{$row}:N{$row}")->getNumberFormat()
            ->setFormatCode('"Rp"#,##0.00');
    }

    // Helper method untuk apply borders
    private function applyBorders($sheet, $highestDataRow, $summaryStartRow, $lastSummaryRow)
    {
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ]
        ];

        // Apply borders in batches
        $ranges = [
            "A5:Q5", // Header (row 3)
            "A5:Q{$highestDataRow}", // Data (mulai dari row 5 karena ada insert row)
            "A{$summaryStartRow}:Q{$lastSummaryRow}" // Summary
        ];

        foreach ($ranges as $range) {
            try {
                $sheet->getStyle($range)->applyFromArray($borderStyle);
            } catch (\Exception $e) {
                // Skip jika range tidak valid
                continue;
            }
        }
    }

    // Helper method untuk signature section
    private function addSignatureSection($sheet, $lastSummaryRow)
    {
        $ttdStartRow = $lastSummaryRow + 3;
        $signatureData = [
            [$ttdStartRow, 'KEPALA UPTD KAS DAERAH'],
            [$ttdStartRow + 4, 'RM. Surya Utama Murad'],
            [$ttdStartRow + 5, 'NIP. 198302122009031001']
        ];

        foreach ($signatureData as [$row, $text]) {
            $sheet->mergeCells("M{$row}:Q{$row}");
            $sheet->setCellValue("M{$row}", $text);
        }

        $sheet->getStyle("M{$ttdStartRow}:Q" . ($ttdStartRow + 5))->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
    }

    // Clear cache when needed
    public static function clearCache()
    {
        self::$processedData = [];
    }
}
