@props([
    'image' => null,
    'existing' => null,
    'label' => __('Featured Image'),
    'recommended' => __('Recommended: 1200x800px. PNG or WEBP. Max 600KB.'),
    'id' => 'image_upload_input',
    'model' => 'featured_image',
    'clearAction' => null,
    'uploadFolder' => 'uploads'
])

@php
    $previewSrc = null;
    if ($image && is_object($image) && method_exists($image, 'temporaryUrl') && method_exists($image, 'isPreviewable') && $image->isPreviewable()) {
        try {
            $previewSrc = $image->temporaryUrl();
        } catch (\Exception $e) {}
    } elseif ($image && is_string($image) && trim($image) !== '') {
        $path = trim($image);
        $previewSrc = str_starts_with($path, 'http') || str_starts_with($path, '/storage') ? $path : \Illuminate\Support\Facades\Storage::url($path);
    } elseif ($existing && is_string($existing) && trim($existing) !== '') {
        $path = trim($existing);
        $previewSrc = str_starts_with($path, 'http') || str_starts_with($path, '/storage') ? $path : \Illuminate\Support\Facades\Storage::url($path);
    }

    $jsId = Str::camel($id);
@endphp

<div class="space-y-2" x-data="{
    isUploading: false,
    handleFileSelect(event) {
        const file = event.target.files[0];
        if (!file) return;

        this.isUploading = true;

        const formData = new FormData();
        formData.append('file', file);
        formData.append('folder', '{{ $uploadFolder }}');

        fetch('{{ route('admin.direct-upload') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(response => {
            if (!response.ok) throw new Error('Upload failed: ' + response.status);
            return response.json();
        })
        .then(data => {
            if (data.success && data.path) {
                $wire['{{ $model }}'] = data.path;
                $wire.$refresh();
            }
        })
        .catch(error => {
            console.error('Direct upload error:', error);
            alert('Image upload failed. Please try again or use the Media Library.');
        })
        .finally(() => {
            this.isUploading = false;
            event.target.value = '';
        });
    }
}">
    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ $label }}</label>
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 bg-white dark:bg-gray-900 p-4 border border-gray-200/60 dark:border-gray-800 rounded-xl shadow-sm">
        <div class="w-24 h-24 rounded-xl overflow-hidden bg-gray-50 dark:bg-gray-950/40 border border-gray-200 dark:border-gray-800 flex items-center justify-center relative shadow-inner group shrink-0">
            @if($previewSrc)
                <img src="{{ $previewSrc }}" class="w-full h-full object-cover">
            @else
                <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                    <x-icon name="photo" class="w-8 h-8" />
                </div>
            @endif

            <!-- Clear Action Overlay Button -->
            @if($previewSrc)
                @if($clearAction)
                    <button type="button" wire:click="{{ $clearAction }}" class="absolute top-1 right-1 p-1 bg-red-600 hover:bg-red-700 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity z-10 shadow-sm" title="{{ __('Remove image') }}">
                        <x-icon name="x-mark" class="w-3.5 h-3.5" />
                    </button>
                @else
                    <button type="button" wire:click="$set('{{ $model }}', null)" class="absolute top-1 right-1 p-1 bg-red-600 hover:bg-red-700 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity z-10 shadow-sm" title="{{ __('Remove image') }}">
                        <x-icon name="x-mark" class="w-3.5 h-3.5" />
                    </button>
                @endif
            @endif

            <!-- Loading Spinner Overlay -->
            <div x-show="isUploading" class="absolute inset-0 bg-black/60 flex items-center justify-center z-10" style="display: none;">
                <x-icon name="arrow-path" class="w-6 h-6 text-white animate-spin" />
            </div>
        </div>

        <div class="flex-1">
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Primary Action: Browse Media Library or Upload New in Modal -->
                <button type="button" 
                        @click="
                            const wire = $wire;
                            const handler = (event) => {
                                const detail = Array.isArray(event.detail) ? event.detail[0] : event.detail;
                                console.log('Media picker chosen event received!', detail, 'Model expected:', '{{ $model }}');
                                if (detail && detail.targetModel === '{{ $model }}') {
                                    console.log('Setting wire property', '{{ $model }}', 'to', detail.storagePath || detail.url);
                                    wire['{{ $model }}'] = detail.storagePath || detail.url;
                                    wire.$refresh();
                                    console.log('Wire property set. Wait for Livewire re-render.');
                                    window.removeEventListener('media-picker-chosen', handler);
                                } else {
                                    console.log('Target model mismatch!', detail.targetModel, 'vs', '{{ $model }}');
                                }
                            };
                            window.addEventListener('media-picker-chosen', handler);
                            Livewire.dispatch('open-media-picker', { targetModel: '{{ $model }}', filterType: 'image', multiple: false });
                        "
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-indigo-600 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs cursor-pointer transition-all select-none shadow-sm">
                    <x-icon name="photo" class="w-4 h-4" />{{ __('Browse Media Library / Upload') }}</button>

                <!-- Secondary Action: Direct Local File Upload (uses AJAX instead of wire:model) -->
                <label for="{{ $id }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-semibold text-xs cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/60 transition-all select-none shadow-sm">
                    <x-icon name="arrow-up-tray" class="w-4 h-4 text-gray-400" />{{ __('Direct Upload') }}</label>
            </div>
            <input type="file" id="{{ $id }}" accept="image/png,image/webp,image/jpeg,image/jpg" @change="handleFileSelect($event)" {{ $attributes->merge(['class' => 'hidden']) }}>
            @if($recommended)
                <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-2">{{ $recommended }}</p>
            @endif
            @error($model) <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
