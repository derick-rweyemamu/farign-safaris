<div class="space-y-4 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <a href="{{ route('admin.destinations.index') }}" class="text-xs text-indigo-500 hover:underline mb-1 inline-block">&larr; Back to Destinations</a>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Attractions for') }} {{ $destination->name }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $attractions->total() }} total attractions</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="create" class="btn-primary">
                <x-icon name="plus" class="w-4 h-4" />{{ __('Add Attraction') }}
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="bg-gray-50/50 dark:bg-gray-800/50 text-xs uppercase text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-800">
                    <tr>
                        <th class="px-4 py-3 font-semibold">{{ __('Image') }}</th>
                        <th class="px-4 py-3 font-semibold">{{ __('Title') }}</th>
                        <th class="px-4 py-3 font-semibold text-center">{{ __('Sort Order') }}</th>
                        <th class="px-4 py-3 font-semibold text-center">{{ __('Status') }}</th>
                        <th class="px-4 py-3 font-semibold text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($attractions as $attraction)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="px-4 py-3 w-20">
                            @if($attraction->featured_image)
                                <img src="{{ asset('storage/' . $attraction->featured_image) }}" class="w-12 h-12 object-cover rounded-md" alt="">
                            @else
                                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-md flex items-center justify-center text-gray-400">
                                    <x-icon name="photo" class="w-5 h-5" />
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            {{ is_array($attraction->title) ? ($attraction->title['en'] ?? reset($attraction->title)) : $attraction->title }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            {{ $attraction->sort_order }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $attraction->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400' : 'bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-400' }}">
                                {{ $attraction->is_active ? __('Active') : __('Inactive') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="edit({{ $attraction->id }})" class="text-gray-400 hover:text-indigo-500 transition-colors" title="{{ __('Edit') }}">
                                    <x-icon name="pencil-square" class="w-4 h-4" />
                                </button>
                                <button wire:click="confirmDelete({{ $attraction->id }})" class="text-gray-400 hover:text-red-500 transition-colors" title="{{ __('Delete') }}">
                                    <x-icon name="trash" class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <x-icon name="map" class="w-8 h-8 mb-2 text-gray-300 dark:text-gray-600" />
                                <p>{{ __('No attractions found.') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100 dark:border-gray-800">
            {{ $attractions->links(data: ['scrollTo' => false]) }}
        </div>
    </div>

    <!-- Modal Form -->
    <x-admin.modal wire:model="showModal" maxWidth="3xl" :title="$isEdit ? __('Edit Attraction') : __('Add Attraction')">
        <form wire:submit="save" class="p-5 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Title (English)') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="title.en" class="form-input w-full" placeholder="e.g. Ndutu Area">
                    @error('title.en') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Content (English)') }}
                    </label>
                    <textarea wire:model="content.en" rows="5" class="form-input w-full" placeholder="Description of the attraction..."></textarea>
                    @error('content.en') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <x-admin.image-upload 
                        model="featured_image" 
                        :existing="$existingFeaturedImage" 
                        :image="$featured_image"
                        label="{{ __('Featured Image') }}" 
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Sort Order') }}
                    </label>
                    <input type="number" wire:model="sort_order" class="form-input w-full">
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="is_active" class="form-checkbox text-indigo-500 rounded border-gray-300">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Is Active') }}</span>
                    </label>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-gray-100 dark:border-gray-800">
                <button type="button" wire:click="$set('showModal', false)" class="btn-secondary">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">{{ __('Save Attraction') }}</span>
                    <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                </button>
            </div>
        </form>
    </x-admin.modal>

    <!-- Delete Modal -->
    <x-admin.delete-modal 
        :show="$showDeleteModal" 
        title="{{ __('Delete Attraction') }}"
        description="{{ __('Are you sure you want to delete this attraction? This action cannot be undone.') }}"
        action="delete"
        cancelAction="$set('showDeleteModal', false)"
        buttonText="{{ __('Yes, Delete') }}"
    />
</div>
