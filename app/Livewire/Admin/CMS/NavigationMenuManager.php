<?php

namespace App\Livewire\Admin\CMS;

use App\Models\NavigationMenu;
use App\Models\Page;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Navigation Menu Manager')]
class NavigationMenuManager extends Component
{
    use AuthorizesRequests;

    public string $activeLocation = 'header';

    // Form state
    public bool $showModal      = false;
    public bool $showDeleteModal = false;
    public ?int $editingId      = null;
    public ?int $deletingId     = null;

    // Form fields
    public string $label        = '';
    public string $url          = '';
    public string $location     = 'header';
    public ?int   $page_id      = null;
    public ?int   $parent_id    = null;
    public int    $sort_order   = 0;
    public bool   $is_external  = false;
    public bool   $is_active    = true;

    protected function rules(): array
    {
        return [
            'label'        => 'required|string|max:100',
            'url'          => 'nullable|string|max:500',
            'location'     => 'required|string|in:' . implode(',', array_keys(NavigationMenu::$locations)),
            'page_id'      => 'nullable|exists:pages,id',
            'parent_id'    => 'nullable|exists:navigation_menus,id',
            'sort_order'   => 'integer|min:0',
            'is_external'  => 'boolean',
            'is_active'    => 'boolean',
        ];
    }

    public function resetForm(): void
    {
        $this->label        = '';
        $this->url          = '';
        $this->location     = $this->activeLocation;
        $this->page_id      = null;
        $this->parent_id    = null;
        $this->sort_order   = 0;
        $this->is_external  = false;
        $this->is_active    = true;
        $this->editingId    = null;
        $this->resetErrorBag();
    }

    public function openModal(): void
    {
        $this->resetForm();
        $this->location   = $this->activeLocation;
        $this->sort_order = NavigationMenu::where('location', $this->activeLocation)->max('sort_order') + 1;
        $this->showModal  = true;
    }

    public function edit(int $id): void
    {
        $item = NavigationMenu::findOrFail($id);
        $this->editingId    = $id;
        $this->label        = $item->label ?? '';
        $this->url          = $item->url ?? '';
        $this->location     = $item->location ?? 'header';
        $this->page_id      = $item->page_id;
        $this->parent_id    = $item->parent_id;
        $this->sort_order   = (int)$item->sort_order;
        $this->is_external  = (bool)$item->is_external;
        $this->is_active    = (bool)$item->is_active;
        $this->showModal    = true;
    }

    public function save(): void
    {
        $this->validate();

        // If a page is selected and no manual URL provided, auto-fill from page slug
        if ($this->page_id && empty($this->url)) {
            $page = Page::find($this->page_id);
            if ($page) {
                $this->url = '/p/' . $page->slug;
            }
        }

        // Prevent item from being its own parent
        if ($this->editingId && $this->editingId == $this->parent_id) {
            $this->parent_id = null;
        }

        $data = [
            'label'        => $this->label,
            'url'          => $this->url ?: '#',
            'location'     => $this->location,
            'page_id'      => $this->page_id,
            'parent_id'    => $this->parent_id,
            'sort_order'   => $this->sort_order,
            'is_external'  => $this->is_external,
            'is_active'    => $this->is_active,
        ];

        if ($this->editingId) {
            NavigationMenu::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', message: __('Menu item updated.'), type: 'success');
        } else {
            NavigationMenu::create($data);
            $this->dispatch('notify', message: __('Menu item added.'), type: 'success');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId     = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            NavigationMenu::findOrFail($this->deletingId)->delete();
            $this->dispatch('notify', message: __('Menu item deleted.'), type: 'success');
        }
        $this->showDeleteModal = false;
        $this->deletingId     = null;
    }

    public function moveUp(int $id): void
    {
        $item = NavigationMenu::findOrFail($id);
        $prev = NavigationMenu::where('location', $item->location)
            ->where('parent_id', $item->parent_id)
            ->where('sort_order', '<', $item->sort_order)
            ->orderByDesc('sort_order')
            ->first();

        if ($prev) {
            [$item->sort_order, $prev->sort_order] = [$prev->sort_order, $item->sort_order];
            $item->save();
            $prev->save();
        }
    }

    public function moveDown(int $id): void
    {
        $item = NavigationMenu::findOrFail($id);
        $next = NavigationMenu::where('location', $item->location)
            ->where('parent_id', $item->parent_id)
            ->where('sort_order', '>', $item->sort_order)
            ->orderBy('sort_order')
            ->first();

        if ($next) {
            [$item->sort_order, $next->sort_order] = [$next->sort_order, $item->sort_order];
            $item->save();
            $next->save();
        }
    }

    public function toggleActive(int $id): void
    {
        $item = NavigationMenu::findOrFail($id);
        $item->update(['is_active' => !$item->is_active]);
        $this->dispatch('notify', message: __('Visibility updated.'), type: 'success');
    }

    public function render()
    {
        // Load items with children
        $items = NavigationMenu::where('location', $this->activeLocation)
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        $pages = Page::where('is_published', true)->orderBy('title')->get(['id', 'title', 'slug']);
        
        $parentOptions = NavigationMenu::where('location', $this->activeLocation)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return view('livewire.admin.cms.navigation-menu-manager', [
            'items'         => $items,
            'pages'         => $pages,
            'parentOptions' => $parentOptions,
            'locations'     => NavigationMenu::$locations,
        ]);
    }
}
