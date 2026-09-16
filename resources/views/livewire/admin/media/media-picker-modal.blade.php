<div>
    @if($isOpen)
    <div class="fixed inset-0 z-[999999] flex items-center justify-center p-4 sm:p-6 bg-black/70 backdrop-blur-sm animate-fadeIn"
         x-data
         x-on:keydown.escape.window="$wire.closePicker()">
        
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-2xl w-full max-w-6xl h-[88vh] flex flex-col overflow-hidden">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex flex-wrap items-center justify-between gap-4 bg-gray-50/70 dark:bg-gray-950/70">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                        <x-icon name="photo" class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">
                            {{ $multiple ? 'Select Multiple Media Files' : 'Select Media File' }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $multiple ? 'Choose existing library files or upload new ones for gallery' : 'Choose from existing library or upload new media' }}
                        </p>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="flex items-center gap-1 bg-gray-200/70 dark:bg-gray-800/80 p-1 rounded-xl">
                    <button type="button"
                            wire:click="$set('activeTab', 'library')"
                            class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $activeTab === 'library' ? 'bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                        <span class="flex items-center gap-1.5">
                            <x-icon name="squares-2x2" class="w-4 h-4" />{{ __('Media Library') }}</span>
                    </button>
                    <button type="button"
                            wire:click="$set('activeTab', 'upload')"
                            class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $activeTab === 'upload' ? 'bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                        <span class="flex items-center gap-1.5">
                            <x-icon name="cloud-arrow-up" class="w-4 h-4" />{{ __('Upload New') }}</span>
                    </button>
                </div>

                <!-- Close Button -->
                <button type="button" wire:click="closePicker" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1 rounded-lg">
                    <x-icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            <!-- Tab 1: Library Content -->
            @if($activeTab === 'library')
                <!-- Filter Bar -->
                <div class="px-6 py-3.5 border-b border-gray-100 dark:border-gray-800/80 bg-white dark:bg-gray-900 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 mr-1">{{ __('Filter:') }}</span>
                        <button type="button" wire:click="$set('filterType', 'all')"
                                class="px-3 py-1 rounded-lg text-xs font-bold transition-all {{ $filterType === 'all' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">{{ __('All') }}</button>
                        <button type="button" wire:click="$set('filterType', 'image')"
                                class="px-3 py-1 rounded-lg text-xs font-bold transition-all {{ $filterType === 'image' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">{{ __('Images') }}</button>
                        <button type="button" wire:click="$set('filterType', 'video')"
                                class="px-3 py-1 rounded-lg text-xs font-bold transition-all {{ $filterType === 'video' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">{{ __('Videos') }}</button>

                        @if(!empty($selectedMediaIds) || $selectedMediaId)
                            <label class="inline-flex items-center gap-1.5 ml-2 px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-200 dark:border-indigo-800 rounded-lg cursor-pointer text-xs font-bold text-indigo-700 dark:text-indigo-300">
                                <input type="checkbox" wire:model.live="showSelectedOnly" class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-indigo-500">
                                <span>{{ __('Selected Only') }}</span>
                            </label>
                        @endif
                    </div>

                    <div class="flex items-center gap-2.5">
                        <div class="relative w-56">
                            <x-icon name="magnifying-glass" class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" />
                            <input type="text"
                                   wire:model.live.debounce.300ms="search"
                                   placeholder="{{ __('Search filename...') }}"
                                   class="w-full bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 rounded-lg pl-9 pr-3 py-1.5 text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <select wire:model.live="sortBy"
                                class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5 text-xs text-gray-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="newest">{{ __('Newest First') }}</option>
                            <option value="oldest">{{ __('Oldest First') }}</option>
                            <option value="smallest">{{ __('Smallest Size') }}</option>
                            <option value="largest">{{ __('Largest Size') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Media Grid -->
                <div class="flex-1 overflow-y-auto p-6 bg-gray-50/60 dark:bg-gray-950/30">
                    @if($mediaItems->count() > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-4">
                            @foreach($mediaItems as $item)
                                @php
                                    $isImage = str_starts_with($item->mime_type, 'image/');
                                    $isSelected = $multiple 
                                        ? in_array($item->id, $selectedMediaIds) 
                                        : ($selectedMediaId === $item->id || in_array($item->id, $selectedMediaIds));
                                @endphp
                                <div wire:click="selectMedia({{ $item->id }})"
                                     class="group relative bg-white dark:bg-gray-900 border rounded-xl overflow-hidden cursor-pointer transition-all hover:shadow-lg {{ $isSelected ? 'border-indigo-600 dark:border-indigo-500 ring-4 ring-indigo-500/20' : 'border-gray-200 dark:border-gray-800 hover:border-gray-300 dark:hover:border-gray-700' }}">
                                    
                                    <!-- Thumbnail -->
                                    <div class="aspect-square bg-gray-100 dark:bg-gray-950 relative overflow-hidden flex items-center justify-center">
                                        @if($isImage)
                                            <img src="{{ $item->getUrl() }}"
                                                 alt="{{ $item->name }}"
                                                 loading="lazy"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 p-4">
                                                <x-icon name="film" class="w-10 h-10 mb-1 text-indigo-500" />
                                                <span class="text-[10px] uppercase font-bold tracking-wider">{{ __('Video File') }}</span>
                                            </div>
                                        @endif

                                        <!-- Selection Check Badge -->
                                        @if($isSelected)
                                            <div class="absolute top-2.5 left-2.5 bg-indigo-600 text-white rounded-full p-1 shadow-lg">
                                                <x-icon name="check" class="w-3.5 h-3.5" />
                                            </div>
                                        @endif

                                        <!-- Delete action -->
                                        <button type="button"
                                                wire:click.stop="deleteMedia({{ $item->id }})"
                                                wire:confirm="Are you sure you want to permanently delete this media item?"
                                                class="absolute top-2.5 right-2.5 bg-red-600/90 hover:bg-red-600 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity shadow"
                                                title="{{ __('Delete Media') }}">
                                            <x-icon name="trash" class="w-3.5 h-3.5" />
                                        </button>
                                    </div>

                                    <!-- Label -->
                                    <div class="p-2.5 bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800/60">
                                        <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate" title="{{ $item->name ?: $item->file_name }}">
                                            {{ $item->name ?: $item->file_name }}
                                        </p>
                                        <div class="flex items-center justify-between text-[10px] text-gray-400 dark:text-gray-500 mt-0.5 font-medium">
                                            <span>{{ number_format($item->size / 1024, 1) }} KB</span>
                                            <span>{{ $item->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $mediaItems->links() }}
                        </div>
                    @else
                        <div class="text-center py-20">
                            <x-icon name="photo" class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
                            <h4 class="text-base font-bold text-gray-700 dark:text-gray-300">{{ __('No media files found') }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Try searching another term, or upload new files.') }}</p>
                            <button type="button"
                                    wire:click="$set('activeTab', 'upload')"
                                    class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow-md transition-all">
                                <x-icon name="cloud-arrow-up" class="w-4 h-4" />{{ __('Upload New Files') }}</button>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Tab 2: Upload New Content -->
            @if($activeTab === 'upload')
                <div class="flex-1 p-8 flex flex-col items-center justify-center bg-gray-50/60 dark:bg-gray-950/30"
                     x-data="{
                        isUploading: false,
                        uploadedFiles: [],
                        errorMessage: '',
                        async handleFiles(event) {
                            const files = event.target.files;
                            if (!files.length) return;

                            this.isUploading = true;
                            this.errorMessage = '';
                            this.uploadedFiles = [];

                            const csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content');

                            for (let i = 0; i < files.length; i++) {
                                const formData = new FormData();
                                formData.append('file', files[i]);
                                formData.append('folder', 'media-library');

                                try {
                                    const response = await fetch('{{ route('admin.direct-upload') }}', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': csrfToken,
                                            'Accept': 'application/json',
                                        },
                                        body: formData,
                                    });

                                    if (!response.ok) {
                                        const errData = await response.json().catch(() => ({}));
                                        throw new Error(errData.message || 'Upload failed: ' + response.status);
                                    }

                                    const data = await response.json();
                                    if (data.success && data.path) {
                                        this.uploadedFiles.push({
                                            name: files[i].name,
                                            path: data.path,
                                        });
                                    }
                                } catch (error) {
                                    console.error('Direct upload error:', error);
                                    this.errorMessage = 'Failed to upload ' + files[i].name + ': ' + error.message;
                                }
                            }

                            this.isUploading = false;
                            event.target.value = '';
                        },
                        async saveToLibrary() {
                            if (!this.uploadedFiles.length) return;
                            this.isUploading = true;
                            const paths = this.uploadedFiles.map(f => f.path);
                            await $wire.registerUploadedMedia(paths);
                            this.uploadedFiles = [];
                            this.isUploading = false;
                        }
                    }">
                    <div class="w-full max-w-lg bg-white dark:bg-gray-900 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-2xl p-10 text-center relative hover:border-indigo-500 transition-all shadow-sm">
                        
                        <div class="w-16 h-16 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mx-auto mb-4">
                            <x-icon name="cloud-arrow-up" class="w-8 h-8" />
                        </div>
                        
                        <h4 class="text-base font-bold text-gray-900 dark:text-white mb-1">{{ __('Upload files directly to library') }}</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">{{ __('Select one or multiple images/videos from your computer. Up to 20MB per file.') }}</p>

                        <label for="modal_media_upload" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold cursor-pointer shadow-md transition-all">
                            <x-icon name="folder-open" class="w-4 h-4" />{{ __('Browse Computer') }}</label>
                        <input type="file"
                               id="modal_media_upload"
                               @change="handleFiles($event)"
                               multiple
                               class="hidden">

                        <!-- Upload Loading indicator -->
                        <div x-show="isUploading" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 rounded-lg text-xs font-semibold" style="display: none;">
                            <x-icon name="arrow-path" class="w-4 h-4 animate-spin" />
                            <span>{{ __('Uploading file(s)...') }}</span>
                        </div>

                        <!-- Error display -->
                        <template x-if="errorMessage">
                            <p class="text-xs font-semibold text-red-600 dark:text-red-400 mt-4" x-text="errorMessage"></p>
                        </template>

                        <!-- Selected files preview before confirm -->
                        <template x-if="uploadedFiles.length > 0">
                            <div class="mt-6 border-t border-gray-200 dark:border-gray-800 pt-4 text-left">
                                <p class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">{{ __('Ready to save to library:') }}</p>
                                <ul class="space-y-1.5 text-xs text-gray-600 dark:text-gray-400 max-h-40 overflow-y-auto">
                                    <template x-for="(file, index) in uploadedFiles" :key="index">
                                        <li class="truncate flex items-center gap-2 bg-gray-50 dark:bg-gray-800/60 px-3 py-1.5 rounded-lg">
                                            <x-icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
                                            <span x-text="file.name"></span>
                                        </li>
                                    </template>
                                </ul>

                                <div class="mt-5 flex justify-end gap-2">
                                    <button type="button"
                                            @click="saveToLibrary()"
                                            class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-md transition-all">{{ __('Save to Library & Select') }}</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            @endif

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50/90 dark:bg-gray-950/90 flex items-center justify-between">
                <div class="text-xs font-semibold text-gray-600 dark:text-gray-400 flex items-center gap-3">
                    @php
                        $countSelected = $multiple ? count($selectedMediaIds) : ($selectedMediaId ? 1 : 0);
                    @endphp
                    @if($countSelected > 0)
                        <span class="text-indigo-600 dark:text-indigo-400 font-bold px-3 py-1 bg-indigo-50 dark:bg-indigo-950/60 rounded-lg border border-indigo-200 dark:border-indigo-800">
                            {{ $countSelected }} item(s) selected
                        </span>
                        <button type="button" wire:click="clearSelection" class="text-gray-500 hover:text-red-600 transition-colors underline">{{ __('Clear selection') }}</button>
                    @else
                        <span>{{ __('Select media from the library') }}</span>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <button type="button"
                            wire:click="closePicker"
                            class="px-5 py-2.5 border border-gray-300 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">{{ __('Cancel') }}</button>
                    <button type="button"
                            wire:click="confirmSelection"
                            @if(($multiple && empty($selectedMediaIds)) || (!$multiple && !$selectedMediaId)) disabled @endif
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl text-xs font-bold shadow-md transition-all">
                        {{ $multiple ? "Add Selected ($countSelected)" : 'Select Media' }}
                    </button>
                </div>
            </div>

        </div>
    </div>
    @endif
</div>
