<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Bank;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Banks Management')]
class BanksList extends Component
{
    use WithPagination, AuthorizesRequests;

    public string $search = '';
    public int $perPage = 10;

    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public string $name = '';
    public string $branch = '';
    public string $account_number = '';
    public bool $status = true;

    protected function rules(): array
    {
        return [
            'name'           => 'required|string|max:255',
            'branch'         => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'status'         => 'boolean',
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->authorize('create banks');
        $this->reset(['name', 'branch', 'account_number', 'status', 'editingId']);
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $this->authorize('edit banks');
        $bank = Bank::findOrFail($id);
        $this->editingId = $bank->id;
        $this->name = $bank->name;
        $this->branch = $bank->branch ?? '';
        $this->account_number = $bank->account_number ?? '';
        $this->status = $bank->status;
        $this->showModal = true;
    }

    public function save()
    {
        $this->authorize($this->editingId ? 'edit banks' : 'create banks');
        $this->validate();

        $data = [
            'name' => $this->name,
            'branch' => $this->branch ?: null,
            'account_number' => $this->account_number ?: null,
            'status' => $this->status,
        ];

        if ($this->editingId) {
            Bank::findOrFail($this->editingId)->update($data);
        } else {
            Bank::create($data);
        }

        $this->dispatch('notify', message: __('Bank saved successfully!'), type: 'success');
        $this->showModal = false;
    }

    public function confirmDelete($id)
    {
        $this->authorize('delete banks');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $this->authorize('delete banks');
        Bank::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', message: __('Bank deleted successfully!'), type: 'success');
        $this->showDeleteModal = false;
    }

    public function toggleStatus($id)
    {
        $this->authorize('edit banks');
        $bank = Bank::findOrFail($id);
        $bank->update(['status' => !$bank->status]);
        $this->dispatch('notify', message: __('Status updated!'), type: 'success');
    }

    public function render()
    {
        $banks = Bank::withCount('employees')
            ->where('name', 'like', "%{$this->search}%")
            ->orWhere('branch', 'like', "%{$this->search}%")
            ->orWhere('account_number', 'like', "%{$this->search}%")
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.admin.settings.banks-list', compact('banks'));
    }
}
