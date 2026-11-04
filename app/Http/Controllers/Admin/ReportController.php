<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoanReportRequest;
use App\Models\Loan;
use App\Services\ActivityLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    /** Batas baris PDF agar proses tidak kehabisan memori. */
    private const PDF_MAX_ROWS = 500;

    public function loans(LoanReportRequest $request)
    {
        Loan::markOverdue();

        $filters = $request->validated();

        $summary = $this->filtered($filters)
            ->selectRaw('status, COUNT(*) as total, COALESCE(SUM(quantity), 0) as units')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $loans = $this->filtered($filters)
            ->with(['user', 'item.category', 'returnLog'])
            ->orderBy('loan_date')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.loans', [
            'loans' => $loans,
            'summary' => $summary,
            'totalLoans' => (int) $summary->sum('total'),
            'totalUnits' => (int) $summary->sum('units'),
        ]);
    }

    public function loansPdf(LoanReportRequest $request)
    {
        Loan::markOverdue();

        $filters = $request->validated();

        if ($this->filtered($filters)->count() > self::PDF_MAX_ROWS) {
            return back()->with(
                'error',
                'Data terlalu banyak untuk PDF (maksimal ' . self::PDF_MAX_ROWS
                    . ' baris). Persempit periode atau gunakan Excel.'
            );
        }

        $data = $this->reportData($this->reportRows($filters), $filters);

        $pdf = Pdf::loadView('reports.loans-pdf', $data)->setPaper('a4', 'landscape');

        ActivityLogger::log('export_report', 'Mengunduh laporan peminjaman (PDF), periode ' . $data['periodLabel']);

        return $pdf->download($this->filename('pdf'));
    }

    public function loansExcel(LoanReportRequest $request)
    {
        Loan::markOverdue();

        $filters = $request->validated();
        $loans = $this->reportRows($filters);
        $data = $this->reportData($loans, $filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Peminjaman');

        $sheet->setCellValue('A1', 'Laporan Peminjaman Barang');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->setCellValue('A2', 'Periode: ' . $data['periodLabel']);
        $sheet->setCellValue('A3', 'Status: ' . $data['statusLabel']);
        $sheet->setCellValue(
            'A4',
            'Total peminjaman: ' . number_format($data['totalLoans'], 0, ',', '.')
                . ' | Total unit: ' . number_format($data['totalUnits'], 0, ',', '.')
        );
        $sheet->setCellValue('A5', 'Dicetak: ' . $data['printedAt'] . ' oleh ' . $data['printedBy']);

        $headerRow = 7;
        $sheet->fromArray([
            'No', 'Peminjam', 'Kode Barang', 'Nama Barang', 'Kategori', 'Jumlah',
            'Tanggal Pinjam', 'Jatuh Tempo', 'Tanggal Kembali', 'Status', 'Kondisi Saat Kembali',
        ], null, 'A' . $headerRow);

        $sheet->getStyle("A{$headerRow}:K{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F2937']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $row = $headerRow + 1;

        foreach ($loans as $index => $loan) {
            $returnLog = $loan->returnLog;

            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $loan->user->name);
            $sheet->setCellValue("C{$row}", $loan->item->code);
            $sheet->setCellValue("D{$row}", $loan->item->name);
            $sheet->setCellValue("E{$row}", $loan->item->category->name);
            $sheet->setCellValue("F{$row}", $loan->quantity);
            $sheet->setCellValue("G{$row}", ExcelDate::PHPToExcel($loan->loan_date));
            $sheet->setCellValue("H{$row}", ExcelDate::PHPToExcel($loan->due_date));
            $sheet->setCellValue("I{$row}", $returnLog ? ExcelDate::PHPToExcel($returnLog->returned_at) : '');
            $sheet->setCellValue("J{$row}", ucfirst($loan->status));
            $sheet->setCellValue("K{$row}", $returnLog ? ucfirst($returnLog->condition_on_return) : '');

            $row++;
        }

        $firstDataRow = $headerRow + 1;
        $lastDataRow = $row - 1;

        if ($lastDataRow >= $firstDataRow) {
            $sheet->getStyle("G{$firstDataRow}:I{$lastDataRow}")
                ->getNumberFormat()->setFormatCode('dd mmm yyyy');
            $sheet->getStyle("A{$firstDataRow}:A{$lastDataRow}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$firstDataRow}:F{$lastDataRow}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Baris total berupa rumus
            $sheet->setCellValue("E{$row}", 'Total');
            $sheet->setCellValue("F{$row}", "=SUM(F{$firstDataRow}:F{$lastDataRow})");
            $sheet->getStyle("E{$row}:F{$row}")->getFont()->setBold(true);
            $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->setAutoFilter("A{$headerRow}:K{$lastDataRow}");
        }

        $sheet->freezePane('A' . $firstDataRow);

        // Kolom A dibuat tetap agar judul di baris atas tidak melebarkannya
        $sheet->getColumnDimension('A')->setWidth(6);
        foreach (range('B', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        ActivityLogger::log('export_report', 'Mengunduh laporan peminjaman (Excel), periode ' . $data['periodLabel']);

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $this->filename('xlsx'), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /** Filter yang dipakai bersama oleh pratinjau, PDF, dan Excel. */
    private function filtered(array $filters): Builder
    {
        return Loan::query()
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('loan_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('loan_date', '<=', $date))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status));
    }

    private function reportRows(array $filters): Collection
    {
        return $this->filtered($filters)
            ->with(['user', 'item.category', 'returnLog'])
            ->orderBy('loan_date')
            ->orderBy('id')
            ->get();
    }

    private function reportData(Collection $loans, array $filters): array
    {
        return [
            'loans' => $loans,
            'totalLoans' => $loans->count(),
            'totalUnits' => (int) $loans->sum('quantity'),
            'perStatus' => $loans->countBy('status'),
            'periodLabel' => $this->periodLabel($filters),
            'statusLabel' => ! empty($filters['status']) ? ucfirst($filters['status']) : 'Semua status',
            'printedAt' => now()->translatedFormat('j M Y, H:i'),
            'printedBy' => auth()->user()->name,
        ];
    }

    private function periodLabel(array $filters): string
    {
        $from = ! empty($filters['date_from'])
            ? Carbon::createFromFormat('Y-m-d', $filters['date_from'])->translatedFormat('j M Y')
            : null;

        $to = ! empty($filters['date_to'])
            ? Carbon::createFromFormat('Y-m-d', $filters['date_to'])->translatedFormat('j M Y')
            : null;

        return match (true) {
            $from !== null && $to !== null => "{$from} s.d. {$to}",
            $from !== null => "Mulai {$from}",
            $to !== null => "Sampai {$to}",
            default => 'Semua periode',
        };
    }

    private function filename(string $extension): string
    {
        return 'laporan-peminjaman-' . now()->format('Ymd-His') . '.' . $extension;
    }
}