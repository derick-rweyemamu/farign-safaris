<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReviewImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReviewSyncController extends Controller
{
    /**
     * Handle browser-based 1-click sync from TripAdvisor / Google / SafariBookings.
     */
    public function sync(Request $request): JsonResponse|Response
    {
        // Handle preflight CORS request
        if ($request->isMethod('OPTIONS')) {
            return response('', 204)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, X-Sync-Key, Authorization, X-Requested-With');
        }

        // Status check for GET requests
        if ($request->isMethod('GET')) {
            return response()->json([
                'status' => 'ready',
                'app' => config('app.name', 'Safari ERP'),
                'message' => 'Safari ERP Review Sync API is operational.',
            ])
            ->header('Access-Control-Allow-Origin', '*');
        }

        $reviews = $request->input('reviews');
        $platform = $request->input('platform', 'tripadvisor');
        $token = $request->header('X-Sync-Key') ?: $request->input('token');

        // Support Form-based Bridge payload (bypasses HTTPS -> HTTP mixed content on localhost)
        $isFormBridge = false;
        if ($request->filled('payload')) {
            $isFormBridge = true;
            $payloadData = json_decode($request->input('payload'), true);
            if (is_array($payloadData)) {
                $token = $payloadData['token'] ?? $token;
                $reviews = $payloadData['reviews'] ?? [];
                $platform = $payloadData['platform'] ?? $platform;
            }
        }

        // Verify Secret Token
        $validToken = hash_hmac('sha256', 'review_sync', config('app.key'));

        if (!hash_equals($validToken, (string) $token)) {
            if ($isFormBridge) {
                return response('<div style="font-family:sans-serif;padding:30px;color:red;"><h3>Unauthorized</h3><p>Invalid sync token.</p></div>', 403)->header('Content-Type', 'text/html');
            }
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid sync security token.',
            ], 403)
            ->header('Access-Control-Allow-Origin', '*');
        }

        if (empty($reviews) || !is_array($reviews)) {
            if ($isFormBridge) {
                return response('<div style="font-family:sans-serif;padding:30px;color:orange;"><h3>Notice</h3><p>No review data was received from the page.</p></div>', 422)->header('Content-Type', 'text/html');
            }
            return response()->json([
                'success' => false,
                'message' => 'No review data was received from the page.',
            ], 422)
            ->header('Access-Control-Allow-Origin', '*');
        }

        $service = new ReviewImportService();
        $result = $service->importFromArray($reviews, $platform);

        $imported = $result['imported'];
        $skipped = $result['skipped'];

        if ($imported > 0 && $skipped === 0) {
            $msg = "Imported {$imported} new review(s) successfully!";
        } elseif ($imported > 0 && $skipped > 0) {
            $msg = "Imported {$imported} new review(s) and refreshed {$skipped} existing review(s).";
        } elseif ($skipped > 0) {
            $msg = "Synced! This review was already saved in your database (refreshed with latest details).";
        } else {
            $msg = "Processed " . count($reviews) . " review item(s).";
        }

        if ($isFormBridge) {
            $safeMsg = e($msg);
            $count = count($reviews);
            $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Safari ERP - Reviews Synced</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #0f172a; color: #fff; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; padding: 16px; }
        .card { background: #1e293b; border: 1px solid rgba(255,255,255,0.12); border-radius: 14px; padding: 32px 28px; text-align: center; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.6); max-width: 440px; width: 100%; }
        .icon { width: 52px; height: 52px; border-radius: 50%; background: #10b981; color: #fff; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 26px; font-weight: bold; }
        h2 { margin: 0 0 10px; font-size: 19px; color: #f8fafc; }
        p { margin: 0 0 20px; color: #94a3b8; font-size: 14px; line-height: 1.5; }
        .btn { background: #38bdf8; color: #0f172a; font-weight: bold; border: none; padding: 10px 22px; border-radius: 6px; cursor: pointer; font-size: 13px; transition: opacity .2s; }
        .btn:hover { opacity: 0.9; }
        .timer { font-size: 11px; color: #64748b; margin-top: 14px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">✓</div>
        <h2>Safari ERP Review Sync</h2>
        <p>{$safeMsg}</p>
        <button class="btn" onclick="window.close()">Close Window</button>
        <div class="timer">This popup will close automatically in 3 seconds...</div>
        <script>setTimeout(function(){ window.close(); }, 3000);</script>
    </div>
</body>
</html>
HTML;
            return response($html, 200)->header('Content-Type', 'text/html');
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $result['errors'],
            'total_parsed' => count($reviews),
        ])
        ->header('Access-Control-Allow-Origin', '*');
    }
}
