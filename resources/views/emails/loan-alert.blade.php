<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
</head>
{{-- Semua gaya ditulis inline & berbasis tabel karena banyak aplikasi email (mis. Outlook) mengabaikan CSS modern --}}
<body style="margin:0;padding:0;background-color:#fafafa;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fafafa;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;">
                    <tr>
                        <td style="padding:0 4px 16px;font-family:Arial,Helvetica,sans-serif;font-size:14px;font-weight:700;color:#18181b;">
                            <span style="display:inline-block;width:10px;height:10px;margin-right:8px;background-color:#0f766e;border-radius:3px;"></span>{{ config('app.name') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#ffffff;border:1px solid #e4e4e7;border-radius:12px;padding:28px;font-family:Arial,Helvetica,sans-serif;color:#27272a;">
                            <h1 style="margin:0 0 16px;font-size:20px;line-height:1.3;font-weight:700;color:#18181b;">{{ $title }}</h1>
                            <p style="margin:0 0 12px;font-size:14px;line-height:1.6;color:#52525b;">Halo, {{ $name }}.</p>
                            <p style="margin:0 0 24px;font-size:14px;line-height:1.6;color:#27272a;">{{ $body }}</p>

                            <a href="{{ $url }}"
                               style="display:inline-block;background-color:#27272a;color:#ffffff;text-decoration:none;padding:11px 20px;border-radius:8px;font-size:14px;font-weight:600;">
                                {{ $actionText }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 4px 0;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.5;color:#a1a1aa;">
                            Pesan otomatis dari {{ config('app.name') }}.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>