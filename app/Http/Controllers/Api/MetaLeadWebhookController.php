<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Lead;
use App\Models\NewsletterSubscriber;
use App\Enums\LeadStatus;
use App\Settings\GeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MetaLeadWebhookController extends Controller
{
    /**
     * Handle Meta Webhook verification handshake (GET request from Meta)
     */
    public function verify(Request $request)
    {
        $settings = app(GeneralSetting::class);
        $verifyToken = $settings->meta_webhook_verify_token;

        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $verifyToken && $token === $verifyToken) {
            Log::info("Meta Lead Webhook verified successfully.");
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        Log::warning("Meta Lead Webhook verification failed. Invalid verify token.");
        return response('Forbidden', 403);
    }

    /**
     * Handle incoming Meta Lead Ad notifications (POST request from Meta)
     */
    public function handle(Request $request)
    {
        $settings = app(GeneralSetting::class);

        // Verify SHA256 HMAC signature if App Secret is configured
        if ($settings->meta_app_secret && $request->hasHeader('X-Hub-Signature-256')) {
            $signatureHeader = $request->header('X-Hub-Signature-256');
            $expectedSignature = 'sha256=' . hash_hmac('sha256', $request->getContent(), $settings->meta_app_secret);

            if (!hash_equals($expectedSignature, $signatureHeader)) {
                Log::warning("Meta Lead Webhook HMAC signature mismatch.");
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        $entries = $request->input('entry', []);

        foreach ($entries as $entry) {
            $changes = $entry['changes'] ?? [];

            foreach ($changes as $change) {
                if (($change['field'] ?? '') === 'leadgen') {
                    $value = $change['value'] ?? [];
                    $leadgenId = $value['leadgen_id'] ?? null;

                    if ($leadgenId && $settings->meta_page_access_token) {
                        $this->processLead($leadgenId, $settings->meta_page_access_token);
                    }
                }
            }
        }

        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * Fetch lead data from Meta Graph API and store as Lead & Subscriber (Option C)
     */
    protected function processLead(string $leadgenId, string $accessToken): void
    {
        try {
            $response = Http::get("https://graph.facebook.com/v19.0/{$leadgenId}", [
                'access_token' => $accessToken
            ]);

            if ($response->failed()) {
                Log::error("Failed to fetch lead data from Meta Graph API for leadgen_id: {$leadgenId}", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return;
            }

            $data = $response->json();
            $fieldData = $data['field_data'] ?? [];

            $leadData = [
                'name' => '',
                'email' => null,
                'phone' => null,
                'notes' => '',
            ];

            $customNotes = [];

            foreach ($fieldData as $field) {
                $fieldName = strtolower($field['name'] ?? '');
                $val = $field['values'][0] ?? '';

                if (in_array($fieldName, ['full_name', 'name', 'first_name', 'last_name'])) {
                    $leadData['name'] = trim($leadData['name'] . ' ' . $val);
                } elseif (in_array($fieldName, ['email', 'email_address'])) {
                    $leadData['email'] = strtolower(trim($val));
                } elseif (in_array($fieldName, ['phone_number', 'phone', 'mobile'])) {
                    $leadData['phone'] = trim($val);
                } else {
                    $customNotes[] = ucfirst(str_replace('_', ' ', $fieldName)) . ': ' . $val;
                }
            }

            $leadData['name'] = trim($leadData['name']) ?: 'Meta Lead (' . $leadgenId . ')';
            if (!empty($customNotes)) {
                $leadData['notes'] = implode("\n", $customNotes);
            }

            // Option C - Action 1: Create new Booking Inquiry / Lead
            if ($leadData['email'] || $leadData['phone']) {
                Lead::create([
                    'name' => $leadData['name'],
                    'email' => $leadData['email'],
                    'phone' => $leadData['phone'],
                    'source' => 'Meta Lead Ads',
                    'status' => LeadStatus::NEW,
                    'notes' => $leadData['notes'],
                ]);

                // Option C - Action 2: Automatically add to Newsletter Subscribers
                if ($leadData['email']) {
                    $existingSub = NewsletterSubscriber::where('email', $leadData['email'])->first();
                    if (!$existingSub) {
                        NewsletterSubscriber::create([
                            'email' => $leadData['email'],
                            'status' => 'subscribed',
                            'unsubscribe_token' => Str::random(32),
                        ]);
                    }
                }

                ActivityLog::record('meta_leads', "Captured new Meta Lead & Subscriber: " . ($leadData['email'] ?? $leadData['phone']));
            }

        } catch (\Exception $e) {
            Log::error("Exception processing Meta Lead ({$leadgenId}): " . $e->getMessage());
        }
    }
}
