<?php

namespace App\Livewire\Admin\CMS;

use App\Models\Review;
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
    public bool $showLinksModal = false;
    public bool $showCreateModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;
    public ?int $editingId = null;

    // Platform Review Links
    public string $tripadvisorUrl = '';
    public string $googleUrl = '';
    public string $googlePlaceId = '';
    public string $googleApiKey = '';
    public string $safaribookingsUrl = '';

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

    public function openLinksModal(): void
    {
        $this->showLinksModal = true;
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

        $this->showLinksModal = false;
        $this->dispatch('notify', message: __('Platform review links saved successfully!'), type: 'success');
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
        ]);
    }
}
