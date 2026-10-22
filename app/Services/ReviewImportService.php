<?php

namespace App\Services;

use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReviewImportService
{
    /**
     * Import an array of structured or semi-structured review data.
     * Deduplicates based on source_id or author_name + review_date.
     *
     * @param array $items
     * @param string $platform
     * @return array ['imported' => int, 'skipped' => int, 'errors' => array]
     */
    public function importFromArray(array $items, string $platform = 'tripadvisor'): array
    {
        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($items as $item) {
            try {
                $authorName = trim($item['author_name'] ?? $item['reviewer_name'] ?? '');
                $content = trim($item['content'] ?? $item['review_body'] ?? $item['text'] ?? '');

                if (empty($authorName) || empty($content)) {
                    $skipped++;
                    continue;
                }

                // Parse Rating
                $rawRating = $item['rating'] ?? 5.0;
                $rating = $this->normalizeRating($rawRating);

                // Parse Date
                $reviewDate = $this->parseDate($item['review_date'] ?? $item['date'] ?? null);

                // Source ID / URL
                $reviewUrl = $item['review_url'] ?? $item['url'] ?? null;
                $sourceId = $item['source_id'] ?? $this->extractSourceId($reviewUrl, $authorName, $reviewDate);

                // Check for existing review
                $query = Review::query();
                if (!empty($sourceId)) {
                    $query->where('source_id', $sourceId);
                } else {
                    $query->where('author_name', $authorName)
                          ->where('platform', $platform)
                          ->when($reviewDate, fn($q) => $q->whereDate('review_date', $reviewDate));
                }

                $existing = $query->first();

                $tripType = $item['trip_type'] ?? $item['experience_date'] ?? null;
                if ($tripType && str_contains($tripType, '•')) {
                    $parts = explode('•', $tripType);
                    $tripType = trim(end($parts));
                }

                $data = [
                    'platform' => $platform,
                    'author_name' => $authorName,
                    'author_avatar' => $item['author_avatar'] ?? $item['avatar'] ?? null,
                    'author_location' => $item['author_location'] ?? $item['reviewer_location'] ?? $item['location'] ?? null,
                    'rating' => $rating,
                    'title' => trim($item['title'] ?? $item['headline'] ?? ''),
                    'content' => $content,
                    'review_date' => $reviewDate,
                    'trip_type' => $tripType,
                    'review_url' => $reviewUrl,
                    'is_published' => $item['is_published'] ?? true,
                    'is_featured' => $item['is_featured'] ?? false,
                    'source_id' => $sourceId,
                    'raw_data' => $item,
                ];

                if ($existing) {
                    $existing->update($data);
                    $skipped++;
                } else {
                    Review::create($data);
                    $imported++;
                }
            } catch (\Throwable $e) {
                Log::warning('Error importing review item', ['error' => $e->getMessage(), 'item' => $item]);
                $errors[] = $e->getMessage();
            }
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Attempt automated scrape from TripAdvisor URL.
     */
    public function scrapeFromTripAdvisor(string $url): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.9',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
        ]);

        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || empty($html)) {
            return [
                'success' => false,
                'message' => "TripAdvisor returned HTTP {$httpCode}. Cloudflare bot protection is active on this endpoint. Please use the Quick-Paste tab to import reviews instantly.",
                'reviews' => []
            ];
        }

        $reviews = $this->parseTripAdvisorHtml($html, $url);

        if (empty($reviews)) {
            return [
                'success' => false,
                'message' => 'Connected successfully but could not find review cards in HTML structure. Please use the Quick-Paste tab.',
                'reviews' => []
            ];
        }

        $result = $this->importFromArray($reviews, 'tripadvisor');
        return [
            'success' => true,
            'message' => "Successfully imported {$result['imported']} review(s) (skipped {$result['skipped']} duplicates).",
            'reviews' => $reviews,
            'imported' => $result['imported'],
            'skipped' => $result['skipped'],
        ];
    }

    /**
     * Parse TripAdvisor HTML (JSON-LD and DOM regex).
     */
    public function parseTripAdvisorHtml(string $html, ?string $pageUrl = null): array
    {
        $reviews = [];

        // 1. Try JSON-LD Schema
        if (preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/is', $html, $matches)) {
            foreach ($matches[1] as $jsonStr) {
                $data = json_decode($jsonStr, true);
                if (isset($data['@graph']) && is_array($data['@graph'])) {
                    $dataList = $data['@graph'];
                } else {
                    $dataList = [$data];
                }

                foreach ($dataList as $entry) {
                    if (isset($entry['review']) && is_array($entry['review'])) {
                        foreach ($entry['review'] as $rev) {
                            $reviews[] = [
                                'author_name' => $rev['author']['name'] ?? 'TripAdvisor Traveler',
                                'rating' => $rev['reviewRating']['ratingValue'] ?? 5.0,
                                'title' => $rev['name'] ?? '',
                                'content' => $rev['reviewBody'] ?? '',
                                'review_date' => $rev['datePublished'] ?? null,
                                'review_url' => $pageUrl,
                            ];
                        }
                    }
                }
            }
        }

        // 2. DOM parsing via regular expressions if JSON-LD wasn't sufficient
        if (empty($reviews)) {
            // Match review card containers
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
            libxml_clear_errors();
            $xpath = new \DOMXPath($dom);

            $nodes = $xpath->query('//div[@data-reviewid] | //div[contains(@class, "review-container")] | //div[contains(@class, "WAllg")]');
            foreach ($nodes as $node) {
                $revId = $node->getAttribute('data-reviewid') ?: null;
                $authorNode = $xpath->query('.//a[contains(@class, "ui_header_link")] | .//span[contains(@class, "yCeNX")] | .//a[contains(@href, "/Profile/")]', $node)->item(0);
                $author = $authorNode ? trim($authorNode->textContent) : null;

                $titleNode = $xpath->query('.//span[contains(@class, "title")] | .//div[contains(@class, "fTKRw")]//span | .//a[contains(@class, "title")]', $node)->item(0);
                $title = $titleNode ? trim($titleNode->textContent) : '';

                $bodyNode = $xpath->query('.//div[contains(@class, "entry")] | .//span[contains(@class, "yCeNX")]//span | .//div[contains(@class, "biGQs")]', $node)->item(0);
                $body = $bodyNode ? trim($bodyNode->textContent) : '';

                $bubbleNode = $xpath->query('.//*[contains(@class, "ui_bubble_rating")]', $node)->item(0);
                $rating = 5.0;
                if ($bubbleNode && preg_match('/bubble_(\d+)/', $bubbleNode->getAttribute('class'), $bm)) {
                    $rating = ((float)$bm[1]) / 10;
                }

                if ($author && $body) {
                    $reviews[] = [
                        'source_id' => $revId ? "ta_{$revId}" : null,
                        'author_name' => $author,
                        'rating' => $rating,
                        'title' => $title,
                        'content' => $body,
                        'review_url' => $pageUrl,
                    ];
                }
            }
        }

        return $reviews;
    }

    /**
     * Attempt automated scrape from SafariBookings URL.
     */
    public function scrapeFromSafariBookings(string $url): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.9',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
        ]);

        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || empty($html)) {
            return [
                'success' => false,
                'message' => "SafariBookings returned HTTP {$httpCode}. If bot verification is active, you can copy the reviews or page snippet and paste into the Smart Paste tab.",
                'reviews' => []
            ];
        }

        $reviews = $this->parseSafariBookingsHtml($html, $url);

        if (empty($reviews)) {
            return [
                'success' => false,
                'message' => 'Connected to SafariBookings successfully, but could not detect review items on this page. Please ensure you provided your operator reviews URL, or use the Smart Paste tab.',
                'reviews' => []
            ];
        }

        $result = $this->importFromArray($reviews, 'safari_bookings');
        return [
            'success' => true,
            'message' => "Successfully imported {$result['imported']} SafariBookings review(s) (updated {$result['skipped']} duplicates).",
            'reviews' => $reviews,
            'imported' => $result['imported'],
            'skipped' => $result['skipped'],
        ];
    }

    /**
     * Parse SafariBookings HTML (JSON-LD and DOM inspection).
     */
    public function parseSafariBookingsHtml(string $html, ?string $pageUrl = null): array
    {
        $reviews = [];

        // 1. JSON-LD Schema
        if (preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/is', $html, $matches)) {
            foreach ($matches[1] as $jsonStr) {
                $data = json_decode($jsonStr, true);
                if (!$data) continue;

                $dataList = isset($data['@graph']) && is_array($data['@graph']) ? $data['@graph'] : [$data];

                foreach ($dataList as $entry) {
                    if (isset($entry['review']) && is_array($entry['review'])) {
                        foreach ($entry['review'] as $rev) {
                            $author = is_array($rev['author'] ?? null) ? ($rev['author']['name'] ?? 'Safari Traveler') : ($rev['author'] ?? 'Safari Traveler');
                            $reviews[] = [
                                'author_name' => trim($author),
                                'rating' => $this->normalizeRating($rev['reviewRating']['ratingValue'] ?? 5.0),
                                'title' => trim($rev['name'] ?? $rev['headline'] ?? ''),
                                'content' => trim($rev['reviewBody'] ?? $rev['description'] ?? ''),
                                'review_date' => $this->parseDate($rev['datePublished'] ?? null),
                                'review_url' => $pageUrl,
                            ];
                        }
                    } elseif (($entry['@type'] ?? '') === 'Review') {
                        $author = is_array($entry['author'] ?? null) ? ($entry['author']['name'] ?? 'Safari Traveler') : ($entry['author'] ?? 'Safari Traveler');
                        $reviews[] = [
                            'author_name' => trim($author),
                            'rating' => $this->normalizeRating($entry['reviewRating']['ratingValue'] ?? 5.0),
                            'title' => trim($entry['name'] ?? $entry['headline'] ?? ''),
                            'content' => trim($entry['reviewBody'] ?? $entry['description'] ?? ''),
                            'review_date' => $this->parseDate($entry['datePublished'] ?? null),
                            'review_url' => $pageUrl,
                        ];
                    }
                }
            }
        }

        // 2. DOM parsing
        if (empty($reviews)) {
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
            libxml_clear_errors();
            $xpath = new \DOMXPath($dom);

            $query = '//div[contains(@class, "review-item")] | //div[contains(@class, "b-review")] | //article[contains(@class, "review")] | //div[contains(@class, "review-card")] | //div[contains(@class, "operator-review")] | //div[@itemprop="review"]';
            $nodes = $xpath->query($query);

            foreach ($nodes as $node) {
                // Author
                $authorNode = $xpath->query('.//*[@itemprop="author"] | .//*[contains(@class, "author")] | .//*[contains(@class, "reviewer")] | .//strong | .//h4 | .//h5', $node)->item(0);
                $author = $authorNode ? trim($authorNode->textContent) : null;

                // Title
                $titleNode = $xpath->query('.//*[@itemprop="name"] | .//*[contains(@class, "title")] | .//*[contains(@class, "tour-title")] | .//h3', $node)->item(0);
                $title = $titleNode ? trim($titleNode->textContent) : '';

                // Content
                $bodyNode = $xpath->query('.//*[@itemprop="reviewBody"] | .//*[contains(@class, "review-text")] | .//*[contains(@class, "body")] | .//p[contains(@class, "text")] | .//blockquote', $node)->item(0);
                $body = $bodyNode ? trim($bodyNode->textContent) : '';

                // Rating
                $rating = 5.0;
                $ratingNode = $xpath->query('.//*[@itemprop="ratingValue"] | .//*[contains(@class, "score")] | .//*[contains(@class, "rating")]', $node)->item(0);
                if ($ratingNode) {
                    $ratingVal = $ratingNode->getAttribute('content') ?: $ratingNode->textContent;
                    $rating = $this->normalizeRating($ratingVal);
                }

                // Date
                $dateNode = $xpath->query('.//*[@itemprop="datePublished"] | .//time | .//*[contains(@class, "date")]', $node)->item(0);
                $dateStr = $dateNode ? ($dateNode->getAttribute('datetime') ?: $dateNode->textContent) : null;

                if ($author && $body) {
                    $reviews[] = [
                        'source_id' => 'sb_' . md5($author . '_' . ($dateStr ?? '') . '_' . mb_substr($body, 0, 30)),
                        'author_name' => $author,
                        'rating' => $rating,
                        'title' => $title,
                        'content' => $body,
                        'review_date' => $this->parseDate($dateStr),
                        'review_url' => $pageUrl,
                    ];
                }
            }
        }

        return $reviews;
    }

    /**
     * Fetch reviews from Google using Google Places API or URL scraper.
     */
    public function fetchFromGoogle(string $urlOrPlaceId, ?string $apiKey = null): array
    {
        $input = trim($urlOrPlaceId);

        // Fallback to configured API key in settings if not provided
        if (empty($apiKey)) {
            try {
                $settings = app(\App\Settings\GeneralSetting::class);
                $apiKey = $settings->google_places_api_key ?: config('services.google.places_api_key');
            } catch (\Throwable $e) {
                $apiKey = config('services.google.places_api_key');
            }
        }

        // Determine if input is a direct Place ID (e.g. ChIJ... or alphanumeric)
        $placeId = null;
        if (preg_match('/^ChIJ[A-Za-z0-9_-]{20,}$/', $input)) {
            $placeId = $input;
        } elseif (preg_match('/placeid=([A-Za-z0-9_-]+)/i', $input, $m)) {
            $placeId = $m[1];
        }

        // If it's a URL, handle redirects (e.g., maps.app.goo.gl)
        $targetUrl = $input;
        if (str_starts_with($input, 'http://') || str_starts_with($input, 'https://')) {
            if (str_contains($input, 'goo.gl') || str_contains($input, 'maps.app')) {
                $expanded = $this->resolveRedirectUrl($input);
                if ($expanded) {
                    $targetUrl = $expanded;
                    if (!$placeId && preg_match('/placeid=([A-Za-z0-9_-]+)/i', $targetUrl, $m)) {
                        $placeId = $m[1];
                    }
                }
            }
        }

        // 1. If we have an API key and Place ID, call the official Google Places API
        if ($apiKey && $placeId) {
            return $this->fetchFromGooglePlacesApi($placeId, $apiKey);
        }

        // 2. If we have an API key but no Place ID and input is text or location name
        if ($apiKey && !$placeId && !str_starts_with($input, 'http')) {
            $foundPlaceId = $this->findPlaceIdFromText($input, $apiKey);
            if ($foundPlaceId) {
                return $this->fetchFromGooglePlacesApi($foundPlaceId, $apiKey);
            }
        }

        // 3. Fallback: Automated HTTP Scrape of Google Maps or Google Reviews page
        if (str_starts_with($targetUrl, 'http')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $targetUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: en-US,en;q=0.9',
            ]);

            $html = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($html && $httpCode === 200) {
                $reviews = $this->parseGoogleHtml($html, $targetUrl);
                if (!empty($reviews)) {
                    $result = $this->importFromArray($reviews, 'google');
                    return [
                        'success' => true,
                        'message' => "Successfully imported {$result['imported']} Google review(s) (updated {$result['skipped']} duplicates).",
                        'reviews' => $reviews,
                        'imported' => $result['imported'],
                        'skipped' => $result['skipped'],
                    ];
                }
            }
        }

        // 4. Return clear instructions
        return [
            'success' => false,
            'message' => 'Google Maps uses strict anti-scraping security. For automated Google fetching, enter your Google Places API Key and Place ID in Review Settings, or copy-paste your Google Reviews into the Smart Paste tab.',
            'reviews' => [],
        ];
    }

    /**
     * Call Google Places API for place details and reviews.
     */
    private function fetchFromGooglePlacesApi(string $placeId, string $apiKey): array
    {
        try {
            $response = Http::timeout(15)->get('https://maps.googleapis.com/maps/api/place/details/json', [
                'place_id' => $placeId,
                'fields' => 'name,rating,reviews,user_ratings_total,url',
                'key' => $apiKey,
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Google Places API request failed with status ' . $response->status(),
                    'reviews' => []
                ];
            }

            $data = $response->json();
            $status = $data['status'] ?? 'UNKNOWN';

            if ($status !== 'OK') {
                $apiMessage = $data['error_message'] ?? "API returned status: {$status}";
                return [
                    'success' => false,
                    'message' => "Google Places API Error: {$apiMessage}",
                    'reviews' => []
                ];
            }

            $rawReviews = $data['result']['reviews'] ?? [];
            if (empty($rawReviews)) {
                return [
                    'success' => false,
                    'message' => "Google Place found ('{$data['result']['name']}'), but no public reviews were returned by Google Places API.",
                    'reviews' => []
                ];
            }

            $reviews = [];
            $placeUrl = $data['result']['url'] ?? null;

            foreach ($rawReviews as $rev) {
                $author = trim($rev['author_name'] ?? 'Google User');
                $text = trim($rev['text'] ?? '');
                if (empty($author) || empty($text)) continue;

                $revTime = isset($rev['time']) ? Carbon::createFromTimestamp($rev['time'])->toDateString() : null;

                $reviews[] = [
                    'source_id' => 'g_' . ($rev['time'] ?? md5($author . '_' . $text)),
                    'author_name' => $author,
                    'author_avatar' => $rev['profile_photo_url'] ?? null,
                    'rating' => (float) ($rev['rating'] ?? 5.0),
                    'content' => $text,
                    'review_date' => $revTime,
                    'review_url' => $rev['author_url'] ?? $placeUrl,
                    'trip_type' => 'Safari Experience',
                ];
            }

            $result = $this->importFromArray($reviews, 'google');
            return [
                'success' => true,
                'message' => "Successfully imported {$result['imported']} Google review(s) from '{$data['result']['name']}' (updated {$result['skipped']} duplicates).",
                'reviews' => $reviews,
                'imported' => $result['imported'],
                'skipped' => $result['skipped'],
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Error querying Google Places API: ' . $e->getMessage(),
                'reviews' => []
            ];
        }
    }

    /**
     * Find a Place ID from a company or location name using Google Find Place.
     */
    private function findPlaceIdFromText(string $query, string $apiKey): ?string
    {
        try {
            $response = Http::timeout(10)->get('https://maps.googleapis.com/maps/api/place/findplacefromtext/json', [
                'input' => $query,
                'inputtype' => 'textquery',
                'fields' => 'place_id,name',
                'key' => $apiKey,
            ]);
            $candidates = $response->json('candidates', []);
            return $candidates[0]['place_id'] ?? null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Parse Google Maps HTML if JSON-LD or structured script is returned.
     */
    public function parseGoogleHtml(string $html, ?string $pageUrl = null): array
    {
        $reviews = [];

        // 1. JSON-LD in Google HTML
        if (preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/is', $html, $matches)) {
            foreach ($matches[1] as $jsonStr) {
                $data = json_decode($jsonStr, true);
                if (!$data) continue;
                $entries = isset($data['@graph']) && is_array($data['@graph']) ? $data['@graph'] : [$data];
                foreach ($entries as $entry) {
                    if (isset($entry['review']) && is_array($entry['review'])) {
                        foreach ($entry['review'] as $rev) {
                            $reviews[] = [
                                'author_name' => $rev['author']['name'] ?? 'Google Traveler',
                                'rating' => $this->normalizeRating($rev['reviewRating']['ratingValue'] ?? 5.0),
                                'content' => $rev['reviewBody'] ?? '',
                                'review_date' => $this->parseDate($rev['datePublished'] ?? null),
                                'review_url' => $pageUrl,
                            ];
                        }
                    }
                }
            }
        }

        return $reviews;
    }

    /**
     * Follow redirects on short URLs (e.g. maps.app.goo.gl).
     */
    private function resolveRedirectUrl(string $url): ?string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        curl_exec($ch);
        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        return $finalUrl ?: $url;
    }

    /**
     * Smart parse free-form text or pasted review snippet.
     */
    public function parseFreeText(string $text, string $platform = 'tripadvisor'): array
    {
        $trimmed = trim($text);

        // 1. Check if user pasted clean JSON
        if ((str_starts_with($trimmed, '[') && str_ends_with($trimmed, ']')) ||
            (str_starts_with($trimmed, '{') && str_ends_with($trimmed, '}'))) {
            $decoded = json_decode($trimmed, true);
            if (is_array($decoded)) {
                $list = isset($decoded['reviews']) ? $decoded['reviews'] : (isset($decoded[0]) ? $decoded : [$decoded]);
                return $this->importFromArray($list, $platform);
            }
        }

        // 2. Check if user pasted HTML
        if (str_contains($trimmed, '<div') || str_contains($trimmed, '<html') || str_contains($trimmed, '<section') || str_contains($trimmed, '<article')) {
            $extracted = match ($platform) {
                'safari_bookings' => $this->parseSafariBookingsHtml($trimmed),
                'google' => $this->parseGoogleHtml($trimmed),
                default => $this->parseTripAdvisorHtml($trimmed),
            };

            if (empty($extracted)) {
                // Try SafariBookings and TripAdvisor parsers as fallbacks
                $extracted = $this->parseSafariBookingsHtml($trimmed);
                if (empty($extracted)) {
                    $extracted = $this->parseTripAdvisorHtml($trimmed);
                }
            }

            if (!empty($extracted)) {
                return $this->importFromArray($extracted, $platform);
            }
        }

        // 3. Smart Text Parser: recognizes reviewer, ratings (5/5 or ★★★★★ or "5 stars"), date, and body
        $chunks = preg_split('/\n{2,}/', $trimmed);
        $items = [];

        foreach ($chunks as $chunk) {
            $lines = array_values(array_filter(array_map('trim', explode("\n", $chunk))));
            if (count($lines) < 2) continue;

            $author = $lines[0];
            $rating = 5.0;
            $reviewDate = null;
            $title = '';
            $bodyLines = [];

            for ($i = 1; $i < count($lines); $i++) {
                $line = $lines[$i];

                // Detect star ratings or rating pattern
                if (preg_match('/[★⭐]{1,5}/u', $line, $sm)) {
                    $rating = (float) mb_strlen($sm[0]);
                    continue;
                } elseif (preg_match('/(\d+(?:\.\d+)?)\s*(?:\/|of)\s*5/i', $line, $rm)) {
                    $rating = (float) $rm[1];
                    continue;
                } elseif (preg_match('/^(\d)\s*stars?$/i', $line, $sm)) {
                    $rating = (float) $sm[1];
                    continue;
                }

                // Detect date (e.g. "2 weeks ago", "October 2025", "2025-10-12")
                if (preg_match('/(?:\b(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*\s+\d{1,2},?\s+\d{4}\b|\b\d{4}-\d{2}-\d{2}\b|\b\d+\s+(?:days?|weeks?|months?|years?)\s+ago\b)/i', $line)) {
                    $reviewDate = $this->parseDate($line);
                    continue;
                }

                // Detect title vs body
                if (empty($title) && count($bodyLines) === 0 && mb_strlen($line) <= 90 && $i < count($lines) - 1) {
                    $title = $line;
                    continue;
                }

                $bodyLines[] = $line;
            }

            $body = implode(' ', $bodyLines);
            if (!empty($author) && !empty($body)) {
                $items[] = [
                    'author_name' => $author,
                    'rating' => $rating,
                    'title' => $title,
                    'content' => $body,
                    'review_date' => $reviewDate,
                ];
            }
        }

        return $this->importFromArray($items, $platform);
    }

    private function normalizeRating($rating): float
    {
        if (is_numeric($rating)) {
            return (float)$rating;
        }
        if (is_string($rating)) {
            if (preg_match('/(\d+(?:\.\d+)?)\s*(?:of|\/)\s*5/i', $rating, $m)) {
                return (float)$m[1];
            }
            if (preg_match('/(\d+(?:\.\d+)?)/', $rating, $m)) {
                return (float)$m[1];
            }
        }
        return 5.0;
    }

    private function parseDate($dateStr): ?string
    {
        if (empty($dateStr)) {
            return null;
        }
        try {
            // Clean strings like "Written July 3, 2026", "Written Dec 2025"
            $cleaned = preg_replace('/^(?:Written|Reviewed|Date of experience:?)\s*/i', '', trim($dateStr));
            return Carbon::parse($cleaned)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function extractSourceId(?string $url, string $author, ?string $date): string
    {
        if ($url && preg_match('/r(\d+)/', $url, $m)) {
            return 'ta_' . $m[1];
        }
        return 'rev_' . md5($author . '_' . ($date ?? '') . '_' . mb_substr($url ?? '', 0, 30));
    }
}
