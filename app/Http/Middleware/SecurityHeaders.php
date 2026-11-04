<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Mencegah halaman dimuat di dalam frame situs lain (clickjacking)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        // Mencegah browser menebak tipe file
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        // Membatasi informasi asal halaman yang dikirim ke situs lain
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        // Mematikan akses fitur perangkat yang tidak dipakai
                $response->headers->set('Permissions-Policy', 'camera=(self), microphone=(), geolocation=()');

        return $response;
    }
}