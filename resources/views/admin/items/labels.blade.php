<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Label QR Barang - {{ config('app.name') }}</title>

    {{-- Halaman cetak berdiri sendiri (tanpa layout aplikasi), jadi CSS-nya ditulis di sini. --}}
    <style>
        * { box-sizing: border-box; }
        @page { margin: 10mm; }
        body { margin: 0; background: #fafafa; color: #18181b; -webkit-font-smoothing: antialiased;
               font-family: "Inter Variable", Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }

        /* Bilah alat (disembunyikan saat dicetak) */
        .toolbar { position: sticky; top: 0; z-index: 10; display: flex; flex-wrap: wrap; align-items: center; gap: 8px; padding: 12px 16px;
                   background: rgba(255, 255, 255, .92); backdrop-filter: blur(6px); border-bottom: 1px solid #e4e4e7; }
        .toolbar input, .toolbar select { padding: 8px 12px; border: 1px solid #d4d4d8; border-radius: 8px; background: #fff; font: inherit; font-size: 14px; color: #18181b; }
        .toolbar input { width: 240px; max-width: 100%; }
        .toolbar input:focus, .toolbar select:focus { outline: none; border-color: #0d9488; box-shadow: 0 0 0 1px #0d9488; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 8px 14px; border: 1px solid #27272a; border-radius: 8px; background: #27272a; color: #fff;
               font: inherit; font-size: 14px; font-weight: 500; text-decoration: none; cursor: pointer; }
        .btn:hover { background: #3f3f46; }
        .btn.light { background: #fff; color: #3f3f46; border-color: #d4d4d8; }
        .btn.light:hover { background: #f4f4f5; }
        .info { margin-left: auto; font-size: 13px; color: #71717a; }
        .info a { color: #0f766e; }

        /* Daftar label */
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 12px; padding: 16px; }
        .label { background: #fff; border: 1px dashed #a1a1aa; border-radius: 8px; padding: 12px 10px; text-align: center; break-inside: avoid; page-break-inside: avoid; }
        .label img { width: 130px; height: 130px; }
        .label .name { margin-top: 8px; font-size: 12px; font-weight: 700; line-height: 1.3; }
        .label .code { margin-top: 2px; font-family: ui-monospace, Consolas, monospace; font-size: 13px; letter-spacing: .04em; }
        .label .cat { margin-top: 2px; font-size: 10px; color: #71717a; }
        .empty { padding: 56px 16px; text-align: center; color: #71717a; }

        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .grid { padding: 0; gap: 8px; grid-template-columns: repeat(4, 1fr); }
            .label { border-radius: 0; }
        }
    </style>
</head>
<body>
    <form method="GET" action="{{ route('admin.items.labels') }}" class="toolbar">
        <a href="{{ route('admin.items.index') }}" class="btn light">&larr; Kembali</a>

        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau kode..." autofocus>

        <select name="category_id">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn light">Tampilkan</button>
        <button type="button" class="btn" onclick="window.print()">Cetak</button>

        <span class="info">
            {{ number_format($labels->count(), 0, ',', '.') }} label
            @if ($selectedOnly)
                (barang terpilih) &middot; <a href="{{ route('admin.items.labels') }}">tampilkan semua</a>
            @endif
            @if ($labels->count() >= $maxLabels)
                &middot; dibatasi {{ $maxLabels }} label, persempit dengan filter
            @endif
        </span>
    </form>

    @if ($labels->isEmpty())
        <p class="empty">Tidak ada barang untuk dicetak.</p>
    @else
        <div class="grid">
            @foreach ($labels as $label)
                <div class="label">
                    <img src="{{ $label['qr'] }}" alt="QR {{ $label['item']->code }}">
                    <div class="name">{{ $label['item']->name }}</div>
                    <div class="code">{{ $label['item']->code }}</div>
                    <div class="cat">{{ $label['item']->category->name }}</div>
                </div>
            @endforeach
        </div>
    @endif
</body>
</html>