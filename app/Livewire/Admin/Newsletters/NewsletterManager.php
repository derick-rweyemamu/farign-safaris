<?php

namespace App\Livewire\Admin\Newsletters;

use App\Models\NewsletterSubscriber;
use App\Models\NewsletterCampaign;
use App\Jobs\SendNewsletterJob;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SubscribersImport;

#[Layout('admin::layouts.app')]
#[Title('Newsletters')]
class NewsletterManager extends Component
{
    use WithPagination, WithFileUploads;

    public $activeTab = 'subscribers';

    // Broadcast form
    public $subject = '';
    public $body = '';

    // Subscriber modal state
    public $showSubscriberModal = false;
    public $showResendModal = false;
    public $resendingCampaignId = null;
    public $isEditSubscriber = false;
    public $subscriberId = null;
    public $subscriberEmail = '';
    public $subscriberStatus = 'active';

    // Import state
    public $showImportModal = false;
    public $importFile;

    // Campaign History Filters
    public $campaignSearch = '';
    public $campaignStatusFilter = '';

    // Subscriber Filters
    public $subscriberSearch = '';
    public $subscriberStatusFilter = '';
    public $perPage = 10;

    // Campaign Preview Modal
    public $showCampaignModal = false;
    public $previewSubject = '';
    public $previewBody = '';
    public $previewStatus = '';
    public $previewSentAt = '';
    public $previewRecipients = 0;

    // Editing a Draft Campaign
    public $editingCampaignId = null;

    public function updatedCampaignSearch()
    {
        $this->resetPage('campaignsPage');
    }

    public function updatedCampaignStatusFilter()
    {
        $this->resetPage('campaignsPage');
    }

    public function updatedSubscriberSearch()
    {
        $this->resetPage('subscribersPage');
    }

    public function updatedSubscriberStatusFilter()
    {
        $this->resetPage('subscribersPage');
    }

    public function updatedPerPage()
    {
        $this->resetPage('subscribersPage');
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function saveDraft()
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        if ($this->editingCampaignId) {
            $campaign = NewsletterCampaign::find($this->editingCampaignId);
            if ($campaign && $campaign->status === 'draft') {
                $campaign->update([
                    'subject' => $this->subject,
                    'body' => $this->body,
                ]);
                $this->dispatch('notify', message: __('Draft campaign updated successfully.'), type: 'success');
            }
        } else {
            NewsletterCampaign::create([
                'subject' => $this->subject,
                'body' => $this->body,
                'status' => 'draft',
                'recipients_count' => 0,
                'sent_at' => null,
            ]);
            $this->dispatch('notify', message: __('Campaign saved as draft.'), type: 'success');
        }

        $this->subject = '';
        $this->body = '';
        $this->editingCampaignId = null;
        $this->switchTab('history');
    }

    public function sendBroadcast()
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $activeSubscribersCount = NewsletterSubscriber::where('status', 'active')->count();

        if ($activeSubscribersCount === 0) {
            $this->dispatch('notify', message: __('No active subscribers to send to.'), type: 'warning');
            return;
        }

        if ($this->editingCampaignId) {
            $campaign = NewsletterCampaign::find($this->editingCampaignId);
            if ($campaign) {
                $campaign->update([
                    'subject' => $this->subject,
                    'body' => $this->body,
                    'status' => 'sending',
                    'recipients_count' => $activeSubscribersCount,
                    'sent_at' => now(),
                ]);
            } else {
                $campaign = NewsletterCampaign::create([
                    'subject' => $this->subject,
                    'body' => $this->body,
                    'status' => 'sending',
                    'recipients_count' => $activeSubscribersCount,
                    'sent_at' => now(),
                ]);
            }
        } else {
            $campaign = NewsletterCampaign::create([
                'subject' => $this->subject,
                'body' => $this->body,
                'status' => 'sending',
                'recipients_count' => $activeSubscribersCount,
                'sent_at' => now(),
            ]);
        }

