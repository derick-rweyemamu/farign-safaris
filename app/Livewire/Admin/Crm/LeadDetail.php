<?php

namespace App\Livewire\Admin\Crm;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Client;
use App\Enums\LeadStatus;
use App\Enums\ClientStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Lead Details')]
class LeadDetail extends Component
{
    use AuthorizesRequests;

    public Lead $lead;
    
    // Note form
    public $noteContent = '';
    public $noteType = 'note';

    // Edit modal
    public $showEditModal = false;

    public function mount($id)
    {
        $this->lead = Lead::with(['activities.user', 'assignee', 'tour', 'client'])->findOrFail($id);
    }

    public function updateStatus($statusValue)
    {
        $this->authorize('change lead status');
        $validStatus = LeadStatus::tryFrom($statusValue);
        if ($validStatus) {
            $data = ['status' => $validStatus];
            if (empty($this->lead->assigned_to) && $validStatus === LeadStatus::CONTACTED) {
                $data['assigned_to'] = auth()->id();
            }
            $this->lead->update($data);
            
            // Auto log activity
            $this->lead->activities()->create([
                'user_id' => auth()->id(),
                'type' => 'status_change',
                'content' => 'Status changed to ' . $validStatus->label()
            ]);

            $this->dispatch('notify', message: __('Status updated to') . $validStatus->label(), type: 'success');
            $this->lead->refresh();
        }
    }

    public function addNote()
    {
        $this->authorize('log lead activities');
        $this->validate([
            'noteContent' => 'required|string',
            'noteType' => 'required|in:note,call,email,meeting',
        ]);

        $this->lead->activities()->create([
            'user_id' => auth()->id(),
            'type' => $this->noteType,
            'content' => $this->noteContent,
        ]);

        $this->noteContent = '';
        $this->dispatch('notify', message: __('Activity logged successfully.'), type: 'success');
        $this->lead->load('activities.user');
    }

    public function deleteActivity($id)
    {
        $this->authorize('delete lead activities');
        $activity = LeadActivity::findOrFail($id);
        if ($activity->lead_id === $this->lead->id) {
            $activity->delete();
            $this->dispatch('notify', message: __('Activity removed.'), type: 'success');
            $this->lead->load('activities.user');
        }
    }

    public function convertToClient()
    {
        $this->authorize('change lead status');
        if ($this->lead->client_id) {
            $this->dispatch('notify', message: __('Lead is already converted to a client.'), type: 'error');
            return;
        }

        DB::beginTransaction();
        try {
            // Create Client
            $client = Client::create([
                'name' => $this->lead->name,
                'email' => $this->lead->email,
                'phone' => $this->lead->phone,
                'status' => ClientStatus::ACTIVE,
                'notes' => "Converted from Lead #" . $this->lead->id . "\nCompany: " . $this->lead->company,
            ]);

            // Update Lead
            $this->lead->update([
                'status' => LeadStatus::WON,
                'client_id' => $client->id
            ]);

            // Log activity
            $this->lead->activities()->create([
                'user_id' => auth()->id(),
                'type' => 'status_change',
                'content' => 'Lead converted to Client. Status set to Won.'
            ]);

            DB::commit();

            $this->dispatch('notify', message: __('Lead successfully converted to Client!'), type: 'success');
            return redirect()->route('clients.index');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', message: __('Error converting lead:') . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.admin.crm.lead-detail', [
            'statuses' => LeadStatus::cases(),
        ]);
    }
}
