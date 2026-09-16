<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Decode base64-encoded Livewire payloads.
 *
 * On shared hosting with strict WAF/ModSecurity/Imunify360, Livewire's POST
 * body (which contains serialized PHP class names, HTML content, etc.) triggers
 * 403 Forbidden errors. This middleware pairs with a client-side script that
 * base64-encodes the JSON payload before sending, making it invisible to WAF
 * pattern-matching rules.
 *
 * Flow:
 *   1. Client JS intercepts fetch() and encodes the JSON body as base64
 *   2. This middleware detects the encoded payload (via custom header)
 *   3. Decodes it back to the original JSON before Laravel/Livewire processes it
 */
class DecodeLivewirePayload
{
    public function handle(Request $request, Closure $next)
    {
        // Try decoding first assuming it's base64 encoded by our JS
        $rawBody = $request->getContent();
        $decoded = base64_decode($rawBody, true);

        // If it's valid base64 and decoding it yields a valid JSON array
        if ($decoded !== false && is_array($decodedData = json_decode($decoded, true))) {
            // Update Laravel's parsed request parameters
            $request->merge($decodedData);

            // Update headers to make Livewire's RequireLivewireHeaders middleware happy
            $request->headers->set('Content-Type', 'application/json');
            $request->server->set('CONTENT_TYPE', 'application/json');
            // Also ensure X-Livewire is set just in case the client forgot it
            $request->headers->set('X-Livewire', 'true');

            // Overwrite the protected $content and $json properties safely
            // without resetting Laravel's route resolver (which initialize() does)
            $updater = function () use ($decoded, $decodedData) {
                $this->content = $decoded;
                if (property_exists($this, 'json')) {
                    $this->json = new \Symfony\Component\HttpFoundation\InputBag($decodedData);
                }
                $this->headers->set('CONTENT_TYPE', 'application/json');
            };
            \Closure::bind($updater, $request, $request)();
        } else {
            \Illuminate\Support\Facades\Log::warning('DecodeLivewirePayload failed to decode.', [
                'raw_body_start' => substr($rawBody, 0, 100),
                'is_base64_valid' => $decoded !== false,
                'json_error' => json_last_error_msg(),
                'is_json' => $request->isJson(),
                'content_type' => $request->header('Content-Type')
            ]);
        }

        return $next($request);
    }
}
