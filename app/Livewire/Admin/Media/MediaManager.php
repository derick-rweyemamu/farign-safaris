<?php

namespace App\Livewire\Admin\Media;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\MediaLibraryHolder;

#[Layout('admin::layouts.app')]
#[Title('Media Library')]
class MediaManager extends Component
{
    use WithPagination, WithFileUploads;

    #[Url(history: true)]
    public string $search = '';

    #[Url]
    public string $filterType = 'all'; // 'all', 'image', 'video'

    #[Url]
    public string $sortBy = 'newest'; // 'newest', 'oldest', 'smallest', 'largest'

    public $uploadFiles = [];
    public array $selectedIds = [];
    public bool $selectAll = false;
    public ?int $detailMediaId = null;
    public string $detailMediaName = '';

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

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->getQuery()->pluck('id')->map(fn ($id) => (int) $id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function registerUploadedMedia(array $paths)
    {
        $holder = MediaLibraryHolder::singleton();
        $count = 0;

        foreach ($paths as $relativePath) {
            $absolutePath = storage_path('app/public/' . $relativePath);

            if (!file_exists($absolutePath)) {
                continue;
            }

            $originalName = basename($relativePath);
            $cleanName = pathinfo($originalName, PATHINFO_FILENAME);

            $holder->addMedia($absolutePath)
                ->usingName($cleanName)
                ->usingFileName($originalName)
                ->toMediaCollection('default');
            $count++;
        }

        $this->dispatch('notify', message: "Successfully uploaded {$count} media file(s) to the library!", type: 'success');
    }

    public function deleteMedia(int $id)
    {
        $media = Media::find($id);
        if ($media) {
            $media->delete();
            $this->selectedIds = array_diff($this->selectedIds, [$id]);
            $this->dispatch('notify', message: __('Media item deleted permanently!'), type: 'success');
        }
    }

    public function bulkDelete()
    {
        if (empty($this->selectedIds)) {
            return;
        }

        $count = count($this->selectedIds);
        Media::whereIn('id', $this->selectedIds)->delete();

        $this->selectedIds = [];
        $this->selectAll = false;

        $this->dispatch('notify', message: "Deleted {$count} media item(s) permanently!", type: 'success');
    }

    public function syncExistingMedia()
    {
        $baseDir = storage_path('app/public');
        if (!\Illuminate\Support\Facades\File::exists($baseDir)) {
            $this->dispatch('notify', message: __('Storage directory not found.'), type: 'error');
            return;
        }

        $allFiles = \Illuminate\Support\Facades\File::allFiles($baseDir);
        $holder = MediaLibraryHolder::singleton();

        $importedCount = 0;

        foreach ($allFiles as $file) {
            $relativePath = str_replace('\\', '/', $file->getRelativePath());
            $filename = $file->getFilename();

            if (str_starts_with($filename, '.')) {
                continue;
            }

            $firstSegment = explode('/', $relativePath)[0] ?? '';
            if (is_numeric($firstSegment) || $firstSegment === 'livewire-tmp') {
                continue;
            }

            $exists = Media::where('file_name', $filename)
                ->where('size', $file->getSize())
                ->exists();

            if ($exists) {
                continue;
            }

            try {
                $holder->addMedia($file->getPathname())
                    ->preservingOriginal()
                    ->toMediaCollection('default');

                $importedCount++;
            } catch (\Exception $e) {
                // skip
            }
        }

        $this->dispatch('notify', message: "Synced {$importedCount} existing file(s) into the Media Library!", type: 'success');
    }

    public function showDetails(int $id)
    {
        $this->detailMediaId = $id;
        $media = Media::find($id);
        if ($media) {
            $this->detailMediaName = $media->name ?: $media->file_name;
        }
    }

    public function closeDetails()
    {
        $this->detailMediaId = null;
        $this->detailMediaName = '';
    }

    public function updateMediaName()
    {
        $this->validate([
            'detailMediaName' => 'required|string|max:255',
        ]);

        if ($this->detailMediaId) {
            $media = Media::find($this->detailMediaId);
            if ($media) {
                $media->name = trim($this->detailMediaName);
                $media->save();
                $this->dispatch('notify', message: __('Media title updated successfully!'), type: 'success');
            }
        }
    }

    protected function getQuery()
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

        return $query;
    }

    public function render()
    {
        $mediaItems = $this->getQuery()->paginate(24);
        $detailMedia = $this->detailMediaId ? Media::find($this->detailMediaId) : null;
        $totalFiles = Media::count();
        $totalSizeKB = Media::sum('size') / 1024;
        $totalSizeMB = round($totalSizeKB / 1024, 2);

        return view('livewire.admin.media.media-manager', [
            'mediaItems' => $mediaItems,
            'detailMedia' => $detailMedia,
            'totalFiles' => $totalFiles,
            'totalSizeMB' => $totalSizeMB,
        ]);
    }
}
