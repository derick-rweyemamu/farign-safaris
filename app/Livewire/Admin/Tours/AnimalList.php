<?php

namespace App\Livewire\Admin\Tours;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Animal;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Animals')]
class AnimalList extends Component
{
    use WithPagination, WithFileUploads, AuthorizesRequests, \App\Traits\InteractsWithMediaUploads, \App\Traits\HasLocaleTranslations;

    #[Url(history: true)]
    public $search = '';

    #[Url]
    public int $perPage = 10;

    public $animal_id, $image_path, $existingImage;
    public $icon;
    public $existingIcon;

    public $isEdit = false;
    public $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFields()
    {
        $this->animal_id = null;
        $this->initLocaleTranslations();
        $this->image_path = null;
        $this->existingImage = null;
        $this->icon = null;
        $this->existingIcon = null;
        $this->isEdit = false;
    }

    public function create()
    {
        $this->authorize('create product'); // Using product permissions
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('edit product');
        $this->resetFields();
        $animal = Animal::findOrFail($id);
        $this->animal_id   = $animal->id;
        $this->loadTranslationsFromModel($animal, ['name']);
        $this->existingIcon  = $animal->icon;
        $this->existingImage = $animal->image_path;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->authorize($this->isEdit ? 'edit product' : 'create product');
        $this->validate([
            'names.en'   => 'required|string|max:255',
            'icon'       => 'nullable|mimes:svg,png,jpg,jpeg,webp|max:512',
            'image_path' => $this->imageRule('image_path', 600),
        ], ['names.en.required' => 'The English name is required.']);

        $data = [];

        if ($this->icon) {
            $data['icon'] = $this->saveImageField($this->icon, 'animals/icons');
        } elseif ($this->isEdit && empty($this->existingIcon)) {
            $data['icon'] = null;
        } elseif ($this->isEdit) {
            $data['icon'] = $this->existingIcon;
        }

        if ($this->image_path) {
            $data['image_path'] = $this->saveImageField($this->image_path, 'animals');
        } elseif ($this->isEdit && empty($this->existingImage)) {
            $data['image_path'] = null;
        }

        if ($this->isEdit) {
            $animal = Animal::find($this->animal_id);
            $animal->fill($data);
            $this->applyTranslationsToModel($animal, ['name']);
            $this->dispatch('notify', message: __('Animal updated successfully!'), type: 'success');
        } else {
            $animal = new Animal($data);
            $this->applyTranslationsToModel($animal, ['name']);
            $this->dispatch('notify', message: __('Animal created successfully!'), type: 'success');
        }

        $this->showModal = false;
        $this->resetFields();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete product');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteAnimal(): void
    {
        $this->authorize('delete product');
        Animal::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', message: __('Animal deleted successfully!'), type: 'success');
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function render()
    {
        $this->authorize('view product');
        
        $animals = Animal::when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.tours.animal-list', [
            'animals' => $animals,
        ]);
    }
}
