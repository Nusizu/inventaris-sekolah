<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Peminjaman Barang</title>
    <style>
        @page { margin: 28px 32px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; }
        h1 { font-size: 16px; margin: 0 0 6px; }
        .meta { color: #4b5563; line-height: 1.6; margin-bottom: 10px; }
        .summary { margin-bottom: 12px; padding: 6px 8px; background: #f3f4f6; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1f2937; color: #ffffff; text-align: left; padding: 5px 6px; font-size: 9px; }
        td { padding: 4px 6px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        .center { text-align: center; }
        .code { color: #6b7280; font-size: 8px; }
        .empty { padding: 14px; text-align: center; color: #6b7280; }
        .footer { margin-top: 14px; color: #6b7280; font-size: 9px; }
    </style>
</head>
<body>
    <h1>Laporan Peminjaman Barang</h1>

    <div class="meta">
        Periode: <strong>{{ $periodLabel }}</strong><br>
        Status: <strong>{{ $statusLabel }}</strong>
    </div>

    <div class="summary">
        Total peminjaman: <strong>{{ number_format($totalLoans, 0, ',', '.') }}</strong>
        &nbsp;|&nbsp; Total unit: <strong>{{ number_format($totalUnits, 0, ',', '.') }}</strong>
        @foreach (\App\Models\Loan::STATUSES as $status)
            &nbsp;|&nbsp; {{ ucfirst($status) }}: <strong>{{ number_format($perStatus->get($status, 0), 0, ',', '.') }}</strong>
        @endforeach
    </div>

    <table>
        <thead>
            <tr>
                <th class="center" style="width: 4%;">No</th>
                <th style="width: 14%;">Peminjam</th>
                <th style="width: 18%;">Barang</th>
                <th style="width: 11%;">Kategori</th>
                <th class="center" style="width: 6%;">Jumlah</th>
                <th style="width: 10%;">Tanggal Pinjam</th>
                <th style="width: 10%;">Jatuh Tempo</th>
                <th style="width: 10%;">Tanggal Kembali</th>
                <th style="width: 9%;">Status</th>
                <th style="width: 8%;">Kondisi Kembali</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($loans as $loan)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $loan->user->name }}</td>
                    <td>
                        {{ $loan->item->name }}<br>
                        <span class="code">{{ $loan->item->code }}</span>
                    </td>
                    <td>{{ $loan->item->category->name }}</td>
                    <td class="center">{{ number_format($loan->quantity, 0, ',', '.') }}</td>
                    <td>{{ $loan->loan_date->translatedFormat('j M Y') }}</td>
                    <td>{{ $loan->due_date->translatedFormat('j M Y') }}</td>
                    <td>{{ $loan->returnLog ? $loan->returnLog->returned_at->translatedFormat('j M Y') : '-' }}</td>
                    <td>{{ ucfirst($loan->status) }}</td>
                    <td>{{ $loan->returnLog ? ucfirst($loan->returnLog->condition_on_return) : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="empty">Tidak ada data peminjaman untuk filter ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ $printedAt }} oleh {{ $printedBy }}.
    </div>
</body>
</html>