<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use App\Models\GroupTour;

class GroupToursBlock extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $categorySlug = 'all';
    public $limit = 3;

    public function mount($limit = 3, $categorySlug = 'all')
    {
        $this->limit = (int) preg_replace('/[^0-9]/', '', (string) $limit) ?: 3;
        $this->categorySlug = (string) $categorySlug;
    }

    public function render()
    {
        $limit = (int) preg_replace('/[^0-9]/', '', (string) $this->limit) ?: 3;
        $query = GroupTour::with(['category'])->where('is_active', true);
        
        if ($this->categorySlug !== 'all') {
            $query->whereHas('category', function($q) {
                $q->where('slug', $this->categorySlug);
            });
        }
        
        $groupTours = $query->orderBy('start_date', 'asc')->paginate($limit)->onEachSide(1);

        return view('livewire.frontend.group-tours-block', compact('groupTours'));
    }
}
