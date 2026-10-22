<?php

namespace App\Livewire\Frontend;

use App\Models\Review;
use Livewire\Component;

class ReviewsBlock extends Component
{
    public ?string $platform = 'all';
    public ?string $mode = 'all';
    public int|string|null $limit = 6;
    public ?string $layout = 'slider';
    public ?string $subheading = 'What Travellers Say';
    public ?string $heading = 'Genuine Reviews From Our Guests';
    public ?string $description = null;
    public ?string $tripadvisorUrl = 'https://www.tripadvisor.com/Attraction_Review-g297913-d33011633-Reviews-Fargin_Safaris-Arusha_Arusha_Region.html';
    public ?string $googleUrl = 'https://www.google.com/search?q=Fargin+Safaris+reviews';
    public ?string $safaribookingsUrl = 'https://www.safaribookings.com';
    public bool|string|null $showBadges = true;
    public bool|string|null $autoplay = true;

    public function mount(
        ?string $platform = 'all',
        ?string $mode = 'all',
        $limit = 6,
        ?string $layout = 'slider',
        ?string $subheading = 'What Travellers Say',
        ?string $heading = 'Genuine Reviews From Our Guests',
        ?string $description = null,
        ?string $tripadvisorUrl = null,
        ?string $googleUrl = null,
        ?string $safaribookingsUrl = null,
        $showBadges = true,
        $autoplay = true
    ) {
        $settings = null;
        try {
            $settings = app(\App\Settings\GeneralSetting::class);
        } catch (\Throwable $e) {}

        $this->platform = $platform ?: 'all';
        $this->mode = $mode ?: 'all';
        $this->limit = (int) $limit ?: 6;
        $this->layout = in_array($layout, ['slider', 'grid']) ? $layout : 'slider';
        $this->subheading = $subheading ?: 'What Travellers Say';
        $this->heading = $heading ?: 'Genuine Reviews From Our Guests';
        $this->description = $description;
        $this->tripadvisorUrl = $tripadvisorUrl ?: ($settings?->tripadvisor_url ?: 'https://www.tripadvisor.com/Attraction_Review-g297913-d33011633-Reviews-Fargin_Safaris-Arusha_Arusha_Region.html');
        $this->googleUrl = $googleUrl ?: ($settings?->google_reviews_url ?: 'https://www.google.com/search?q=Fargin+Safaris+reviews');
        $this->safaribookingsUrl = $safaribookingsUrl ?: ($settings?->safaribookings_url ?: 'https://www.safaribookings.com');
        $this->showBadges = filter_var($showBadges, FILTER_VALIDATE_BOOLEAN);
        $this->autoplay = filter_var($autoplay, FILTER_VALIDATE_BOOLEAN);
    }

    public function render()
    {
        $query = Review::query()
            ->published()
            ->when($this->platform !== 'all', fn($q) => $q->where('platform', $this->platform))
            ->when($this->mode === 'featured', fn($q) => $q->where('is_featured', true))
            ->orderBy('is_featured', 'desc')
            ->orderBy('review_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take($this->limit);

        $reviews = $query->get();

        $stats = [
            'tripadvisor_count' => Review::published()->where('platform', 'tripadvisor')->count(),
            'google_count' => Review::published()->where('platform', 'google')->count(),
            'safari_bookings_count' => Review::published()->where('platform', 'safari_bookings')->count(),
            'avg_rating' => round(Review::published()->avg('rating') ?: 5.0, 1),
        ];

        return view('livewire.frontend.reviews-block', [
            'reviews' => $reviews,
            'stats' => $stats,
        ]);
    }
}
