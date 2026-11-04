<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Label QR Barang - {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; }
        @page { margin: 10mm; }
        body { margin: 0; font-family: ui-sans-serif, system-ui, sans-serif; background: #f3f4f6; color: #1f2937; }
        .toolbar { position: sticky; top: 0; z-index: 10; display: flex; flex-wrap: wrap; align-items: center; gap: 8px; padding: 12px 16px; background: #ffffff; border-bottom: 1px solid #e5e7eb; }
        .toolbar input, .toolbar select { padding: 7px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; }
        .btn { display: inline-block; padding: 8px 14px; border: 0; border-radius: 6px; background: #1f2937; color: #ffffff; font-size: 12px; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; text-decoration: none; cursor: pointer; }
        .btn.light { background: #ffffff; color: #374151; border: 1px solid #d1d5db; }
        .info { margin-left: auto; font-size: 12px; color: #6b7280; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 10px; padding: 16px; }
        .label { background: #ffffff; border: 1px dashed #9ca3af; border-radius: 6px; padding: 10px; text-align: center; break-inside: avoid; page-break-inside: avoid; }
        .label img { width: 130px; height: 130px; }
        .label .name { margin-top: 6px; font-size: 12px; font-weight: 700; line-height: 1.3; }
        .label .code { margin-top: 2px; font-family: ui-monospace, Consolas, monospace; font-size: 13px; letter-spacing: .04em; }
        .label .cat { margin-top: 2px; font-size: 10px; color: #6b7280; }
        .empty { padding: 40px 16px; text-align: center; color: #6b7280; }
        @media print {
            body { background: #ffffff; }
            .toolbar { display: none; }
            .grid { padding: 0; gap: 8px; grid-template-columns: repeat(4, 1fr); }
        }
    </style>
</head>
<body>
    <form method="GET" action="{{ route('admin.items.labels') }}" class="toolbar">
        <a href="{{ route('admin.items.index') }}" class="btn light">Kembali</a>

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