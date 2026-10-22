<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Don't apply to non-successful or non-redirect responses if not appropriate,
        // but typically security headers should be applied to all responses.
        if (method_exists($response, 'header')) {
            $response->header('X-Content-Type-Options', 'nosniff');
            if ($request->is('api/*')) {
                $response->header('Cross-Origin-Resource-Policy', 'cross-origin');
                $response->header('Access-Control-Allow-Origin', '*');
                $response->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
                $response->header('Access-Control-Allow-Headers', 'Content-Type, X-Sync-Key, Authorization, X-Requested-With');
            } else {
                $response->header('X-Frame-Options', 'SAMEORIGIN');
                $response->header('Cross-Origin-Resource-Policy', 'same-site');
            }

            // Baseline CSP. Allows self for everything, inline scripts and eval for Livewire/Alpine,
            // inline styles, and data URIs for images and fonts. Also allows https: and http: for images.
            $csp = [
                'default-src'     => "default-src 'self'",
                'script-src'      => "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://www.youtube.com https://www.youtube-nocookie.com https://s.ytimg.com https://cdn.tiny.cloud https://sp.tinymce.com https://unpkg.com",
                'style-src'       => "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.tiny.cloud",
                'img-src'         => "img-src 'self' data: blob: https: http:",
                'font-src'        => "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com",
                'object-src'      => "object-src 'none'",
                'frame-src'       => "frame-src 'self' https://www.youtube.com https://youtube.com https://www.youtube-nocookie.com https://player.vimeo.com https://maps.google.com https://www.google.com https://google.com",
                'frame-ancestors' => "frame-ancestors 'self'",
                'connect-src'     => "connect-src 'self' https://fonts.googleapis.com https://fonts.gstatic.com https://cdn.tiny.cloud https://sp.tinymce.com https://maps.googleapis.com https://maps.google.com"
            ];

            if (app()->environment('local') || in_array($request->getHost(), ['127.0.0.1', 'localhost'])) {
                // Allow Vite dev server & WebSockets for HMR and source maps
                $viteHost = 'http://localhost:5173 http://127.0.0.1:5173 *:5173';
                $viteWs   = 'ws://localhost:5173 ws://127.0.0.1:5173 ws://*:5173 ws: wss:';
                
                $csp['script-src']  .= " $viteHost";
                $csp['style-src']   .= " $viteHost";
                $csp['connect-src'] .= " $viteHost $viteWs";
                $csp['img-src']     .= " $viteHost";
            }

            $response->header('Content-Security-Policy', implode('; ', array_values($csp)) . ';');
        }

        return $response;
    }
}
