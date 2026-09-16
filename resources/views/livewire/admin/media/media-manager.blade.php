<div class="space-y-6 animate-fadeIn">
    <!-- Header with Title & Stats -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-gray-900 p-6 rounded-xl border border-gray-200/80 dark:border-gray-800 shadow-sm">
        <div>
            <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                    <x-icon name="photo" class="w-6 h-6" />
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Media Library') }}</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Manage, organize, and upload all website media assets in one place') }}</p>
                </div>
            </div>
        </div>

        <!-- Library Stats Badges -->
        <div class="flex items-center gap-3">
            <button wire:click="syncExistingMedia" 
                    wire:loading.attr="disabled"
                    title="{{ __('Import legacy uploaded images into Media Library') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 text-xs font-semibold transition-all">
                <x-icon name="arrow-path" class="w-4 h-4" wire:loading.class="animate-spin" wire:target="syncExistingMedia" />
                <span>{{ __('Sync Existing Media') }}</span>
            </button>
            <div class="px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-800/80 border border-gray-200/60 dark:border-gray-700/60 text-center">
                <span class="block text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500">{{ __('Total Files') }}</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($totalFiles) }}</span>
            </div>
            <div class="px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-800/80 border border-gray-200/60 dark:border-gray-700/60 text-center">
                <span class="block text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500">{{ __('Total Size') }}</span>
                <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ $totalSizeMB }} MB</span>
            </div>
        </div>
    </div>

    <!-- Drag & Drop Upload Area -->
    <div x-data="{
        isDragging: false,
        isUploading: false,
        uploadedFiles: [],
        errorMessage: '',
        async processFiles(files) {
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
        },
        handleFileInput(event) {
            this.processFiles(event.target.files);
            event.target.value = '';
        },
        handleDrop(event) {
            this.isDragging = false;
            this.processFiles(event.dataTransfer.files);
        },
        async saveToLibrary() {
            if (!this.uploadedFiles.length) return;
            this.isUploading = true;
            const paths = this.uploadedFiles.map(f => f.path);
            await $wire.registerUploadedMedia(paths);
            this.uploadedFiles = [];
            this.isUploading = false;
        }
    }"
         @dragover.prevent="isDragging = true"
         @dragleave.prevent="isDragging = false"
         @drop.prevent="handleDrop($event)"
         class="bg-white dark:bg-gray-900 border-2 border-dashed rounded-xl p-8 transition-all duration-200 relative shadow-sm"
         :class="isDragging ? 'border-indigo-500 bg-indigo-50/40 dark:bg-indigo-950/30' : 'border-gray-300 dark:border-gray-700 hover:border-indigo-400 dark:hover:border-indigo-600'">
         
        <input type="file"
               id="media_manager_upload"
               @change="handleFileInput($event)"
               multiple
               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

        <div class="flex flex-col items-center justify-center text-center">
            <div class="w-14 h-14 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-3">
                <x-icon name="cloud-arrow-up" class="w-7 h-7" />
            </div>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">
                <span class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('Click to upload') }}</span>{{ __('or drag and drop new files here') }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 max-w-sm">{{ __('Supports PNG, JPG, WEBP, SVG, and Videos. Up to 20MB per file. Multi-file upload supported.') }}</p>

            <!-- Loading Spinner during upload -->
            <div x-show="isUploading" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 rounded-lg text-xs font-semibold" style="display: none;">
                <x-icon name="arrow-path" class="w-4 h-4 animate-spin" />
                <span>{{ __('Uploading files...') }}</span>
            </div>

            <!-- Upload error display -->
            <template x-if="errorMessage">
                <p class="text-xs font-semibold text-red-600 dark:text-red-400 mt-3" x-text="errorMessage"></p>
            </template>

            <!-- Pending uploads preview -->
            <template x-if="uploadedFiles.length > 0">
                <div class="mt-6 w-full max-w-md border-t border-gray-200 dark:border-gray-800 pt-4 text-left">
                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Ready to save:') }}</p>
                    <ul class="space-y-1 text-xs text-gray-600 dark:text-gray-400 max-h-36 overflow-y-auto">
                        <template x-for="(file, index) in uploadedFiles" :key="index">
                            <li class="truncate flex items-center gap-2 bg-gray-50 dark:bg-gray-800/60 px-3 py-1.5 rounded">
                                <x-icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
                                <span x-text="file.name"></span>
                            </li>
                        </template>
                    </ul>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button"
                                @click="saveToLibrary()"
                                class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold shadow transition-colors">{{ __('Save All to Library') }}</button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Filters, Search, and Bulk Actions Toolbar -->
    <div class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200/80 dark:border-gray-800 shadow-sm flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        <!-- Filter Pills -->
        <div class="flex items-center gap-1.5 flex-wrap">
            <button type="button" wire:click="$set('filterType', 'all')"
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $filterType === 'all' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">{{ __('All Media') }}</button>
            <button type="button" wire:click="$set('filterType', 'image')"
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $filterType === 'image' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">{{ __('Images') }}</button>
            <button type="button" wire:click="$set('filterType', 'video')"
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $filterType === 'video' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">{{ __('Videos') }}</button>

            <!-- Select All Checkbox -->
            <label class="inline-flex items-center gap-2 ml-3 px-3 py-1.5 bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer text-xs font-medium text-gray-700 dark:text-gray-300">
                <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-indigo-500">
                <span>{{ __('Select All') }}</span>
            </label>
        </div>

        <!-- Search & Sort Controls -->
        <div class="flex items-center gap-3">
            <div class="relative min-w-[200px]">
                <x-icon name="magnifying-glass" class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" />
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="{{ __('Search filenames...') }}"
                       class="w-full bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 rounded-lg pl-9 pr-3 py-1.5 text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <select wire:model.live="sortBy"
                    class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5 text-xs text-gray-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="newest">{{ __('Sort by Newest') }}</option>
                <option value="oldest">{{ __('Sort by Oldest') }}</option>
                <option value="smallest">{{ __('Sort by Smallest') }}</option>
                <option value="largest">{{ __('Sort by Largest') }}</option>
            </select>
        </div>
    </div>

    <!-- Bulk Action Bar -->
    @if(!empty($selectedIds))
        <div class="bg-indigo-600 text-white px-6 py-3 rounded-xl shadow-lg flex items-center justify-between animate-fadeIn">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold">{{ count($selectedIds) }} file(s) selected</span>
            </div>
            <div class="flex items-center gap-3">
                <button type="button"
                        wire:click="bulkDelete"
                        wire:confirm="Are you sure you want to permanently delete the selected media files? This cannot be undone."
                        class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition-colors shadow">{{ __('Delete Selected') }}</button>
                <button type="button"
                        wire:click="$set('selectedIds', [])"
                        class="px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-xs font-semibold rounded-lg transition-colors">{{ __('Clear Selection') }}</button>
            </div>
        </div>
    @endif

    <!-- Media Grid -->
    @if($mediaItems->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($mediaItems as $item)
                @php
                    $isImage = str_starts_with($item->mime_type, 'image/');
                    $isSelected = in_array($item->id, $selectedIds);
                @endphp
                <div class="group relative bg-white dark:bg-gray-900 border rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all {{ $isSelected ? 'border-indigo-600 ring-2 ring-indigo-500/30' : 'border-gray-200 dark:border-gray-800' }}">
                    
                    <!-- Selection Checkbox -->
                    <div class="absolute top-2.5 left-2.5 z-20">
                        <input type="checkbox"
                               value="{{ $item->id }}"
                               wire:model.live="selectedIds"
                               class="w-4 h-4 rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-indigo-500 shadow">
                    </div>

                    <!-- Quick Action Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity z-10 flex flex-col justify-end p-3">
                        <div class="flex items-center justify-between gap-1">
                            <button type="button"
                                    wire:click="showDetails({{ $item->id }})"
                                    class="p-1.5 bg-white/90 dark:bg-gray-800/90 hover:bg-white text-gray-800 dark:text-white rounded-lg shadow transition-colors"
                                    title="{{ __('View Info') }}">
                                <x-icon name="eye" class="w-4 h-4" />
                            </button>
                            <a href="{{ $item->getUrl() }}"
                               target="_blank"
                               download="{{ $item->file_name }}"
                               class="p-1.5 bg-white/90 dark:bg-gray-800/90 hover:bg-white text-gray-800 dark:text-white rounded-lg shadow transition-colors"
                               title="{{ __('Download') }}">
                                <x-icon name="arrow-down-tray" class="w-4 h-4" />
                            </a>
                            <button type="button"
                                    x-data
                                    @click="
                                        navigator.clipboard.writeText('{{ $item->getUrl() }}');
                                        $dispatch('notify', { message: 'URL copied to clipboard!', type: 'success' });
                                    "
                                    class="p-1.5 bg-white/90 dark:bg-gray-800/90 hover:bg-white text-gray-800 dark:text-white rounded-lg shadow transition-colors"
                                    title="{{ __('Copy URL') }}">
                                <x-icon name="clipboard" class="w-4 h-4" />
                            </button>
                            <button type="button"
                                    wire:click="deleteMedia({{ $item->id }})"
                                    wire:confirm="Are you sure you want to permanently delete this file?"
                                    class="p-1.5 bg-red-600/90 hover:bg-red-600 text-white rounded-lg shadow transition-colors"
                                    title="{{ __('Delete File') }}">
                                <x-icon name="trash" class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Media Preview Area -->
                    <div wire:click="showDetails({{ $item->id }})"
                         class="aspect-square bg-gray-100 dark:bg-gray-950 relative overflow-hidden flex items-center justify-center cursor-pointer">
                        @if($isImage)
                            <img src="{{ $item->getUrl() }}"
                                 alt="{{ $item->name }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 p-4">
                                <x-icon name="film" class="w-10 h-10 mb-1.5 text-indigo-500" />
                                <span class="text-[10px] uppercase font-bold tracking-wider text-gray-600 dark:text-gray-400">{{ __('Video File') }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Card Info -->
                    <div class="p-2.5 bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800">
                        <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate" title="{{ $item->name ?: $item->file_name }}">
                            {{ $item->name ?: $item->file_name }}
                        </p>
                        <div class="flex items-center justify-between mt-1 text-[10px] text-gray-400 dark:text-gray-500 font-medium">
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
        <!-- Empty State -->
        <div class="text-center py-20 bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-xl">
            <div class="w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-gray-400 mx-auto mb-4">
                <x-icon name="photo" class="w-8 h-8" />
            </div>
            <h3 class="text-base font-bold text-gray-800 dark:text-white">{{ __('No media found in library') }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">{{ __('Try a different filter or search term, or upload new files using the drag & drop area above.') }}</p>
        </div>
    @endif

    <!-- Details Modal -->
    @if($detailMedia)
        <div class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm animate-fadeIn"
             x-data
             @keydown.escape.window="$wire.closeDetails()">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-950/50">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('File Details') }}</h3>
                    <button type="button" wire:click="closeDetails" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="aspect-square bg-gray-100 dark:bg-gray-950 rounded-xl overflow-hidden flex items-center justify-center border border-gray-200 dark:border-gray-800">
                        @if(str_starts_with($detailMedia->mime_type, 'image/'))
                            <img src="{{ $detailMedia->getUrl() }}" alt="{{ $detailMedia->name }}" class="w-full h-full object-contain">
                        @else
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <x-icon name="film" class="w-12 h-12 mb-2 text-indigo-500" />
                                <span class="text-xs font-bold uppercase">{{ __('Video File') }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-gray-400">{{ __('Media Name / Title') }}</label>
                            <div class="flex items-center gap-2 mt-1">
                                <input type="text" wire:model="detailMediaName" wire:keydown.enter="updateMediaName" class="w-full text-xs bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg px-2.5 py-1.5 text-gray-900 dark:text-white font-semibold">
                                <button type="button"
                                        wire:click="updateMediaName"
                                        wire:loading.attr="disabled"
                                        class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-xs font-semibold shrink-0 shadow">{{ __('Save') }}</button>
                            </div>
                            @error('detailMediaName') <span class="text-[10px] text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-gray-400">{{ __('File Name') }}</label>
                            <p class="text-sm font-bold text-gray-900 dark:text-white break-all">{{ $detailMedia->file_name }}</p>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-gray-400">{{ __('MIME Type') }}</label>
                            <p class="text-xs text-gray-700 dark:text-gray-300 font-medium">{{ $detailMedia->mime_type }}</p>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-gray-400">{{ __('File Size') }}</label>
                            <p class="text-xs text-gray-700 dark:text-gray-300 font-medium">{{ number_format($detailMedia->size / 1024, 2) }} KB</p>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-gray-400">{{ __('Uploaded On') }}</label>
                            <p class="text-xs text-gray-700 dark:text-gray-300 font-medium">{{ $detailMedia->created_at->format('F d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-gray-400">{{ __('Public URL') }}</label>
                            <div class="flex items-center gap-2 mt-1">
                                <input type="text" readonly value="{{ $detailMedia->getUrl() }}" class="w-full text-xs bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg px-2.5 py-1.5 text-gray-600 dark:text-gray-400">
                                <button type="button"
                                        x-data
                                        @click="
                                            navigator.clipboard.writeText('{{ $detailMedia->getUrl() }}');
                                            $dispatch('notify', { message: 'URL copied to clipboard!', type: 'success' });
                                        "
                                        class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-xs font-semibold shrink-0 shadow">{{ __('Copy') }}</button>
                            </div>
                        </div>
                        <div class="pt-2 flex justify-end gap-2">
                            <a href="{{ $detailMedia->getUrl() }}"
                               target="_blank"
                               download="{{ $detailMedia->file_name }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg text-xs font-bold transition-colors">
                                <x-icon name="arrow-down-tray" class="w-4 h-4" />{{ __('Download') }}</a>
                            <button type="button"
                                    wire:click="deleteMedia({{ $detailMedia->id }})"
                                    wire:confirm="Permanently delete this media file?"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg text-xs font-bold shadow transition-colors">
                                <x-icon name="trash" class="w-4 h-4" />{{ __('Delete') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
