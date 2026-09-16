@props([
    'show' => false,
    'title' => __("Delete Item?"),
    'description' => __("This action cannot be undone. This item will be permanently removed."),
    'action' => 'delete',
    'cancelAction' => '$set(\'showDeleteModal\', false)',
    'buttonText' => __("Delete Item")
])

@if($show)
    <div class="modal-backdrop" x-data @keydown.escape.window="$wire.{{ $cancelAction }}">
        <div class="modal-box max-w-sm">
            <div class="p-5 text-center">
                <div class="w-16 h-16 gradient-red rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="trash" class="w-8 h-8 text-white" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    {{ $description }}
                </p>
                <div class="flex items-center justify-center gap-3 mt-6">
                    <button wire:click="{{ $cancelAction }}" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button wire:click="{{ $action }}" class="btn-danger">
                        <x-icon name="trash" class="w-4 h-4" />
                        {{ $buttonText }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

