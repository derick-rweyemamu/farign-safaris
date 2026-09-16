<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Currency;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Currency Management')]
class CurrencyManagement extends Component
{
    use WithPagination, AuthorizesRequests;

    public $showModal = false;
    public $editingCurrency = null;

    // Form fields
    public $code = '';
    public $name = '';
    public $symbol = '';
    public $exchange_rate = 1.0;
    public $decimal_places = 2;
    public $is_active = true;

    protected $rules = [
        'code' => 'required|string|max:10|unique:currencies,code',
        'name' => 'required|string|max:255',
        'symbol' => 'required|string|max:10',
        'exchange_rate' => 'required|numeric|min:0.000001',
        'decimal_places' => 'required|integer|min:0|max:8',
        'is_active' => 'boolean',
    ];

    public function openCreateModal()
    {
        $this->authorize('add currency details');
        $this->reset(['code', 'name', 'symbol', 'exchange_rate', 'decimal_places', 'is_active', 'editingCurrency']);
        $this->showModal = true;
    }

    public function edit(Currency $currency)
    {
        $this->authorize('edit currency details');
        $this->editingCurrency = $currency;
        $this->code = $currency->code;
        $this->name = $currency->name;
        $this->symbol = $currency->symbol;
        $this->exchange_rate = $currency->exchange_rate;
        $this->decimal_places = $currency->decimal_places;
        $this->is_active = $currency->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->authorize($this->editingCurrency ? 'edit currency details' : 'add currency details');
        $rules = $this->rules;
        if ($this->editingCurrency) {
            $rules['code'] = 'required|string|max:10|unique:currencies,code,' . $this->editingCurrency->id;
        }

        $this->validate($rules);

        Currency::updateOrCreate(
            ['id' => $this->editingCurrency?->id],
            [
                'code' => strtoupper($this->code),
                'name' => $this->name,
                'symbol' => $this->symbol,
                'exchange_rate' => $this->exchange_rate,
                'decimal_places' => $this->decimal_places,
                'is_active' => $this->is_active,
            ]
        );

        $this->showModal = false;
        $this->dispatch('notify', message: __('Currency saved successfully!'), type: 'success');
    }

    public function toggleStatus(Currency $currency)
    {
        $this->authorize('delete currency details');
        $currency->update(['is_active' => !$currency->is_active]);
        $this->dispatch('notify', message: __('Status updated!'), type: 'success');
    }

    public function render()
    {
        $this->authorize('view currencies setting');
        return view('livewire.admin.settings.currency-management', [
            'currencies' => Currency::latest()->paginate(10)
        ]);
    }
}
