<?php

namespace App\Livewire\Admin\Cities;
    
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\City as CityModel;
use App\Models\Country;

#[Layout('admin::layouts.app')]
#[Title('Cities')]
class City extends Component {
    use WithPagination, AuthorizesRequests;

    #[Url(history: true)]
    public $search = '';

    #[Url]
    public int $perPage = 10;

    public $city_id, $name, $code, $country_id;
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
        $this->city_id = null;
        $this->name = '';
        $this->code = '';
        $this->country_id = '';
        $this->isEdit = false;
    }

    public function create()
    {
        $this->authorize('create cities');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('edit cities');
        $this->resetFields();
        $city = CityModel::findOrFail($id);
        $this->city_id = $city->id;
        $this->name = $city->name;
        $this->code = $city->code;
        $this->country_id = $city->country_id;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->authorize($this->isEdit ? 'edit cities' : 'create cities');
        $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10',
            'country_id' => 'required|exists:countries,id',
        ]);

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'country_id' => $this->country_id,
        ];

        if ($this->isEdit) {
            CityModel::find($this->city_id)->update($data);
            $this->dispatch('notify', message: __('City updated successfully!'), type: 'success');
        } else {
            CityModel::create($data);
            $this->dispatch('notify', message: __('City created successfully!'), type: 'success');
        }

        $this->showModal = false;
        $this->resetFields();
    }

    public function confirmDelete($id)
    {
        $this->authorize('delete cities');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteCity()
    {
        $this->authorize('delete cities');
        CityModel::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', message: __('City deleted successfully!'), type: 'success');
        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->resetPage();
    }

    public function render()
    {
        $this->authorize('view cities');
        $cities = CityModel::with('country')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('country', function($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy('name', 'asc')
            ->paginate($this->perPage);

        $countries = Country::orderBy('name', 'asc')->get();

        return view('livewire.admin.cities.index', [
            'cities' => $cities,
            'countries' => $countries,
        ]);
    }
}
