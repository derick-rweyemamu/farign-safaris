<?php

namespace App\Livewire\Admin\Destinations;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Destination;
use App\Models\Attraction;
use Illuminate\Support\Str;
use App\Traits\HasReports;

#[Layout('admin::layouts.app')]
#[Title('Manage Attractions')]
class ManageAttractions extends Component
{
    use WithPagination, WithFileUploads, HasReports, \App\Traits\InteractsWithMediaUploads;

    public $destination_id;
    public $destination;
    
    public $attraction_id;
    public $title = [];
    public $slug;
    public $content = [];
    public $featured_image;
    public $existingFeaturedImage;
    public $sort_order = 0;
    public $is_active = true;

    public $isEdit = false;
    public $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    public function mount($destination_id)
    {
        $this->destination_id = $destination_id;
        $this->destination = Destination::findOrFail($destination_id);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->resetInputFields();
        $this->isEdit = true;
        
        $attraction = Attraction::findOrFail($id);
        $this->attraction_id = $id;
        $this->title = $attraction->getTranslations('title');
        $this->content = $attraction->getTranslations('content');
        $this->sort_order = $attraction->sort_order;
        $this->is_active = $attraction->is_active;
        $this->existingFeaturedImage = $attraction->featured_image;
        
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'title.en' => 'required|string|max:255',
        ]);

        $data = [
            'destination_id' => $this->destination_id,
            'title' => $this->title,
            'content' => $this->content,
            'sort_order' => (int) $this->sort_order,
            'is_active' => $this->is_active,
        ];

        $enTitle = $this->title['en'] ?? reset($this->title);
        $baseSlug = Str::slug($enTitle);
        if (empty($baseSlug)) {
            $baseSlug = 'attraction';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (Attraction::withTrashed()
            ->where('slug', $slug)
            ->when($this->isEdit && $this->attraction_id, fn($q) => $q->where('id', '!=', $this->attraction_id))
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $data['slug'] = $slug;

        if ($this->featured_image) {
            if (is_string($this->featured_image)) {
                $data['featured_image'] = $this->featured_image;
            } else {
                $data['featured_image'] = $this->featured_image->store('attractions', 'public');
            }
        }

        if ($this->isEdit) {
            $attraction = Attraction::findOrFail($this->attraction_id);
            $attraction->update($data);
            $this->dispatch('notify', message: __('Attraction updated successfully!'), type: 'success');
        } else {
            Attraction::create($data);
            $this->dispatch('notify', message: __('Attraction created successfully!'), type: 'success');
        }

        $this->showModal = false;
        $this->resetInputFields();
    }

    public function confirmDelete($id)
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->deletingId) {
            Attraction::findOrFail($this->deletingId)->delete();
            $this->dispatch('notify', message: __('Attraction deleted successfully!'), type: 'success');
        }
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function resetInputFields()
    {
        $this->attraction_id = null;
        $this->title = ['en' => ''];
        $this->content = ['en' => ''];
        $this->featured_image = null;
        $this->existingFeaturedImage = null;
        $this->sort_order = 0;
        $this->is_active = true;
    }

    public function render()
    {
        $attractions = Attraction::where('destination_id', $this->destination_id)
            ->orderBy('sort_order')
            ->paginate(10);
            
        return view('livewire.admin.destinations.manage-attractions', compact('attractions'));
    }
}
