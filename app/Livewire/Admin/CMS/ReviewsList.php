<?php

namespace App\Livewire\Admin\CMS;

use App\Models\Review;
use App\Services\ReviewImportService;
use App\Settings\GeneralSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin::layouts.app')]
#[Title('Reviews Management')]
class ReviewsList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $platformFilter = 'all';
    public string $statusFilter = 'all';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    public int $perPage = 15;

    // Modals
    public bool $showImportModal = false;
    public bool $showLinksModal = false;
    public bool $showCreateModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;
    public ?int $editingId = null;

    // Import Form & Platform Links
    public string $importTab = 'tripadvisor'; // tripadvisor, google, safari_bookings, paste, links
    public string $tripadvisorUrl = '';
    public string $googleUrl = '';
    public string $googlePlaceId = '';
    public string $googleApiKey = '';
    public string $safaribookingsUrl = '';
    public bool $saveAsDefault = true;

    public string $pastePlatform = 'tripadvisor';
    public string $pasteContent = '';
    public bool $isImporting = false;
    public ?string $importMessage = null;
    public ?string $importMessageType = null;

    // Review Form (Create / Edit)
    public string $platform = 'tripadvisor';
    public string $author_name = '';
    public string $author_location = '';
    public float $rating = 5.0;
    public string $title = '';
    public string $content = '';
    public ?string $review_date = null;
    public string $trip_type = '';
    public string $review_url = '';
    public bool $is_published = true;
    public bool $is_featured = false;

    public function mount(): void
    {
        $settings = app(GeneralSetting::class);
        $this->tripadvisorUrl = $settings->tripadvisor_url ?: 'https://www.tripadvisor.com/Attraction_Review-g297913-d33011633-Reviews-Fargin_Safaris-Arusha_Arusha_Region.html';
        $this->googleUrl = $settings->google_reviews_url ?: 'https://www.google.com/search?q=Fargin+Safaris+reviews';
        $this->googlePlaceId = $settings->google_place_id ?: '';
        $this->googleApiKey = $settings->google_places_api_key ?: '';
        $this->safaribookingsUrl = $settings->safaribookings_url ?: 'https://www.safaribookings.com';
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPlatformFilter()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function togglePublished(int $id): void
    {
        $review = Review::findOrFail($id);
        $review->is_published = !$review->is_published;
        $review->save();

        $status = $review->is_published ? __('published to website') : __('hidden from website');
        $this->dispatch('notify', message: __("Review by :name is now :status.", ['name' => $review->author_name, 'status' => $status]), type: 'success');
    }

    public function toggleFeatured(int $id): void
    {
        $review = Review::findOrFail($id);
        $review->is_featured = !$review->is_featured;
        $review->save();

        $status = $review->is_featured ? __('marked as featured') : __('removed from featured');
        $this->dispatch('notify', message: __("Review by :name :status.", ['name' => $review->author_name, 'status' => $status]), type: 'success');
    }

    public function openImportModal(?string $tab = null): void
    {
        $this->importMessage = null;
        $this->importMessageType = null;
        if ($tab === 'tripadvisor' || empty($tab)) {
            $this->importTab = 'browser_sync';
        } else {
            $this->importTab = $tab;
        }
        $this->showImportModal = true;
    }

    public function openLinksModal(): void
    {
        $this->importTab = 'links';
        $this->showImportModal = true;
    }

    public function executeScrape(): void
    {
        $this->validate([
            'tripadvisorUrl' => 'required|url',
        ]);

        $this->isImporting = true;
        $this->importMessage = null;

        if ($this->saveAsDefault) {
            $settings = app(GeneralSetting::class);
            $settings->tripadvisor_url = $this->tripadvisorUrl;
            $settings->save();
        }

        $service = new ReviewImportService();
        $result = $service->scrapeFromTripAdvisor($this->tripadvisorUrl);

        $this->isImporting = false;

        if ($result['success']) {
            $this->importMessageType = 'success';
            $this->importMessage = $result['message'];
            $this->dispatch('notify', message: $result['message'], type: 'success');
        } else {
            $this->importMessageType = 'error';
            $this->importMessage = $result['message'];
        }
    }

    public function executeSafariBookingsScrape(): void
    {
        $this->validate([
            'safaribookingsUrl' => 'required|url',
        ]);

        $this->isImporting = true;
        $this->importMessage = null;

        if ($this->saveAsDefault) {
            $settings = app(GeneralSetting::class);
            $settings->safaribookings_url = $this->safaribookingsUrl;
            $settings->save();
        }

        $service = new ReviewImportService();
        $result = $service->scrapeFromSafariBookings($this->safaribookingsUrl);

        $this->isImporting = false;

        if ($result['success']) {
            $this->importMessageType = 'success';
            $this->importMessage = $result['message'];
            $this->dispatch('notify', message: $result['message'], type: 'success');
        } else {
            $this->importMessageType = 'error';
            $this->importMessage = $result['message'];
        }
    }

    public function executeGoogleScrape(): void
    {
        if (empty($this->googleUrl) && empty($this->googlePlaceId)) {
            $this->addError('googleUrl', __('Please provide either a Google Maps URL or a Google Place ID.'));
            return;
        }

        $this->isImporting = true;
        $this->importMessage = null;

        if ($this->saveAsDefault) {
            $settings = app(GeneralSetting::class);
            if ($this->googleUrl) $settings->google_reviews_url = $this->googleUrl;
            if ($this->googlePlaceId) $settings->google_place_id = $this->googlePlaceId;
            if ($this->googleApiKey) $settings->google_places_api_key = $this->googleApiKey;
            $settings->save();
        }

        $service = new ReviewImportService();
        $query = $this->googlePlaceId ?: $this->googleUrl;
        $result = $service->fetchFromGoogle($query, $this->googleApiKey ?: null);

        $this->isImporting = false;

        if ($result['success']) {
            $this->importMessageType = 'success';
            $this->importMessage = $result['message'];
            $this->dispatch('notify', message: $result['message'], type: 'success');
        } else {
            $this->importMessageType = 'error';
            $this->importMessage = $result['message'];
        }
    }

    public function savePlatformLinks(): void
    {
        $this->validate([
            'tripadvisorUrl' => 'nullable|url',
            'safaribookingsUrl' => 'nullable|url',
            'googleUrl' => 'nullable|string',
            'googlePlaceId' => 'nullable|string|max:100',
            'googleApiKey' => 'nullable|string|max:255',
        ]);

        $settings = app(GeneralSetting::class);
        $settings->tripadvisor_url = $this->tripadvisorUrl;
        $settings->google_reviews_url = $this->googleUrl;
        $settings->google_place_id = $this->googlePlaceId;
        $settings->google_places_api_key = $this->googleApiKey;
        $settings->safaribookings_url = $this->safaribookingsUrl;
        $settings->save();

        $this->importMessageType = 'success';
        $this->importMessage = __('Platform review links and credentials saved successfully.');
        $this->dispatch('notify', message: __('Platform review links saved successfully!'), type: 'success');
    }

    public function executePasteImport(): void
    {
        $this->validate([
            'pasteContent' => 'required|string|min:10',
            'pastePlatform' => 'required|in:tripadvisor,google,safari_bookings,direct',
        ]);

        $service = new ReviewImportService();
        $result = $service->parseFreeText($this->pasteContent, $this->pastePlatform);

        $this->pasteContent = '';
        $msg = __("Imported :imported new review(s) (:skipped existing/duplicates updated).", [
            'imported' => $result['imported'],
            'skipped' => $result['skipped']
        ]);

        $this->importMessageType = 'success';
        $this->importMessage = $msg;
        $this->dispatch('notify', message: $msg, type: 'success');
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showCreateModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetForm();
        $this->editingId = $id;
        $review = Review::findOrFail($id);

        $this->platform = $review->platform;
        $this->author_name = $review->author_name;
        $this->author_location = $review->author_location ?? '';
        $this->rating = (float) $review->rating;
        $this->title = $review->title ?? '';
        $this->content = $review->content;
        $this->review_date = $review->review_date ? $review->review_date->format('Y-m-d') : null;
        $this->trip_type = $review->trip_type ?? '';
        $this->review_url = $review->review_url ?? '';
        $this->is_published = (bool) $review->is_published;
        $this->is_featured = (bool) $review->is_featured;

        $this->showCreateModal = true;
    }

    public function saveReview(): void
    {
        $this->validate([
            'platform' => 'required|in:tripadvisor,google,safari_bookings,direct',
            'author_name' => 'required|string|max:255',
            'author_location' => 'nullable|string|max:255',
            'rating' => 'required|numeric|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'review_date' => 'nullable|date',
            'trip_type' => 'nullable|string|max:255',
            'review_url' => 'nullable|url',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $data = [
            'platform' => $this->platform,
            'author_name' => $this->author_name,
            'author_location' => $this->author_location ?: null,
            'rating' => $this->rating,
            'title' => $this->title ?: null,
            'content' => $this->content,
            'review_date' => $this->review_date ?: null,
            'trip_type' => $this->trip_type ?: null,
            'review_url' => $this->review_url ?: null,
            'is_published' => $this->is_published,
            'is_featured' => $this->is_featured,
        ];

        if ($this->editingId) {
            $review = Review::findOrFail($this->editingId);
            $review->update($data);
            $this->dispatch('notify', message: __('Review updated successfully.'), type: 'success');
        } else {
            Review::create($data);
            $this->dispatch('notify', message: __('New review created successfully.'), type: 'success');
        }

        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteReview(): void
    {
        if ($this->deletingId) {
            Review::findOrFail($this->deletingId)->delete();
            $this->dispatch('notify', message: __('Review deleted successfully.'), type: 'success');
        }
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->platform = 'tripadvisor';
        $this->author_name = '';
        $this->author_location = '';
        $this->rating = 5.0;
        $this->title = '';
        $this->content = '';
        $this->review_date = now()->format('Y-m-d');
        $this->trip_type = '';
        $this->review_url = '';
        $this->is_published = true;
        $this->is_featured = false;
    }

    public function getBrowserSyncBookmarkletProperty(): string
    {
        $targetUrl = url('/api/reviews/sync-browser');
        $syncKey = hash_hmac('sha256', 'review_sync', config('app.key'));

        $template = <<<'JS'
javascript:(function(){var o = document.getElementById('safari-erp-sync-overlay');if (o) o.remove();var b = document.createElement('div');b.id = 'safari-erp-sync-overlay';b.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:99999999;background:#0f172a;color:#f8fafc;padding:16px 20px;border-radius:12px;box-shadow:0 20px 40px rgba(0,0,0,0.5),0 0 0 1px rgba(255,255,255,0.15);font-family:system-ui,-apple-system,sans-serif;font-size:13px;max-width:380px;line-height:1.4;';b.innerHTML = '<div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#38bdf8;"></span><strong style="font-size:14px;color:#fff;">Safari ERP Review Sync</strong></div><div id="safari-erp-status" style="color:#94a3b8;">Scanning TripAdvisor reviews...</div>';document.body.appendChild(b);var s = function(h, c) {var e = document.getElementById('safari-erp-status');if (e) { e.innerHTML = h; if (c) e.style.color = c; }};try {document.querySelectorAll('button, span, a').forEach(function(x) {if (x.innerText && x.innerText.trim().toLowerCase() === 'read more') {try { x.click(); } catch(e){}}});var revs = [];var seen = {};var addRev = function(r) {var a = (r.author_name || '').trim();var c = (r.content || '').trim();if (!a || !c || c.length < 15) return;var k = a.toLowerCase() + '::' + c.slice(0, 30).toLowerCase();if (!seen[k]) {seen[k] = true;revs.push({source_id: r.source_id || null,author_name: a,rating: parseFloat(r.rating || 5.0),title: (r.title || '').trim(),content: c,review_date: r.review_date || null,review_url: r.review_url || window.location.href});}};var parseRating = function(el) {if (!el) return 5.0;var b = el.matches && el.matches('svg, [class*="bubble_"]') ? el : el.querySelector('[class*="ui_bubble_rating"], [class*="bubble_"], svg[aria-label*="5"], svg[aria-label*="4"], svg[aria-label*="3"], svg[aria-label*="bubble"], svg[title*="5"], svg.UctUV');if (b) {var ar = b.getAttribute('aria-label') || b.getAttribute('title') || '';var ma = ar.match(/([0-9.]+)\s*of\s*5/);if (ma) return parseFloat(ma[1]);var cls = b.getAttribute('class') || '';var mc = cls.match(/bubble_(\d+)/);if (mc) return parseInt(mc[1], 10) / 10;}return 5.0;};var parseDate = function(txt) {var dm = (txt || '').match(/(?:Written|Reviewed|Date of experience:?|Visited)\s*([A-Za-z]+ \d{1,2},? \d{4}|\d{1,2} [A-Za-z]+ \d{4}|[A-Za-z]+ \d{4})/i);return dm ? dm[1] : null;};document.querySelectorAll('script[type="application/ld+json"]').forEach(function(sc) {try {var d = JSON.parse(sc.textContent || '{}');var items = Array.isArray(d) ? d : (d['@graph'] ? d['@graph'] : [d]);items.forEach(function(it) {if (it['@type'] === 'Review' || it.reviewBody) {var a = (it.author && (it.author.name || it.author)) ? (it.author.name || it.author) : 'TripAdvisor Traveler';var mId = it.url && it.url.match(/r(\d+)/) ? 'ta_' + it.url.match(/r(\d+)/)[1] : null;addRev({author_name: a,rating: it.reviewRating ? (it.reviewRating.ratingValue || 5.0) : 5.0,title: it.name || it.headline || '',content: it.reviewBody || it.description || '',review_date: it.datePublished || null,review_url: it.url || window.location.href,source_id: mId});}if (it.review) {var rl = Array.isArray(it.review) ? it.review : [it.review];rl.forEach(function(rv) {var a = (rv.author && (rv.author.name || rv.author)) ? (rv.author.name || rv.author) : 'TripAdvisor Traveler';var mId = rv.url && rv.url.match(/r(\d+)/) ? 'ta_' + rv.url.match(/r(\d+)/)[1] : null;addRev({author_name: a,rating: rv.reviewRating ? (rv.reviewRating.ratingValue || 5.0) : 5.0,title: rv.name || rv.headline || '',content: rv.reviewBody || '',review_date: rv.datePublished || null,review_url: rv.url || window.location.href,source_id: mId});});}});} catch(e){}});document.querySelectorAll('div[role="dialog"], div[aria-modal="true"], div[class*="modal"]').forEach(function(dlg) {try {var aEl = dlg.querySelector('div[class*="avatar"] + div span, [class*="username"], [data-test-target="review-author"], h4, span.yCeNX, strong');var a = aEl ? aEl.innerText.split('\n')[0].trim() : '';var tEl = dlg.querySelector('h1, h2, h3, [data-test-target="review-title"], [class*="title"]');var t = tEl ? tEl.innerText.trim() : '';var bEl = dlg.querySelector('div[data-test-target="review-body"], span[data-test-target="review-text"], div[class*="content"], div[class*="body"], p');var bd = bEl ? bEl.innerText.trim() : '';if (a && bd) {addRev({author_name: a,rating: parseRating(dlg),title: t,content: bd,review_date: parseDate(dlg.innerText),review_url: window.location.href});}} catch(e){}});var ratingNodes = document.querySelectorAll('[class*="ui_bubble_rating"], [class*="bubble_"], svg[aria-label*="5"], svg[aria-label*="4"], svg[aria-label*="bubble"], svg.UctUV');ratingNodes.forEach(function(rNode) {try {var curr = rNode.parentElement;var box = null;for (var l = 0; l < 7; l++) {if (!curr || curr === document.body) break;var txt = curr.innerText || '';if (txt.length > 50 && (txt.includes('Reviewed') || txt.includes('Written') || txt.includes('Visited') || txt.includes('Date of experience') || txt.includes('contribution'))) {box = curr;if (txt.length < 2500) break;}curr = curr.parentElement;}if (box) {var rawLines = box.innerText.split('\n').map(function(x){ return x.trim(); }).filter(function(x){ return x.length > 0; });var cleanLines = rawLines.filter(function(line) {var low = line.toLowerCase();if (low === 'helpful' || low.startsWith('helpful (') || low.startsWith('thank ') || low === 'share' || low === 'save') return false;if (low.includes('transparency report') || low.includes('subjective opinion') || low.startsWith('review of ')) return false;if (/^\d+\s*contribution/i.test(low) || /^\d+$/i.test(line)) return false;return true;});if (cleanLines.length >= 2) {var authorCandidate = cleanLines[0];if (authorCandidate.length <= 40 && !authorCandidate.startsWith('Reviewed')) {var titleCandidate = '';var bodyParts = [];var second = cleanLines[1];var isDateLine = /^(?:Reviewed|Written|Visited|Date)/i.test(second);var startIdx = 1;if (second && second.length < 90 && !isDateLine && cleanLines.length >= 3) {titleCandidate = second;startIdx = 2;}for (var idx = startIdx; idx < cleanLines.length; idx++) {if (!/^(?:Reviewed|Written|Visited|Date of experience)/i.test(cleanLines[idx])) {bodyParts.push(cleanLines[idx]);}}var bodyText = bodyParts.join('\n\n').trim();var revUrl = window.location.href;var linkEl = box.querySelector('a[href*="/ShowUserReviews"]');if (linkEl) revUrl = linkEl.href;var idMatch = revUrl.match(/r(\d{8,12})/);addRev({source_id: idMatch ? 'ta_' + idMatch[1] : null,author_name: authorCandidate,rating: parseRating(rNode),title: titleCandidate,content: bodyText,review_date: parseDate(box.innerText),review_url: revUrl});}}}} catch(e){}});if (revs.length === 0) {s('⚠️ No reviews found on this page. Please open your TripAdvisor review page or modal.', '#f87171');setTimeout(function(){ b.remove(); }, 6000);return;}s('Found <strong>' + revs.length + ' review(s)</strong>. Syncing...', '#38bdf8');var targetUrl = '__TARGET_URL__';var syncKey = '__SYNC_KEY__';var sendViaFormBridge = function(note) {s('🚀 ' + (note || 'Transmitting to Safari ERP...'), '#38bdf8');var form = document.createElement('form');form.method = 'POST';form.action = targetUrl;form.target = '_blank';var input = document.createElement('input');input.type = 'hidden';input.name = 'payload';input.value = JSON.stringify({ token: syncKey, platform: 'tripadvisor', reviews: revs });form.appendChild(input);document.body.appendChild(form);form.submit();setTimeout(function(){ form.remove(); }, 2000);s('✅ <strong>Sent!</strong> ' + revs.length + ' review(s) synced to Safari ERP.', '#4ade80');setTimeout(function(){ b.remove(); }, 6000);};var isMixed = (window.location.protocol === 'https:' && targetUrl.indexOf('http:') === 0);if (isMixed) {sendViaFormBridge('Syncing to Safari ERP localhost...');} else {fetch(targetUrl, {method: 'POST',mode: 'cors',headers: {'Content-Type': 'application/json','Accept': 'application/json','X-Sync-Key': syncKey},body: JSON.stringify({ token: syncKey, platform: 'tripadvisor', reviews: revs })}).then(function(r){ return r.json(); }).then(function(d){if (d.success) {s('✅ <strong>Success!</strong> ' + d.message, '#4ade80');} else {s('⚠️ ' + (d.message || 'Sync failed'), '#fbbf24');}setTimeout(function(){ b.remove(); }, 6000);}).catch(function(err){sendViaFormBridge('Retrying via backup bridge window...');});}} catch(err) {s('❌ Error: ' + err.message, '#f87171');setTimeout(function(){ b.remove(); }, 6000);}})();
JS;

        return str_replace(['__TARGET_URL__', '__SYNC_KEY__'], [$targetUrl, $syncKey], $template);
    }

    public function getSyncKeyProperty(): string
    {
        return hash_hmac('sha256', 'review_sync', config('app.key'));
    }

    public function getSyncUrlProperty(): string
    {
        return url('/api/reviews/sync-browser');
    }

    public function render()
    {
        $query = Review::query()
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('author_name', 'like', "%{$this->search}%")
                        ->orWhere('title', 'like', "%{$this->search}%")
                        ->orWhere('content', 'like', "%{$this->search}%")
                        ->orWhere('author_location', 'like', "%{$this->search}%");
                });
            })
            ->when($this->platformFilter !== 'all', fn($q) => $q->where('platform', $this->platformFilter))
            ->when($this->statusFilter === 'published', fn($q) => $q->where('is_published', true))
            ->when($this->statusFilter === 'hidden', fn($q) => $q->where('is_published', false))
            ->when($this->statusFilter === 'featured', fn($q) => $q->where('is_featured', true))
            ->orderBy($this->sortBy, $this->sortDir);

        $reviews = $query->paginate($this->perPage);

        $stats = [
            'total' => Review::count(),
            'published' => Review::where('is_published', true)->count(),
            'featured' => Review::where('is_featured', true)->count(),
            'tripadvisor' => Review::where('platform', 'tripadvisor')->count(),
            'google' => Review::where('platform', 'google')->count(),
            'safari_bookings' => Review::where('platform', 'safari_bookings')->count(),
            'avg_rating' => round(Review::avg('rating') ?: 5.0, 1),
        ];

        return view('livewire.admin.cms.reviews-list', [
            'reviews' => $reviews,
            'stats' => $stats,
            'browserSyncBookmarklet' => $this->browserSyncBookmarklet,
            'syncUrl' => $this->syncUrl,
        ]);
    }
}
