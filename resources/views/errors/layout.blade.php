<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') - @yield('title')</title>
    <style>
        body { margin: 0; font-family: ui-sans-serif, system-ui, sans-serif; background: #f3f4f6; color: #1f2937; }
        .wrap { min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1rem; text-align: center; }
        .code { font-size: 4rem; font-weight: 700; color: #4f46e5; margin: 0; }
        h1 { font-size: 1.5rem; margin: 0.5rem 0 0; }
        p { max-width: 28rem; color: #4b5563; font-size: 0.9rem; margin: 0.5rem 0 0; }
        a { display: inline-block; margin-top: 1.5rem; padding: 0.6rem 1.2rem; background: #1f2937; color: #fff; border-radius: 0.375rem; text-decoration: none; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; }
        a:hover { background: #374151; }
    </style>
</head>
<body>
    <div class="wrap">
        <p class="code">@yield('code')</p>
        <h1>@yield('title')</h1>
        <p>@yield('message')</p>
        <a href="{{ url('/') }}">Kembali ke Beranda</a>
    </div>
</body>
</html>