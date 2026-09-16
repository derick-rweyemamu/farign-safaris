<?php

namespace App\Livewire\Admin\Countries;
    
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Country as CountryModel;

#[Layout('admin::layouts.app')]
#[Title('Countries')]
class Country extends Component {
    use WithPagination, AuthorizesRequests;

    #[Url(history: true)]
    public $search = '';

    #[Url]
    public int $perPage = 10;

    public $country_id, $name, $code, $phone_code, $flag;
    public $isEdit = false;
    public $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function resetFields()
    {
        $this->country_id = null;
        $this->name = '';
        $this->code = '';
        $this->phone_code = '';
        $this->flag = '';
        $this->isEdit = false;
    }

    public function create()
    {
        $this->authorize('create countries');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('edit countries');
        $this->resetFields();
        $country = CountryModel::findOrFail($id);
        $this->country_id = $country->id;
        $this->name = $country->name;
        $this->code = $country->code;
        $this->phone_code = $country->phone_code;
        $this->flag = $country->flag;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->authorize($this->isEdit ? 'edit countries' : 'create countries');
        $this->validate([
            'name' => 'required|string|max:255|unique:countries,name,' . ($this->country_id ?? 'NULL'),
            'code' => 'required|string|max:3',
        ]);

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'phone_code' => $this->phone_code,
            'flag' => $this->flag,
        ];

        if ($this->isEdit) {
            CountryModel::find($this->country_id)->update($data);
            $this->dispatch('notify', message: __('Country updated successfully!'), type: 'success');
        } else {
            CountryModel::create($data);
            $this->dispatch('notify', message: __('Country created successfully!'), type: 'success');
        }

        $this->showModal = false;
        $this->resetFields();
    }

    public function confirmDelete($id)
    {
        $this->authorize('delete countries');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteCountry()
    {
        $this->authorize('delete countries');
        CountryModel::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', message: __('Country deleted successfully!'), type: 'success');
        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->resetPage();
    }

    public function render()
    {
        $this->authorize('view countries');
        $countries = CountryModel::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.countries.index', [
            'countries' => $countries,
        ]);
    }
}