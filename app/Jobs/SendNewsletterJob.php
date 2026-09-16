<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use App\Mail\NewsletterMail;
use Illuminate\Support\Facades\Mail;

class SendNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $campaign;

    /**
     * Create a new job instance.
     */
    public function __construct(NewsletterCampaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Mark as sending
        $this->campaign->update(['status' => 'sending']);

        // Chunk subscribers to handle large lists
        NewsletterSubscriber::where('status', 'active')->chunk(100, function ($subscribers) {
            foreach ($subscribers as $subscriber) {
                try {
                    Mail::to($subscriber->email)->send(new NewsletterMail($this->campaign, $subscriber));
                } catch (\Exception $e) {
                    \Log::error("Failed to send newsletter to {$subscriber->email}: " . $e->getMessage());
                }
            }
        });

        // Mark as sent
        $this->campaign->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}
