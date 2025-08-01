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
 * Class Sp2dExportGaji
 *
 * Handles the export of SP2D Gaji data to an Excel sheet.
 * The sheet construction, including headers, data, summaries, and styling,
 * is managed through event listeners for better control and clarity.
 */
class Sp2dExportGaji implements FromCollection, WithTitle, ShouldAutoSize, WithEvents
{
    use Exportable;

    protected Collection $data;
    protected array $totals;
    protected string $date;

    // Constants for styling and layout
    private const START_ROW = 5; // Data starts at this row
    private const CURRENCY_FORMAT = '"Rp"#,##0.00';
    private const HEADER_COLUMNS = [
        'No', 'Tanggal SP2D', 'Nomor SP2D', 'Jenis SP2D', 'Nama CV/Penerima',
        'Nama Instansi', 'Bruto', 'IWP (8%)', 'IWP (1%)', 'PPH 21', 'Jumlah Potongan', 'Netto', 'No BG', 'No Rekening',
    ];

    /**
     * Sp2dExportGaji constructor.
     *
     * @param Collection $data The main data for the report.
     * @param array $totals An array containing totals for 'today', 'yesterday', and 'combined'.
     * @param string $date The reference date for the report.
     */
    public function __construct(Collection $data, array $totals, string $date)
    {
        $this->data = $data;
        $this->totals = $totals;
        $this->date = $date;
    }

    /**
     * Sets the title of the worksheet.
     *
     * @return string
     */
    public function title(): string
    {
        return Carbon::parse($this->date)->format('d-m-Y');
    }

    /**
     * Prepares the data collection for the export.
     * Values are cast to their appropriate types for correct formatting in Excel.
     *
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->data->map(function ($item, $key) {
            $jumlah_potongan = $item->pph_21 + $item->iuran_wajib + $item->iuran_wajib_2;

            return [
                'No' => $key + 1,
                'Tanggal SP2D' => Carbon::parse($item->tanggal_sp2d)->format('d-m-Y'),
                'Nomor SP2D' => ' ' . $item->nomor_sp2d,
                'Jenis SP2D' => $item->jenis_sp2d,
                'Penerima' => $item->penerima->nama_penerima ?? 'N/A',
                'Instansi' => $item->instansi->nama_instansi ?? 'N/A',
                'Bruto' => (float)$item->brutto,
                'IWP (8%)' => (float)$item->iuran_wajib,
                'IWP (1%)' => (float)$item->iuran_wajib_2,
                'PPH 21' => (float)$item->pph_21,
                'Jumlah Potongan' => (float)$jumlah_potongan,
                'Netto' => (float)$item->netto,
                'No BG' => $item->no_bg,
                'No Rekening' => ' ' . ($item->penerima->no_rek ?? 'N/A'),
            ];
        });
    }

    /**
     * Registers events to manipulate the worksheet before and after data insertion.
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            // Event before the data is written to the sheet
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $this->drawHeader($sheet);
            },

            // Event after the data is written
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastDataRow = self::START_ROW + $this->data->count() - 1;
                $lastCol = $sheet->getHighestColumn();

                // Apply formatting to the data area
                $this->formatDataCells($sheet, $lastDataRow, $lastCol);

                // Add summary rows below the data
                $summaryStartRow = $lastDataRow + 2;
                $lastSummaryRow = $this->drawSummaryBlock($sheet, $summaryStartRow);

                // Add signature block at the end
                $signatureStartRow = ($lastSummaryRow ?: $lastDataRow) + 3;
                $this->drawSignatureBlock($sheet, $signatureStartRow);
            },
        ];
    }

    /**
     * Draws the main title and table headers.
     *
     * @param Worksheet $sheet
     */
    private function drawHeader(Worksheet $sheet): void
    {
        // Main Title
        $sheet->mergeCells("A1:N2");
        $sheet->setCellValue("A1", "REALISASI PENCAIRAN SP2D GAJI TAHUN ANGGARAN " . date('Y'));
        $sheet->getStyle("A1")->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Table Headers
        $headerRow = self::START_ROW - 1;
        $sheet->fromArray(self::HEADER_COLUMNS, null, "A{$headerRow}");
        $sheet->getStyle("A{$headerRow}:N{$headerRow}")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);
        $sheet->getColumnDimension('A')->setWidth(5);
    }

    /**
     * Applies formatting to the data cells.
     *
     * @param Worksheet $sheet
     * @param int $lastDataRow
     * @param string $lastCol
     */
    private function formatDataCells(Worksheet $sheet, int $lastDataRow, string $lastCol): void
    {
        if ($this->data->isEmpty()) {
            return;
        }

        // Apply currency format to relevant columns
        $sheet->getStyle("G" . self::START_ROW . ":L{$lastDataRow}")
            ->getNumberFormat()
            ->setFormatCode(self::CURRENCY_FORMAT);

        // Apply borders to the entire data table
        $sheet->getStyle("A" . self::START_ROW . ":{$lastCol}{$lastDataRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
    }

    /**
     * Draws the summary rows below the main data.
     *
     * @param Worksheet $sheet
     * @param int $startRow
     * @return int The last row number where a summary was written, or 0 if none.
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
            $sheet->mergeCells("A{$currentRow}:F{$currentRow}");
            $sheet->setCellValue("A{$currentRow}", $row['label']);
            $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true);

            // Data
            $sheet->setCellValue("G{$currentRow}", (float)($row['data']['brutto'] ?? 0));
            $sheet->setCellValue("H{$currentRow}", (float)($row['data']['iuran_wajib'] ?? 0));
            $sheet->setCellValue("I{$currentRow}", (float)($row['data']['iuran_wajib_2'] ?? 0));
            $sheet->setCellValue("J{$currentRow}", (float)($row['data']['pph_21'] ?? 0));
            $sheet->setCellValue("K{$currentRow}", (float)($row['data']['jumlah_potongan'] ?? 0));
            $sheet->setCellValue("L{$currentRow}", (float)($row['data']['netto'] ?? 0));

            // Style for the entire summary row
            $range = "A{$currentRow}:N{$currentRow}";
            $sheet->getStyle($range)->getFont()->setBold(true);
            $sheet->getStyle("G{$currentRow}:L{$currentRow}")->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
            $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $currentRow++;
        }

        return $currentRow > $startRow ? $currentRow - 1 : 0;
    }

    /**
     * Draws the signature block at the end of the sheet.
     *
     * @param Worksheet $sheet
     * @param int $startRow
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
