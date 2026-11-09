<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') - @yield('title')</title>

    {{--
        CSS sengaja ditulis langsung di sini (bukan lewat Vite) supaya halaman error
        tetap tampil walau aset belum di-build atau aplikasi sedang bermasalah.
        Warna mengikuti tema aplikasi: netral (zinc) + aksen teal.
    --}}
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #fafafa; color: #18181b; -webkit-font-smoothing: antialiased;
               font-family: "Inter Variable", Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
        .wrap { min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 24px 16px; text-align: center; }
        .logo { width: 48px; height: 48px; border-radius: 12px; background: #18181b; color: #fff; display: flex; align-items: center; justify-content: center; margin-bottom: 28px; }
        .logo svg { width: 24px; height: 24px; }
        .code { margin: 0; font-size: 72px; line-height: 1; font-weight: 700; letter-spacing: -0.03em; color: #0f766e; }
        h1 { margin: 16px 0 0; font-size: 24px; line-height: 1.3; font-weight: 600; letter-spacing: -0.01em; }
        .message { max-width: 28rem; margin: 8px 0 0; color: #71717a; font-size: 15px; line-height: 1.6; }
        .actions { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; margin-top: 28px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 9px 16px; border-radius: 8px; border: 1px solid #27272a; background: #27272a; color: #fff;
               font: inherit; font-size: 14px; font-weight: 500; text-decoration: none; cursor: pointer; transition: background-color .15s; }
        .btn:hover { background: #3f3f46; }
        .btn.light { background: #fff; color: #3f3f46; border-color: #d4d4d8; }
        .btn.light:hover { background: #f4f4f5; }
        .btn:focus-visible { outline: 2px solid #0d9488; outline-offset: 2px; }
    </style>
</head>
<body>
    <main class="wrap">
        <div class="logo" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
            </svg>
        </div>

        <p class="code">@yield('code')</p>
        <h1>@yield('title')</h1>
        <p class="message">@yield('message')</p>

        <div class="actions">
            <a class="btn" href="{{ url('/') }}">Kembali ke Beranda</a>
            <button type="button" class="btn light" onclick="history.back()">Halaman Sebelumnya</button>
        </div>
    </main>
</body>
</html>