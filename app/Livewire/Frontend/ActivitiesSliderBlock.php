<?php

namespace App\Livewire\Frontend;

use App\Models\Activity;
use Livewire\Component;

class ActivitiesSliderBlock extends Component
{
    public $activityIds = 'all';
    public $heading = null;
    public $headingAccent = null;
    public $subheading = null;
    public $theme = 'light';
    public $autoplay = true;
    public $limit = 12;

    public function mount(
        $activityIds = 'all',
        $heading = null,
        $headingAccent = null,
        $subheading = null,
        $theme = 'light',
        $autoplay = true,
        $limit = 12
    ) {
        $this->activityIds = $activityIds ?: 'all';
        $this->heading = $heading;
        $this->headingAccent = $headingAccent;
        $this->subheading = $subheading;
        $this->theme = in_array($theme, ['dark', 'light']) ? $theme : 'light';
        $this->autoplay = filter_var($autoplay, FILTER_VALIDATE_BOOLEAN);
        $this->limit = (int) $limit ?: 12;
    }

    public function render()
    {
        $query = Activity::where('is_active', true);
        $ids = [];

        if ($this->activityIds !== 'all' && !empty($this->activityIds)) {
            $ids = is_array($this->activityIds)
                ? array_values(array_filter($this->activityIds))
                : array_values(array_filter(array_map('trim', explode(',', (string) $this->activityIds))));

            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
        } else {
            $query->take($this->limit);
        }

        $activities = $query->get();

        // Preserve exact order from multiselect if specific IDs were chosen
        if (!empty($ids)) {
            $orderMap = array_flip($ids);
            $activities = $activities->sortBy(function ($item) use ($orderMap) {
                return $orderMap[(string) $item->id] ?? $orderMap[$item->id] ?? 9999;
            })->values();
        }

        return view('livewire.frontend.activities-slider-block', [
            'activities' => $activities,
        ]);
    }
}
