<?php

namespace App\Livewire\Admin\Crm;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Lead;
use App\Models\Tour;
use App\Models\User;
use App\Enums\LeadStatus;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Leads Pipeline')]
class LeadList extends Component
{
    use WithPagination, AuthorizesRequests;

    #[Url(history: true)]
    public $search = '';

    #[Url]
    public $statusFilter = '';

    #[Url]
    public int $perPage = 10;

    public $showModal = false;
    public $isEdit = false;
    public $lead_id;
    
    // Form fields
    public $name, $email, $phone, $company, $source, $status, $estimated_budget, $travel_date, $assigned_to, $tour_id;

    public $showDeleteModal = false;
    public $deletingId = null;

    public function mount()
    {
        // Default status
        $this->status = LeadStatus::NEW->value;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function resetFields()
    {
        $this->lead_id = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->company = '';
        $this->source = '';
        $this->status = LeadStatus::NEW->value;
        $this->estimated_budget = '';
        $this->travel_date = '';
        $this->assigned_to = null;
        $this->tour_id = null;
        $this->isEdit = false;
    }

    public function create()
    {
        $this->authorize('create leads');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('edit leads');
        $this->resetFields();
        $lead = Lead::findOrFail($id);
        
        $this->lead_id = $lead->id;
        $this->name = $lead->name;
        $this->email = $lead->email;
        $this->phone = $lead->phone;
        $this->company = $lead->company;
        $this->source = $lead->source;
        $this->status = $lead->status->value;
        $this->estimated_budget = $lead->estimated_budget;
        $this->travel_date = $lead->travel_date ? $lead->travel_date->format('Y-m-d') : null;
        $this->assigned_to = $lead->assigned_to;
        $this->tour_id = $lead->tour_id;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->authorize($this->isEdit ? 'edit leads' : 'create leads');
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'status' => 'required|string',
            'estimated_budget' => 'nullable|numeric|min:0',
            'travel_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'tour_id' => 'nullable|exists:tours,id',
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'source' => $this->source,
            'status' => $this->status,
            'estimated_budget' => $this->estimated_budget ?: null,
            'travel_date' => $this->travel_date ?: null,
            'assigned_to' => $this->assigned_to ?: null,
            'tour_id' => $this->tour_id ?: null,
        ];

        if (!$this->isEdit && empty($data['assigned_to'])) {
            $data['assigned_to'] = auth()->id();
        }

        if ($this->isEdit) {
            $lead = Lead::findOrFail($this->lead_id);
            
            // Prevent lead stealing
            if (!empty($lead->assigned_to) && $lead->assigned_to != $data['assigned_to']) {
                if (!auth()->user()->hasAnyRole(['super admin', 'admin', 'manager'])) {
                    $this->addError('assigned_to', 'You do not have permission to reassign an already assigned lead.');
                    return;
                }
            }

            if (empty($lead->assigned_to) && empty($data['assigned_to']) && $data['status'] === LeadStatus::CONTACTED->value) {
                $data['assigned_to'] = auth()->id();
            }
            $lead->update($data);
            $this->dispatch('notify', message: __('Lead updated successfully!'), type: 'success');
        } else {
            Lead::create($data);
            $this->dispatch('notify', message: __('Lead created successfully!'), type: 'success');
        }

        $this->showModal = false;
        $this->resetFields();
    }

    public function confirmDelete($id)
    {
        $this->authorize('delete leads');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteLead()
    {
        $this->authorize('delete leads');
        if ($this->deletingId) {
            Lead::findOrFail($this->deletingId)->delete();
            $this->dispatch('notify', message: __('Lead deleted successfully!'), type: 'success');
            $this->showDeleteModal = false;
            $this->deletingId = null;
        }
    }

    public function render()
    {
        $leads = Lead::with(['assignee', 'tour'])
            ->when($this->search, function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('company', 'like', "%{$this->search}%");
            })
            ->when($this->statusFilter, function($q) {
                $q->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.crm.lead-list', [
            'leads' => $leads,
            'tours' => Tour::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
            'statuses' => LeadStatus::cases(),
        ]);
    }
}