        // Dispatch job to send emails
        SendNewsletterJob::dispatch($campaign);

        $this->subject = '';
        $this->body = '';
        $this->editingCampaignId = null;

        $this->dispatch('notify', message: "Broadcast queued for {$activeSubscribersCount} subscribers.", type: 'success');
        $this->switchTab('history');
    }

    public function viewCampaign($id)
    {
        $campaign = NewsletterCampaign::find($id);
        if ($campaign) {
            $this->previewSubject = $campaign->subject;
            $this->previewBody = $campaign->body;
            $this->previewStatus = $campaign->status;
            $this->previewSentAt = $campaign->sent_at ? $campaign->sent_at->format('M d, Y H:i') : 'Not sent';
            $this->previewRecipients = $campaign->recipients_count ?? 0;
            $this->showCampaignModal = true;
        }
    }

    public function duplicateCampaign($id)
    {
        $campaign = NewsletterCampaign::find($id);
        if ($campaign) {
            $this->subject = $campaign->subject . ' (Copy)';
            $this->body = $campaign->body;
            $this->editingCampaignId = null;
            $this->switchTab('broadcast');
            $this->dispatch('notify', message: __('Campaign copied into New Broadcast form.'), type: 'info');
        }
    }

    public function confirmResend($id)
    {
        $this->resendingCampaignId = $id;
        $this->showResendModal = true;
    }

    public function resendCampaign()
    {
        abort_if(!auth()->user()->can('create newsletters'), 403, 'Unauthorized action.');

        $campaign = NewsletterCampaign::find($this->resendingCampaignId);
        if ($campaign) {
            $newCampaign = NewsletterCampaign::create([
                'subject' => $campaign->subject,
                'body' => $campaign->body,
                'status' => 'queued',
            ]);
            \App\Jobs\SendNewsletterJob::dispatch($newCampaign);
            $this->showResendModal = false;
            $this->resendingCampaignId = null;
            $this->dispatch('notify', message: __('Campaign resent successfully!'), type: 'success');
        }
    }

    public function editCampaign($id)
    {
        $campaign = NewsletterCampaign::find($id);
        if ($campaign && $campaign->status === 'draft') {
            $this->subject = $campaign->subject;
            $this->body = $campaign->body;
            $this->editingCampaignId = $campaign->id;
            $this->switchTab('broadcast');
            $this->dispatch('notify', message: __('Draft loaded into New Broadcast form.'), type: 'info');
        }
    }

    public function deleteCampaign($id)
    {
        abort_if(!auth()->user()->can('delete newsletters') && !auth()->user()->can('edit newsletters'), 403, 'Unauthorized action.');

        $campaign = NewsletterCampaign::find($id);
        if ($campaign) {
            $campaign->delete();
            $this->dispatch('notify', message: __('Campaign deleted successfully.'), type: 'success');
        }
    }

    public function createSubscriber()
    {
        abort_if(!auth()->user()->can('create newsletters') && !auth()->user()->can('edit newsletters'), 403, 'Unauthorized action.');

        $this->resetSubscriberFields();
        $this->showSubscriberModal = true;
    }

    public function editSubscriber($id)
    {
        abort_if(!auth()->user()->can('edit newsletters') && !auth()->user()->can('create newsletters'), 403, 'Unauthorized action.');

        $this->resetSubscriberFields();

        $subscriber = NewsletterSubscriber::findOrFail($id);
        $this->subscriberId = $subscriber->id;
        $this->subscriberEmail = $subscriber->email;
        $this->subscriberStatus = $subscriber->status;
        $this->isEditSubscriber = true;
        $this->showSubscriberModal = true;
    }

    public function saveSubscriber()
    {
        if ($this->isEditSubscriber) {
            abort_if(!auth()->user()->can('edit newsletters') && !auth()->user()->can('create newsletters'), 403, 'Unauthorized action.');
        } else {
            abort_if(!auth()->user()->can('create newsletters') && !auth()->user()->can('edit newsletters'), 403, 'Unauthorized action.');
        }

        $rules = [
            'subscriberEmail' => 'required|email|max:255|unique:newsletter_subscribers,email' . ($this->isEditSubscriber ? ',' . $this->subscriberId : ''),
            'subscriberStatus' => 'required|in:active,unsubscribed',
        ];

        $this->validate($rules, [
            'subscriberEmail.unique' => 'This email address is already subscribed.',
        ]);

        if ($this->isEditSubscriber) {
            $subscriber = NewsletterSubscriber::findOrFail($this->subscriberId);
            $subscriber->update([
                'email' => $this->subscriberEmail,
                'status' => $this->subscriberStatus,
            ]);
            $this->dispatch('notify', message: __('Subscriber details updated successfully.'), type: 'success');
        } else {
            NewsletterSubscriber::create([
                'email' => $this->subscriberEmail,
                'status' => $this->subscriberStatus,
                'unsubscribe_token' => bin2hex(random_bytes(32)),
            ]);
            $this->dispatch('notify', message: __('New subscriber added successfully.'), type: 'success');
        }

        $this->showSubscriberModal = false;
        $this->resetSubscriberFields();
    }

    public function resetSubscriberFields()
    {
        $this->subscriberId = null;
        $this->subscriberEmail = '';
        $this->subscriberStatus = 'active';
        $this->isEditSubscriber = false;
    }

    public function toggleSubscriberStatus($id)
    {
        abort_if(!auth()->user()->can('edit newsletters'), 403, 'Unauthorized action.');

        $subscriber = NewsletterSubscriber::find($id);
        if ($subscriber) {
            $subscriber->status = $subscriber->status === 'active' ? 'unsubscribed' : 'active';
            $subscriber->save();
            $this->dispatch('notify', message: __('Subscriber status updated successfully.'), type: 'success');
        }
    }

    public function deleteSubscriber($id)
    {
        abort_if(!auth()->user()->can('delete newsletters'), 403, 'Unauthorized action.');

        $subscriber = NewsletterSubscriber::find($id);
        if ($subscriber) {
            $subscriber->delete();
            $this->dispatch('notify', message: __('Subscriber deleted successfully.'), type: 'success');
        }
    }

    public function importSubscribers()
    {
        abort_if(!auth()->user()->can('create newsletters'), 403, 'Unauthorized action.');

        $this->validate([
            'importFile' => 'required|file|mimes:csv,xlsx,xls,txt|max:5120', // 5MB max
        ]);

        Excel::import(new SubscribersImport, $this->importFile->getRealPath());

        $this->showImportModal = false;
        $this->importFile = null;
        $this->dispatch('notify', message: __('Subscribers imported successfully!'), type: 'success');
    }

    public function render()
    {
        $campaignsQuery = NewsletterCampaign::query();

        if (!empty($this->campaignSearch)) {
            $campaignsQuery->where('subject', 'like', '%' . $this->campaignSearch . '%');
        }

        if (!empty($this->campaignStatusFilter)) {
            $campaignsQuery->where('status', $this->campaignStatusFilter);
        }

        $subscribersQuery = NewsletterSubscriber::query();

        if (!empty($this->subscriberSearch)) {
            $subscribersQuery->where('email', 'like', '%' . $this->subscriberSearch . '%');
        }

        if (!empty($this->subscriberStatusFilter)) {
            $subscribersQuery->where('status', $this->subscriberStatusFilter);
        }

        $stats = [
            'total' => NewsletterSubscriber::count(),
            'active' => NewsletterSubscriber::where('status', 'active')->count(),
            'unsubscribed' => NewsletterSubscriber::where('status', 'unsubscribed')->count(),
        ];

        return view('livewire.admin.newsletters.newsletter-manager', [
            'subscribers' => $subscribersQuery->latest()->paginate($this->perPage, ['*'], 'subscribersPage'),
            'campaigns' => $campaignsQuery->latest()->paginate(10, ['*'], 'campaignsPage'),
            'stats' => $stats,
        ]);
    }
}

