<?php

namespace App\Livewire\Admin\Media;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\MediaLibraryHolder;

class MediaPickerModal extends Component
{
    use WithPagination, WithFileUploads;

    public bool $isOpen = false;
    public string $activeTab = 'library'; // 'library' or 'upload'
    public string $search = '';
    public string $filterType = 'all'; // 'all', 'image', 'video'
    public string $sortBy = 'newest'; // 'newest', 'oldest', 'smallest', 'largest'
    public bool $showSelectedOnly = false;

    public ?string $callbackId = null;
    public ?string $targetModel = null;
    public bool $multiple = false;
    public ?int $selectedMediaId = null;
    public array $selectedMediaIds = [];

    public $uploadFiles = [];
    public string $collectionName = 'default';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function updatedShowSelectedOnly()
    {
        $this->resetPage();
    }

    #[On('open-media-picker')]
    public function openPicker($callbackId = null, $targetModel = null, $filterType = 'all', $multiple = false, $selected = [])
    {
        $this->reset(['search', 'selectedMediaId', 'selectedMediaIds', 'uploadFiles', 'showSelectedOnly']);
        $this->resetPage();

        if (is_array($callbackId)) {
            $params = $callbackId;
            $callbackId = $params['callbackId'] ?? null;
            $targetModel = $params['targetModel'] ?? $targetModel;
            $filterType = $params['filterType'] ?? $filterType;
            $multiple = $params['multiple'] ?? $multiple;
            $selected = $params['selected'] ?? $selected;
        }

        $this->callbackId = $callbackId ?: null;
        $this->targetModel = $targetModel ?: null;
        $this->filterType = $filterType ?: 'all';
        $this->multiple = (bool) $multiple;
        $this->selectedMediaIds = is_array($selected) ? array_map('intval', $selected) : [];
        if (!$this->multiple && !empty($this->selectedMediaIds)) {
            $this->selectedMediaId = $this->selectedMediaIds[0];
        }
        $this->activeTab = 'library';
        $this->isOpen = true;
    }

    public function closePicker()
    {
        $this->isOpen = false;
    }

    public function selectMedia(int $mediaId)
    {
        if (!$this->multiple) {
            $this->selectedMediaId = $mediaId;
            $this->selectedMediaIds = [$mediaId];
        } else {
            if (in_array($mediaId, $this->selectedMediaIds)) {
                $this->selectedMediaIds = array_values(array_diff($this->selectedMediaIds, [$mediaId]));
            } else {
                $this->selectedMediaIds[] = $mediaId;
            }
        }
    }

    public function clearSelection()
    {
        $this->selectedMediaId = null;
        $this->selectedMediaIds = [];
    }

    public function confirmSelection()
    {
        $ids = $this->multiple ? $this->selectedMediaIds : ($this->selectedMediaId ? [$this->selectedMediaId] : []);
        if (empty($ids)) {
            return;
        }

        $mediaItems = Media::whereIn('id', $ids)->get();
        if ($mediaItems->isEmpty()) {
            return;
        }

        $items = [];
        foreach ($mediaItems as $media) {
            $url = $media->getUrl();
            $storagePath = $media->id . '/' . $media->file_name;
            $items[] = [
                'id' => $media->id,
                'url' => $url,
                'storagePath' => $storagePath,
                'name' => $media->name ?: $media->file_name,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
            ];
        }

        $first = $items[0] ?? [];

        // Dispatch browser event so components, Alpine, TinyMCE, or GrapesJS can catch it
        $this->dispatch('media-picker-chosen',
            callbackId: $this->callbackId,
            targetModel: $this->targetModel,
            multiple: $this->multiple,
            items: $items,
            url: $first['url'] ?? null,
            storagePath: $first['storagePath'] ?? null,
            name: $first['name'] ?? null,
            mediaId: $first['id'] ?? null
        );

        $this->isOpen = false;
    }

    public function registerUploadedMedia(array $paths)
    {
        $holder = MediaLibraryHolder::singleton();
        $newIds = [];

        foreach ($paths as $relativePath) {
            $absolutePath = storage_path('app/public/' . $relativePath);

            if (!file_exists($absolutePath)) {
                continue;
            }

            $originalName = basename($relativePath);
            $cleanName = pathinfo($originalName, PATHINFO_FILENAME);

            $media = $holder->addMedia($absolutePath)
                ->usingName($cleanName)
                ->usingFileName($originalName)
                ->toMediaCollection($this->collectionName);
            $newIds[] = $media->id;
        }

        $this->activeTab = 'library';

        if (!empty($newIds)) {
            if ($this->multiple) {
                $this->selectedMediaIds = array_values(array_unique(array_merge($this->selectedMediaIds, $newIds)));
            } else {
                $lastId = end($newIds);
                $this->selectedMediaId = $lastId;
                $this->selectedMediaIds = [$lastId];
            }
        }

        $count = count($newIds);
        $this->dispatch('notify', message: "Uploaded {$count} file(s) to library and selected!", type: 'success');
    }

    public function deleteMedia(int $id)
    {
        $media = Media::find($id);
        if ($media) {
            $media->delete();
            if ($this->selectedMediaId === $id) {
                $this->selectedMediaId = null;
            }
            $this->selectedMediaIds = array_values(array_diff($this->selectedMediaIds, [$id]));
            $this->dispatch('notify', message: __('Media deleted from library!'), type: 'success');
        }
    }

    public function render()
    {
        $query = Media::query();

        if (trim($this->search) !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('file_name', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterType === 'image') {
            $query->where('mime_type', 'like', 'image/%');
        } elseif ($this->filterType === 'video') {
            $query->where('mime_type', 'like', 'video/%');
        }

        if ($this->showSelectedOnly && !empty($this->selectedMediaIds)) {
            $query->whereIn('id', $this->selectedMediaIds);
        }

        switch ($this->sortBy) {
            case 'oldest':
                $query->oldest();
                break;
            case 'smallest':
                $query->orderBy('size', 'asc');
                break;
            case 'largest':
                $query->orderBy('size', 'desc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $mediaItems = $query->paginate(24);

        return view('livewire.admin.media.media-picker-modal', [
            'mediaItems' => $mediaItems,
        ]);
    }
}
