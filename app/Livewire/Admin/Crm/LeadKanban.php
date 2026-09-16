<?php

namespace App\Livewire\Admin\Crm;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Lead;
use App\Enums\LeadStatus;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Leads Kanban')]
class LeadKanban extends Component
{
    use AuthorizesRequests;

    public function updateStatus($leadId, $statusValue)
    {
        $this->authorize('change lead status');
        $lead = Lead::findOrFail($leadId);
        
        $validStatus = LeadStatus::tryFrom($statusValue);
        if ($validStatus) {
            $data = ['status' => $validStatus];
            if (empty($lead->assigned_to) && $validStatus === LeadStatus::CONTACTED) {
                $data['assigned_to'] = auth()->id();
            }
            $lead->update($data);
            $this->dispatch('notify', message: __('Lead status updated!'), type: 'success');
        }
    }

    public function render()
    {
        // Load all leads ordered by created_at DESC to show newest on top in columns
        $leads = Lead::with(['assignee', 'tour'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(fn($lead) => $lead->status->value);

        return view('livewire.admin.crm.lead-kanban', [
            'leadsByStatus' => $leads,
            'statuses' => LeadStatus::cases(),
        ]);
    }
}
