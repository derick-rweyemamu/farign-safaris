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
    public bool   $is_mega_menu = false;
    public array  $mega_menu_data = [];

    protected function rules(): array
    {
        return [
            'label'          => 'required|string|max:100',
            'url'            => 'nullable|string|max:500',
            'location'       => 'required|string|in:' . implode(',', array_keys(NavigationMenu::$locations)),
            'page_id'        => 'nullable|exists:pages,id',
            'parent_id'      => 'nullable|exists:navigation_menus,id',
            'sort_order'     => 'integer|min:0',
            'is_external'    => 'boolean',
            'is_active'      => 'boolean',
            'is_mega_menu'   => 'boolean',
            'mega_menu_data' => 'nullable|array',
        ];
    }

    public function resetForm(): void
    {
        $this->label          = '';
        $this->url            = '';
        $this->location       = $this->activeLocation;
        $this->page_id        = null;
        $this->parent_id      = null;
        $this->sort_order     = 0;
        $this->is_external    = false;
        $this->is_active      = true;
        $this->is_mega_menu   = false;
        $this->mega_menu_data = [];
        $this->editingId      = null;
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
        $this->editingId      = $id;
        $this->label          = $item->label ?? '';
        $this->url            = $item->url ?? '';
        $this->location       = $item->location ?? 'header';
        $this->page_id        = $item->page_id;
        $this->parent_id      = $item->parent_id;
        $this->sort_order     = (int)$item->sort_order;
        $this->is_external    = (bool)$item->is_external;
        $this->is_active      = (bool)$item->is_active;
        $this->is_mega_menu   = (bool)$item->is_mega_menu;
        $this->mega_menu_data = $item->mega_menu_data ?? [];

        if ($this->is_mega_menu) {
            if (!isset($this->mega_menu_data['columns'])) {
                $this->mega_menu_data['columns'] = $item->getMegaMenuColumns();
            }
            if (empty($this->mega_menu_data['columns'])) {
                $default = $this->getDefaultMegaMenuData();
                $this->mega_menu_data['columns'] = $default['columns'];
            }
            if (empty($this->mega_menu_data['header_title'])) {
                $this->mega_menu_data['header_title'] = 'Climb & Conquer';
            }
        }

        $this->showModal = true;
    }

    public function updatedIsMegaMenu($value): void
    {
        if ($value && (empty($this->mega_menu_data) || empty($this->mega_menu_data['columns']))) {
            $this->mega_menu_data = $this->getDefaultMegaMenuData();
        }
    }

    public function getDefaultMegaMenuData(): array
    {
        return [
            'header_title' => 'Climb & Conquer',
            'columns'      => [
                [
                    'type'     => 'links',
                    'title'    => 'Mount Kilimanjaro',
                    'items'    => [
                        ['label' => 'Marangu Route – 5 to 6 Days Hike', 'url' => '/kilimanjaro#marangu', 'badge' => 'New'],
                        ['label' => 'Umbwe Route – 5 to 6 Days Hike', 'url' => '/kilimanjaro#umbwe', 'badge' => ''],
                        ['label' => 'Machame Route – 6 to 7 Days Hike', 'url' => '/kilimanjaro#machame', 'badge' => ''],
                        ['label' => 'Rongai Route – 6 to 7 Days Hike', 'url' => '/kilimanjaro#rongai', 'badge' => ''],
                        ['label' => 'Lemosho Route – 6 to 8 Days Hike', 'url' => '/kilimanjaro#lemosho', 'badge' => ''],
                    ],
                    'btn_text' => 'View All Packages',
                    'btn_url'  => '/tours',
                ],
                [
                    'type'     => 'media_list',
                    'title'    => 'Featured Treks',
                    'items'    => [
                        [
                            'title'    => 'Mount Meru',
                            'subtitle' => 'A Majestic Adventure Awaits',
                            'image'    => '/images/mega-menu/mount-meru.jpg',
                            'url'      => '/destinations',
                        ],
                        [
                            'title'    => 'Ngorongoro Highland Trekking',
                            'subtitle' => 'Discover Untamed Beauty.',
                            'image'    => '/images/mega-menu/ngorongoro-trekking.jpg',
                            'url'      => '/destinations',
                        ],
                        [
                            'title'    => 'Usambara Mountains Hiking',
                            'subtitle' => 'Scenic Trails and Untouched Beauty',
                            'image'    => '/images/mega-menu/usambara-hiking.jpg',
                            'url'      => '/destinations',
                        ],
                    ],
                ],
                [
                    'type'     => 'promo',
                    'title'    => 'Conquer the Roof of Africa',
                    'image'    => '/images/mega-menu/kilimanjaro-summit.jpg',
                    'btn_text' => 'Explore the Heights',
                    'btn_url'  => '/kilimanjaro',
                    'bg_color' => '#7d834a',
                ],
            ],
        ];
    }

    public function addColumn(string $type = 'links'): void
    {
        if (!isset($this->mega_menu_data['columns'])) {
            $this->mega_menu_data['columns'] = [];
        }

        if (count($this->mega_menu_data['columns']) >= 4) {
            $this->dispatch('notify', message: __('Maximum 4 columns allowed for optimal layout.'), type: 'warning');
            return;
        }

        if ($type === 'links') {
            $this->mega_menu_data['columns'][] = [
                'type'     => 'links',
                'title'    => 'More Routes & Treks',
                'items'    => [
                    ['label' => 'New Route / Package', 'url' => '/tours', 'badge' => ''],
                ],
                'btn_text' => 'View All',
                'btn_url'  => '/tours',
            ];
        } elseif ($type === 'media_list') {
            $this->mega_menu_data['columns'][] = [
                'type'     => 'media_list',
                'title'    => 'Extra Highlights',
                'items'    => [
                    [
                        'title'    => 'Serengeti Migration',
                        'subtitle' => 'Wild Safari Adventure',
                        'image'    => '/images/mega-menu/ngorongoro-trekking.jpg',
                        'url'      => '/destinations',
                    ],
                ],
            ];
        } elseif ($type === 'promo') {
            $this->mega_menu_data['columns'][] = [
                'type'     => 'promo',
                'title'    => 'Exclusive Safari Offer',
                'image'    => '/images/mega-menu/kilimanjaro-summit.jpg',
                'btn_text' => 'Discover More',
                'btn_url'  => '/tours',
                'bg_color' => '#838a53',
            ];
        }
    }

    public function removeColumn(int $index): void
    {
        if (isset($this->mega_menu_data['columns'][$index])) {
            unset($this->mega_menu_data['columns'][$index]);
            $this->mega_menu_data['columns'] = array_values($this->mega_menu_data['columns']);
        }
    }

    public function moveColumn(int $index, string $direction): void
    {
        if (!isset($this->mega_menu_data['columns'][$index])) {
            return;
        }

        $target = $direction === 'left' ? $index - 1 : $index + 1;
        if ($target < 0 || $target >= count($this->mega_menu_data['columns'])) {
            return;
        }

        $temp = $this->mega_menu_data['columns'][$index];
        $this->mega_menu_data['columns'][$index] = $this->mega_menu_data['columns'][$target];
        $this->mega_menu_data['columns'][$target] = $temp;
    }

    public function addColItem(int $colIndex): void
    {
        if (!isset($this->mega_menu_data['columns'][$colIndex])) {
            return;
        }

        $type = $this->mega_menu_data['columns'][$colIndex]['type'] ?? 'links';
        if ($type === 'links') {
            $this->mega_menu_data['columns'][$colIndex]['items'][] = ['label' => '', 'url' => '', 'badge' => ''];
        } else {
            $this->mega_menu_data['columns'][$colIndex]['items'][] = ['title' => '', 'subtitle' => '', 'image' => '', 'url' => ''];
        }
    }

    public function removeColItem(int $colIndex, int $itemIndex): void
    {
        if (isset($this->mega_menu_data['columns'][$colIndex]['items'][$itemIndex])) {
            unset($this->mega_menu_data['columns'][$colIndex]['items'][$itemIndex]);
            $this->mega_menu_data['columns'][$colIndex]['items'] = array_values($this->mega_menu_data['columns'][$colIndex]['items']);
        }
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
            'label'          => $this->label,
            'url'            => $this->url ?: '#',
            'location'       => $this->location,
            'page_id'        => $this->page_id,
            'parent_id'      => $this->parent_id,
            'sort_order'     => $this->sort_order,
            'is_external'    => $this->is_external,
            'is_active'      => $this->is_active,
            'is_mega_menu'   => $this->is_mega_menu,
            'mega_menu_data' => $this->is_mega_menu ? $this->mega_menu_data : null,
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
