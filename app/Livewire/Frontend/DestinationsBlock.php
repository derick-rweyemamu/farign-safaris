<?php

namespace App\Livewire\Frontend;

use App\Models\Destination;
use Livewire\Component;

class DestinationsBlock extends Component
{
    public $limit = 6;
    public $countryId = null;

    public function mount($limit = 6, $countryId = null)
    {
        $this->limit = (int) preg_replace('/[^0-9]/', '', (string) $limit) ?: 6;
        $this->countryId = $countryId;
    }

    public function render()
    {
        $limit = (int) preg_replace('/[^0-9]/', '', (string) $this->limit) ?: 6;
        $countryId = $this->countryId;
        
        // Fetch active destinations with a featured image
        $destinations = Destination::where('is_active', true)
            ->where('is_featured', true)
            ->when($countryId, function($query) use ($countryId) {
                $query->whereHas('city', function($q) use ($countryId) {
                    $q->where('country_id', $countryId);
                });
            })
            ->whereNotNull('featured_image')
            ->orderBy('name', 'asc')
            ->take($limit)
            ->get();

        return view('livewire.frontend.destinations-block', [
            'destinations' => $destinations
        ]);
    }
}
