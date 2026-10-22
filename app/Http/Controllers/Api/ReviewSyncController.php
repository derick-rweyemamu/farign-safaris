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

        // Verify Secret Token
        $token = $request->header('X-Sync-Key') ?: $request->input('token');
        $validToken = hash_hmac('sha256', 'review_sync', config('app.key'));

        if (!hash_equals($validToken, (string) $token)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid sync security token.',
            ], 403)
            ->header('Access-Control-Allow-Origin', '*');
        }

        $reviews = $request->input('reviews');
        $platform = $request->input('platform', 'tripadvisor');

        if (empty($reviews) || !is_array($reviews)) {
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
