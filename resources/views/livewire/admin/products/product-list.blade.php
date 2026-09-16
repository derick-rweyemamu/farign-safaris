<div class="space-y-4 animate-fade-in">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Products') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $products->total() }} total products</p>
        </div>
        <div class="flex items-center gap-3">
            @can('view product reports')
                <button wire:click="openReportModal" class="btn-secondary">
                    <x-icon name="document-chart-bar" class="w-4 h-4" />{{ __('Reports') }}</button>
            @endcan
            @can('create product')
                <button wire:click="create" class="btn-primary">
                    <x-icon name="plus" class="w-4 h-4" />{{ __('Add Product') }}</button>
            @endcan
        </div>
    </div>

    <!-- Reports Modal -->
    <x-admin.report-modal title="{{ __('Products Report') }}" :show="$showReportModal">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Category') }}</label>
            <select wire:model="category" class="form-input text-sm">
                <option value="">{{ __('All Categories') }}</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Status') }}</label>
            <select wire:model="statusFilter" class="form-input text-sm">
                <option value="">{{ __('All Statuses') }}</option>
                <option value="active">{{ __('Active') }}</option>
                <option value="inactive">{{ __('Inactive') }}</option>
            </select>
        </div>
    </x-admin.report-modal>

    <!-- Filters -->
    <div class="bg-card rounded-[3px] p-3 shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="flex flex-col sm:flex-row items-center gap-3 justify-between">
            <!-- Left side - Per page and Status (small widths) -->
            <div class="flex items-center gap-3">
                <!-- Per page -->
                <select wire:model.live="perPage" class="form-input text-xs py-1.5 px-2 w-24">
                    <option value="10">10 / {{ __('page') }}</option>
                    <option value="25">25 / {{ __('page') }}</option>
                    <option value="50">50 / {{ __('page') }}</option>
                    <option value="100">100 / {{ __('page') }}</option>
                </select>

                <!-- Status filter -->
                <select wire:model.live="statusFilter" class="form-input text-xs py-1.5 px-2 w-36">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active" selected>{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>
            </div>

            <!-- Right side - Search with medium width -->
            <div class=" sm:w-90 relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by...') }}"
                    class="form-input text-xs py-1.5 !pl-8 w-full">
                <x-icon name="magnifying-glass"
                    class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-16">{{ __('Image') }}</th>
                        <th>{{ __('Product Details') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Pricing') }}</th>
                        <th>{{ __('Stock') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">
                            <div class="flex justify-end">{{ __('Actions') }}</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr wire:key="product-{{ $product->id }}" class="animate-fade-in">
                            <td>
                                <div
                                    class="w-10 h-10 rounded-[3px] overflow-hidden bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-100 dark:border-gray-700">
                                    @if($product->image)<img src="{{ asset('storage/' . $product->image) }}" alt=""
                                            class="w-full h-full object-cover">
                                    @else
                                        <x-icon name="photo" class="w-5 h-5 text-gray-400" />
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-col space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="font-semibold text-gray-800 dark:text-gray-100 leading-tight">{{ $product->name }}</span>
                                    </div>
                                    <div
                                        class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                        <span>SKU: {{ $product->sku }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span
                                    class="text-sm px-2 py-1 rounded bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                                    {{ $product->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td>
                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-bold text-indigo-600 dark:text-indigo-400">${{ number_format($product->unit_price, 2) }}</span>
                                    <span class="text-[10px] text-gray-400">Cost:
                                        ${{ number_format($product->cost_price, 2) }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-medium {{ $product->stock_quantity <= 5 ? 'text-red-600 font-bold' : 'text-gray-700 dark:text-gray-300' }}">
                                        {{ $product->stock_quantity }} {{ $product->unit }}
                                    </span>
                                    @if($product->stock_quantity <= 5)
                                        <span class="text-[10px] text-red-500 animate-pulse">{{ __('Low Stock') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <x-admin.action-dropdown>
                                    @can('edit product')
                                        <button wire:click="edit({{ $product->id }})"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors"
                                            title="{{ __('Edit') }}">
                                            <x-icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                    @endcan
                                    @can('delete product')
                                        <button wire:click="confirmDelete({{ $product->id }})"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                            title="{{ __('Delete') }}">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    @endcan
                                </x-admin.action-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <x-icon name="archive-box"
                                    class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" />
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No products found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-box max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">
                <div
                    class="flex-shrink-0 flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $isEdit ? 'Edit Product' : 'Add New Product' }}
                    </h3>
                    <button wire:click="$set('showModal', false)"
                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit="save" class="flex-1 flex flex-col overflow-hidden">
                    <div class="p-5 space-y-4 overflow-y-auto flex-1">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <!-- Name -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Product
                                    Name
                                    *</label>
                                <input type="text" wire:model.live="name" class="form-input"
                                    placeholder="{{ __('e.g. Wireless Headphones') }}">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Slug -->
                            <div class="md:col-span-2">
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Slug *</label>
                                    @if($isEdit)
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="checkbox" wire:model.live="shouldUpdateSlug"
                                                class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                            <span
                                                class="text-xs text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors">{{ __('Update
                                                from name') }}</span>
                                        </label>
                                    @endif
                                </div>
                                <input type="text" wire:model="slug" class="form-input bg-gray-50 dark:bg-gray-800/50"
                                    placeholder="{{ __('product-slug') }}">
                                @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Category & SKU -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Category
                                    *</label>
                                <select wire:model="category_id" class="form-input">
                                    <option value="">{{ __('Select Category') }}</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">SKU
                                    *</label>
                                <input type="text" wire:model="sku" class="form-input" placeholder="{{ __('PROD-001') }}">
                                @error('sku') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Pricing -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Unit Price
                                    *</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                    <input type="number" step="0.01" wire:model="unit_price" class="form-input !pl-8"
                                        placeholder="0.00">
                                </div>
                                @error('unit_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Cost
                                    Price') }}</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                    <input type="number" step="0.01" wire:model="cost_price" class="form-input !pl-8"
                                        placeholder="0.00">
                                </div>
                                @error('cost_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Stock & Unit -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Stock
                                    Quantity') }}</label>
                                <input type="number" wire:model="stock_quantity" class="form-input" placeholder="0">
                                @error('stock_quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Unit (e.g.
                                    pcs,
                                    kg) *</label>
                                <input type="text" wire:model="unit" class="form-input" placeholder="{{ __('pcs') }}">
                                @error('unit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Description') }}</label>
                            <textarea wire:model="description" class="form-input min-h-[80px]"
                                placeholder="{{ __('Product description...') }}"></textarea>
                            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>



                        <!-- Image & Status -->
                        <div class="flex flex-col sm:flex-row gap-5">
                            <div class="flex-1">
                                <x-admin.image-upload 
                                    :image="$image" 
                                    :existing="$existingImage" 
                                    label="{{ __('Product Image') }}" 
                                    id="product_image_input" 
                                    model="image" 
                                    clearAction="$set('image', null); $set('existingImage', '')" 
                                />
                            </div>
                            <div class="flex-1">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Status') }}</label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" wire:model="is_active"
                                        class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Active and visible') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" wire:click="$set('showModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                            <x-icon name="check" class="w-4 h-4" wire:loading.remove />
                            <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading />
                            <span wire:loading.remove>{{ $isEdit ? 'Update Product' : 'Create Product' }}</span>
                            <span wire:loading>{{ __('Processing...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showDeleteModal', false)">
            <div class="modal-box max-w-sm p-5 text-center">
                <div class="w-16 h-16 gradient-red rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="trash" class="w-8 h-8 text-white" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Delete Product?') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Are you sure you want to delete this product? This action cannot be undone.') }}</p>
                <div class="flex items-center justify-center gap-3 mt-6">
                    <button wire:click="$set('showDeleteModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button wire:click="deleteProduct" class="btn-danger">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
