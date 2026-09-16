<?php

namespace App\Livewire\Admin\Client;

use App\Models\Estimation;
use App\Models\Invoice;
use App\Models\Payment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('admin::layouts.client')]
#[Title('Client Dashboard')]
class ClientDashboard extends Component
{
    public array $stats = [];
    public $recentEstimations = [];
    public $recentInvoices = [];

    public function mount()
    {
        $client = auth()->user()->client;

        if (!$client) {
            return redirect()->route('dashboard');
        }

        $this->loadStats($client);
        $this->loadRecentData($client);
    }

    private function loadStats($client)
    {
        $totalEstimations = Estimation::where('client_id', $client->id)->count();
        $totalInvoices = Invoice::where('client_id', $client->id)->where('status', '!=', 'draft')->count();
        $totalPaid = Payment::whereHas('invoice', function($q) use ($client) {
            $q->where('client_id', $client->id);
        })->sum('amount');
        
        $pendingInvoices = Invoice::where('client_id', $client->id)
            ->whereIn('status', ['unpaid', 'partially_paid'])
            ->count();

        $this->stats = [
            [
                'label' => 'Total Estimations',
                'value' => $totalEstimations,
                'icon' => 'document-text',
                'gradient' => 'gradient-blue',
                'bg' => 'bg-indigo-50 dark:bg-indigo-900/20',
            ],
            [
                'label' => 'Total Invoices',
                'value' => $totalInvoices,
                'icon' => 'receipt-percent',
                'gradient' => 'gradient-purple',
                'bg' => 'bg-purple-50 dark:bg-purple-900/20',
            ],
            [
                'label' => 'Pending Payments',
                'value' => $pendingInvoices,
                'icon' => 'clock',
                'gradient' => 'gradient-orange',
                'bg' => 'bg-amber-50 dark:bg-amber-900/20',
            ],
            [
                'label' => 'Total Spent',
                'value' => number_format($totalPaid, 2),
                'icon' => 'banknotes',
                'gradient' => 'gradient-green',
                'bg' => 'bg-emerald-50 dark:bg-emerald-900/20',
            ],
        ];
    }

    private function loadRecentData($client)
    {
        $this->recentEstimations = Estimation::where('client_id', $client->id)
            ->latest()
            ->take(5)
            ->get();

        $this->recentInvoices = Invoice::where('client_id', $client->id)
            ->where('status', '!=', 'draft')
            ->latest()
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.client.client-dashboard');
    }
}
