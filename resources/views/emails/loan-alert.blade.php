<!DOCTYPE html>
<html lang="id">
<body style="margin:0;padding:24px;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <div style="max-width:520px;margin:0 auto;background:#ffffff;border-radius:8px;padding:24px;">
        <h2 style="margin:0 0 12px;font-size:18px;color:#4f46e5;">{{ $title }}</h2>
        <p style="margin:0 0 12px;font-size:14px;">Halo, {{ $name }}.</p>
        <p style="margin:0 0 20px;font-size:14px;line-height:1.5;">{{ $body }}</p>
        <a href="{{ $url }}"
           style="display:inline-block;background:#1f2937;color:#ffffff;text-decoration:none;padding:10px 18px;border-radius:6px;font-size:13px;">
            {{ $actionText }}
        </a>
        <p style="margin:24px 0 0;font-size:12px;color:#6b7280;">Pesan otomatis dari {{ config('app.name') }}.</p>
    </div>
</body>
</html>