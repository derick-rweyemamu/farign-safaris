<div class="space-y-4 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Posts Management') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $posts->total() }} total posts</p>
        </div>
        <div class="flex items-center gap-3">
            @can('create posts')
                <button wire:click="openModal" class="btn-primary" id="btn-create-post">
                    <x-icon name="plus" class="w-4 h-4" />{{ __('Add Post') }}</button>
            @endcan
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-card rounded-[3px] p-3 shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="flex flex-col sm:flex-row items-center gap-3 justify-between">
            <!-- Left side - Per page and Status -->
            <div class="flex items-center gap-3">
                <!-- Per page -->
                <select wire:model.live="perPage" class="form-input text-xs py-1.5 px-2 w-24">
                    <option value="10">10 / {{ __('page') }}</option>
                    <option value="25">25 / {{ __('page') }}</option>
                    <option value="50">50 / {{ __('page') }}</option>
                    <option value="100">100 / {{ __('page') }}</option>
                </select>

                <!-- Status filter -->
                <select wire:model.live="statusFilter" class="form-input w-full sm:w-40">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Right side - Search -->
            <div class=" sm:w-90 relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by...') }}"
                    class="form-input text-xs py-1.5 !pl-8 w-full">
                <x-icon name="magnifying-glass"
                    class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-900 rounded-[3px] border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                    <tr>
                        <th class="px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Post') }}</th>
                        <th class="px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Author') }}</th>
                        <th class="px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                        <th class="px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">{{ __('Status') }}</th>
                        <th class="px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($posts as $post)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors group">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    @if($post->featured_image)
                                        <img src="{{ asset('storage/' . $post->featured_image) }}"
                                            class="w-10 h-10 rounded-[3px] object-cover bg-gray-100" alt="">
                                    @else
                                        <div
                                            class="w-10 h-10 rounded-[3px] bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                            <x-icon name="photo" class="w-5 h-5 text-gray-400" />
                                        </div>
                                    @endif
                                    <div>
                                        <span
                                            class="text-sm font-semibold text-gray-900 dark:text-white block">{{ $post->title }}</span>
                                        <span class="text-[10px] font-mono text-gray-500 dark:text-gray-400 block">/{{ $post->slug }}</span>
                                        @if($post->category)<span class="text-[10px] uppercase tracking-wider font-bold text-brand-primary mt-1 block">{{ $post->category->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-[10px] font-bold text-indigo-600">
                                        {{ $post->author->initials ?? '??' }}
                                    </div>
                                    <span
                                        class="text-sm text-gray-600 dark:text-gray-400">{{ $post->author->name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span
                                    class="text-xs text-gray-500 dark:text-gray-400">{{ $post->published_at ? $post->published_at->format('M d, Y') : 'Not set' }}</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @can('publish posts')
                                    <button wire:click="togglePublished({{ $post->id }})"
                                        class="inline-flex items-center px-2 py-1 rounded-[3px] text-[10px] font-bold uppercase tracking-wider transition-colors {{ $post->is_published ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                                        {{ $post->is_published ? 'Published' : 'Draft' }}
                                    </button>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-[3px] text-[10px] font-bold uppercase tracking-wider {{ $post->is_published ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                                        {{ $post->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                @endcan
                            </td>
                            <td class="px-5 py-4 text-right">
                                <x-admin.action-dropdown>
                                    @can('edit posts')
                                    <button wire:click="edit({{ $post->id }})">
                                            <x-icon name="pencil-square" class="w-4 h-4 text-blue-500" />
                                            <span>{{ __('Post Settings') }}</span>
                                        </button>
                                        <a href="{{ route('visual-editor', ['type' => 'post', 'id' => $post->id]) }}" target="_blank">
                                            <x-icon name="paint-brush" class="w-4 h-4 text-indigo-500" />
                                            <span>{{ __('Visual Editor') }}</span>
                                        </a>
                                        
                                    @endcan
                                    @can('create posts')
                                        <button wire:click="duplicate({{ $post->id }})">
                                            <x-icon name="document-duplicate" class="w-4 h-4 text-amber-500" />
                                            <span>{{ __('Duplicate') }}</span>
                                        </button>
                                    @endcan
                                    @can('delete posts')
                                        <button wire:click="confirmDelete({{ $post->id }})" class="!text-red-600 hover:!bg-red-50 dark:hover:!bg-red-900/20">
                                            <x-icon name="trash" class="w-4 h-4" />
                                            <span>{{ __('Delete') }}</span>
                                        </button>
                                    @endcan
                                </x-admin.action-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center">
                                <div class="flex flex-col items-center">
                                    <x-icon name="newspaper" class="w-10 h-10 text-gray-300 mb-2" />
                                    <p class="text-gray-500 dark:text-gray-400">{{ __('No posts found.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
            {{ $posts->links() }}
        </div>
    </div>

    <!-- Post Modal -->
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-box max-w-4xl max-h-[90vh] overflow-hidden flex flex-col shadow-2xl">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $editingId ? 'Edit Post' : 'Add New Post' }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">{{ __('Create and publish blog posts') }}</p>
                    </div>
                    <button wire:click="$set('showModal', false)"
                        class="p-2 rounded-[3px] text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit="save" class="flex-1 flex flex-col overflow-hidden min-h-0">
                    <div class="p-6 overflow-y-auto flex-1 min-h-0">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Main Content Column -->
                            <div class="lg:col-span-2 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Post
                                        Title *</label>
                                    <input type="text" wire:model.live="title" class="form-input"
                                        placeholder="{{ __('e.g. 5 Tips for Better Adventure') }}">
                                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">URL Slug
                                            *</label>
                                        @if($isEdit)
                                            <label class="flex items-center gap-2 cursor-pointer group">
                                                <input type="checkbox" wire:model.live="shouldUpdateSlug"
                                                    class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                                <span
                                                    class="text-xs text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors">{{ __('Update
                                                    from title') }}</span>
                                            </label>
                                        @endif
                                    </div>
                                    <div class="flex gap-1 items-center">
                                        <span class="text-gray-400 text-sm">/articles/</span>
                                        <input type="text" wire:model="slug" class="form-input"
                                            placeholder="{{ __('5-tips-for-better-adventure') }}">
                                    </div>
                                    @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Category') }}</label>
                                    <select wire:model="post_category_id" class="form-input text-sm">
                                        <option value="">{{ __('Select Category (Optional)') }}</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('post_category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Excerpt') }}</label>
                                    <textarea wire:model="excerpt" rows="3" class="form-input text-sm"
                                        placeholder="{{ __('Brief summary of the post...') }}"></textarea>
                                    @error('excerpt') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Content') }}</label>
                                    <textarea wire:model="content" rows="15" class="form-input font-mono text-sm"
                                        placeholder="{{ __('Post body...') }}"></textarea>
                                    @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- SEO Settings -->
                                <div class="border-t border-gray-100 dark:border-gray-800 pt-4 mt-2">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ __('Search Engine Optimization') }}</h4>
                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-1.5">{{ __('Meta Title') }}<span class="text-gray-400 font-normal normal-case">(Max 60 chars)</span></label>
                                            <input type="text" wire:model="seo_title" class="form-input text-sm" placeholder="{{ __('e.g. Best Adventure Tips') }}" maxlength="60">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-1.5">{{ __('Meta Description') }}<span class="text-gray-400 font-normal normal-case">(Max 160 chars)</span></label>
                                            <textarea wire:model="seo_description" rows="2" class="form-textarea w-full resize-none text-sm" placeholder="{{ __('A compelling summary of the post...') }}" maxlength="160"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sidebar Settings Column -->
                            <div class="space-y-6">
                                <div
                                    class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-[3px] space-y-4 border border-gray-100 dark:border-gray-800">
                                    <h4 class="text-sm font-bold uppercase tracking-wider text-gray-400">{{ __('Publishing') }}</h4>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Publish
                                            Date') }}</label>
                                        <input type="datetime-local" wire:model="published_at" class="form-input">
                                        @error('published_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Status') }}</label>
                                        <select wire:model="is_published" class="form-input">
                                            <option value="0">{{ __('Draft') }}</option>
                                            <option value="1">{{ __('Published') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <x-admin.image-upload 
                                    :image="$featuredImageUpload" 
                                    :existing="$featured_image" 
                                    label="{{ __('Featured Image') }}" 
                                    id="featured_image_input" 
                                    model="featuredImageUpload" 
                                    uploadFolder="posts"
                                    clearAction="$set('featuredImageUpload', null); $set('featured_image', '')" 
                                />
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-end gap-3 p-5 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                        <button type="button" wire:click="$set('showModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                            <x-icon name="check" class="w-4 h-4" wire:loading.remove />
                            <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading />
                            <span wire:loading.remove>{{ $editingId ? 'Update Post' : 'Create Post' }}</span>
                            <span wire:loading>{{ __('Saving...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <x-admin.delete-modal :show="$showDeleteModal" title="{{ __('Delete Post?') }}"
        description="This action cannot be undone. This blog post will be permanently removed from the site."
        buttonText="Delete Post" />
</div>
