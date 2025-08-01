<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Class PajakSp2dExport
 *
 * Handles the export of SP2D tax data to an Excel sheet.
 */
class PajakSp2dExport implements FromCollection, WithTitle, ShouldAutoSize, WithEvents
{
    use Exportable;

    protected Collection $data;
    protected array $totals;
    protected string $date;

    // Constants for styling and layout
    private const START_ROW = 5;
    private const CURRENCY_FORMAT = '"Rp"#,##0.00';
    // [MODIFIED] Added 'Jenis SP2D' column
    private const HEADER_COLUMNS = [
        'No', 'Tanggal SP2D', 'Nomor SP2D', 'Jenis SP2D', 'Bruto', 'PPN', 'PPH 21',
        'PPH 22', 'PPH 23', 'PPH 4', 'Jumlah Potongan', 'Netto',
        'Nama CV/Penerima', 'No NPWP',
    ];

    /**
     * PajakSp2dExport constructor.
     *
     * @param Collection $data The main data for the report.
     * @param array $totals An array containing totals for 'today', 'yesterday', and 'combined'.
     * @param string $date The reference date for the report title.
     */
    public function __construct(Collection $data, array $totals, string $date)
    {
        $this->data = $data;
        $this->totals = $totals;
        $this->date = $date;
    }

    /**
     * Sets the title of the worksheet.
     */
    public function title(): string
    {
        return 'Pajak SP2D ' . Carbon::parse($this->date)->format('d-m-Y');
    }

    /**
     * Prepares the data collection for the export.
     * [MODIFIED] Added 'Jenis SP2D' to the collection map.
     */
    public function collection(): Collection
    {
        return $this->data->map(function ($item, $key) {
            $jumlah_potongan = $item->ppn + $item->pph_21 + $item->pph_22 + $item->pph_23 + $item->pph_4;

            return [
                'No' => $key + 1,
                'Tanggal SP2D' => Carbon::parse($item->tanggal_sp2d)->format('d-m-Y'),
                'Nomor SP2D' => ' ' . $item->nomor_sp2d,
                'Jenis SP2D' => $item->jenis_sp2d, // Added this line
                'Bruto' => (float)$item->brutto,
                'PPN' => (float)$item->ppn,
                'PPH 21' => (float)$item->pph_21,
                'PPH 22' => (float)$item->pph_22,
                'PPH 23' => (float)$item->pph_23,
                'PPH 4' => (float)$item->pph_4,
                'Jumlah Potongan' => (float)$jumlah_potongan,
                'Netto' => (float)$item->netto,
                'Penerima' => $item->penerima->nama_penerima ?? 'N/A',
                'No NPWP' => '', // Empty column as requested
            ];
        });
    }

    /**
     * Registers events to manipulate the worksheet.
     */
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $this->drawHeader($sheet);
            },
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastDataRow = self::START_ROW + $this->data->count() - 1;
                $lastCol = $sheet->getHighestColumn();

                $this->formatDataCells($sheet, $lastDataRow, $lastCol);

                $summaryStartRow = $lastDataRow + 2;
                $lastSummaryRow = $this->drawSummaryBlock($sheet, $summaryStartRow);

                $signatureStartRow = ($lastSummaryRow ?: $lastDataRow) + 3;
                $this->drawSignatureBlock($sheet, $signatureStartRow);
            },
        ];
    }

    /**
     * Draws the main title and table headers.
     * [MODIFIED] Adjusted column ranges for the new column.
     */
    private function drawHeader(Worksheet $sheet): void
    {
        $lastCol = 'N'; // Adjusted from M to N
        $sheet->mergeCells("A1:{$lastCol}2");
        $sheet->setCellValue("A1", "REALISASI PENCAIRAN PAJAK SP2D TAHUN ANGGARAN " . date('Y'));
        $sheet->getStyle("A1")->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $headerRow = self::START_ROW - 1;
        $sheet->fromArray(self::HEADER_COLUMNS, null, "A{$headerRow}");
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $sheet->getColumnDimension('A')->setWidth(5);
    }

    /**
     * Applies formatting to the data cells.
     * [MODIFIED] Adjusted column ranges for the new column.
     */
    private function formatDataCells(Worksheet $sheet, int $lastDataRow, string $lastCol): void
    {
        if ($this->data->isEmpty()) {
            return;
        }
        // Apply currency format to E through L
        $sheet->getStyle("E" . self::START_ROW . ":L{$lastDataRow}")
            ->getNumberFormat()
            ->setFormatCode(self::CURRENCY_FORMAT);

        $sheet->getStyle("A" . self::START_ROW . ":{$lastCol}{$lastDataRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
    }

    /**
     * Draws the summary rows below the main data.
     * [MODIFIED] Adjusted column ranges for the new column.
     */
    private function drawSummaryBlock(Worksheet $sheet, int $startRow): int
    {
        $today = Carbon::parse($this->date);
        $yesterday = $today->copy()->subDay();
        $summaryRowsData = [
            ['label' => 'Jumlah Tanggal ' . $today->format('d-m-Y'), 'data' => $this->totals['today'] ?? []],
            ['label' => 'Jumlah sebelumnya ' . $yesterday->format('d-m-Y'), 'data' => $this->totals['yesterday'] ?? []],
            ['label' => 'Jumlah s/d Tanggal ' . $today->format('d-m-Y'), 'data' => $this->totals['combined'] ?? []],
        ];

        $currentRow = $startRow;
        foreach ($summaryRowsData as $row) {
            if (empty($row['data'])) {
                continue;
            }

            // Label
            $sheet->mergeCells("A{$currentRow}:D{$currentRow}");
            $sheet->setCellValue("A{$currentRow}", $row['label']);
            
            // Data
            $sheet->setCellValue("E{$currentRow}", (float)($row['data']['brutto'] ?? 0));
            $sheet->setCellValue("F{$currentRow}", (float)($row['data']['ppn'] ?? 0));
            $sheet->setCellValue("G{$currentRow}", (float)($row['data']['pph_21'] ?? 0));
            $sheet->setCellValue("H{$currentRow}", (float)($row['data']['pph_22'] ?? 0));
            $sheet->setCellValue("I{$currentRow}", (float)($row['data']['pph_23'] ?? 0));
            $sheet->setCellValue("J{$currentRow}", (float)($row['data']['pph_4'] ?? 0));
            $sheet->setCellValue("K{$currentRow}", (float)($row['data']['jumlah_potongan'] ?? 0));
            $sheet->setCellValue("L{$currentRow}", (float)($row['data']['netto'] ?? 0));

            // Style for the entire summary row
            $range = "A{$currentRow}:N{$currentRow}";
            $sheet->getStyle($range)->getFont()->setBold(true);
            $sheet->getStyle("E{$currentRow}:L{$currentRow}")->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
            $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $currentRow++;
        }

        return $currentRow > $startRow ? $currentRow - 1 : 0;
    }

    /**
     * Draws the signature block at the end of the sheet.
     * [MODIFIED] Adjusted column ranges for the new column.
     */
    private function drawSignatureBlock(Worksheet $sheet, int $startRow): void
    {
        $sheet->setCellValue("M{$startRow}", 'KEPALA UPTD KAS DAERAH');
        $sheet->setCellValue("M" . ($startRow + 4), 'RM. Surya Utama Murad');
        $sheet->setCellValue("M" . ($startRow + 5), 'NIP. 198302122009031001');

        $styleRange = "M{$startRow}:N" . ($startRow + 5);
        $sheet->getStyle($styleRange)->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
    }
}
