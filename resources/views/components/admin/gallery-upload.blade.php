@props([
    'gallery' => [],
    'existing' => [],
    'label' => __('Gallery Images'),
    'id' => 'gallery_input',
    'model' => 'gallery',
    'removeExistingAction' => 'removeGalleryImage',
    'removeTempAction' => 'removeTempGalleryImage',
])

<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
            {{ $label }}
        </label>

        <!-- Primary Action: Open Media Library Modal (Multi-select) -->
        <button type="button" 
                x-data
                @click="
                    const wire = $wire;
                    const handler = (event) => {
                        const detail = Array.isArray(event.detail) ? event.detail[0] : event.detail;
                        if (detail && detail.targetModel === '{{ $model }}') {
                            const paths = (detail.items || []).map(item => item.storagePath || item.url);
                            wire.addGalleryFromLibrary('{{ $model }}', paths);
                            window.removeEventListener('media-picker-chosen', handler);
                        }
                    };
                    window.addEventListener('media-picker-chosen', handler);
                    Livewire.dispatch('open-media-picker', { targetModel: '{{ $model }}', filterType: 'image', multiple: true });
                "
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-sm transition-all select-none">
            <x-icon name="photo" class="w-4 h-4" />{{ __('Add from Media Library / Upload') }}</button>
    </div>

    <!-- Direct Drag & Drop Upload Area -->
    <div 
        x-data="{ isDragging: false }"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="isDragging = false"
        class="relative border-2 border-dashed rounded-xl p-6 text-center transition-all duration-200"
        :class="isDragging ? 'border-indigo-500 bg-indigo-50/40 dark:bg-indigo-950/30' : 'border-gray-200 dark:border-gray-800 hover:border-indigo-400 dark:hover:border-indigo-600 bg-gray-50/50 dark:bg-gray-900/40'"
    >
        <input 
            type="file" 
            id="{{ $id }}" 
            wire:model="{{ $model }}" 
            multiple 
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
        >
        
        <div class="flex flex-col items-center justify-center space-y-2">
            <div class="w-12 h-12 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                <x-icon name="cloud-arrow-up" class="w-6 h-6 animate-pulse" />
            </div>
            <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                <span class="text-indigo-600 dark:text-indigo-400 font-bold hover:underline">{{ __('Click to upload directly') }}</span>{{ __('or drag and drop files here') }}</div>
            <p class="text-[10px] text-gray-400">{{ __('PNG, JPG, or WEBP. Max 600KB per image.') }}</p>

            <!-- Loading Spinner -->
            <div wire:loading wire:target="{{ $model }}" class="text-xs text-indigo-600 dark:text-indigo-400 font-bold">
                <div class="flex items-center gap-2">
                    <x-icon name="arrow-path" class="w-4 h-4 animate-spin" />
                    <span>{{ __('Processing gallery upload...') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Display -->
    @error($model . '.*')
        <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>
    @enderror

    <!-- Preview Grid -->
    @if(!empty($existing) || !empty($gallery))
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3 pt-2">
            <!-- Existing Saved Images -->
            @foreach($existing as $index => $path)
                @if($path)
                    @php
                        $src = str_starts_with($path, 'http') || str_starts_with($path, '/storage') ? $path : \Illuminate\Support\Facades\Storage::url($path);
                    @endphp
                    <div class="relative group aspect-square rounded-xl overflow-hidden border border-gray-200 dark:border-gray-800 bg-gray-100 dark:bg-gray-950 shadow-sm transition-all hover:shadow-md">
                        <img src="{{ $src }}" class="w-full h-full object-cover">
                        <!-- Actions Overlay -->
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                            <button 
                                type="button" 
                                wire:click="{{ $removeExistingAction }}({{ $index }})" 
                                class="p-1.5 bg-red-600 text-white rounded-full hover:bg-red-700 shadow transition-colors"
                                title="{{ __('Remove Saved Image') }}"
                            >
                                <x-icon name="trash" class="w-4 h-4" />
                            </button>
                        </div>
                        <span class="absolute bottom-1.5 left-1.5 text-[9px] font-bold tracking-wider uppercase px-1.5 py-0.5 rounded bg-gray-950/70 text-gray-300">{{ __('Saved') }}</span>
                    </div>
                @endif
            @endforeach

            <!-- Newly Added Images (either temporary uploads or library strings) -->
            @foreach($gallery as $index => $file)
                @if($file)
                    @php
                        $tempUrl = null;
                        $isLibraryString = false;

                        if (is_object($file) && method_exists($file, 'isPreviewable') && $file->isPreviewable()) {
                            try {
                                $tempUrl = $file->temporaryUrl();
                            } catch (\Exception $e) {}
                        } elseif (is_string($file) && trim($file) !== '') {
                            $isLibraryString = true;
                            $path = trim($file);
                            $tempUrl = str_starts_with($path, 'http') || str_starts_with($path, '/storage') ? $path : \Illuminate\Support\Facades\Storage::url($path);
                        }
                    @endphp

                    @if($tempUrl)
                        <div class="relative group aspect-square rounded-xl overflow-hidden border-2 border-indigo-500/60 bg-gray-100 dark:bg-gray-950 shadow-sm transition-all hover:shadow-md">
                            <img src="{{ $tempUrl }}" class="w-full h-full object-cover">
                            <!-- Actions Overlay -->
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                <button 
                                    type="button" 
                                    wire:click="{{ $removeTempAction }}({{ $index }})" 
                                    class="p-1.5 bg-red-600 text-white rounded-full hover:bg-red-700 shadow transition-colors"
                                    title="{{ __('Remove Image') }}"
                                    wire:key="temp-gallery-{{ $index }}"
                                >
                                    <x-icon name="trash" class="w-4 h-4" />
                                </button>
                            </div>
                            @if($isLibraryString)
                                <span class="absolute bottom-1.5 left-1.5 text-[9px] font-bold tracking-wider uppercase px-1.5 py-0.5 rounded bg-indigo-600 text-white">{{ __('Library') }}</span>
                            @else
                                <span class="absolute bottom-1.5 left-1.5 text-[9px] font-bold tracking-wider uppercase px-1.5 py-0.5 rounded bg-emerald-600 text-white">{{ __('New') }}</span>
                            @endif
                        </div>
                    @endif
                @endif
            @endforeach
        </div>
    @endif
</div>
