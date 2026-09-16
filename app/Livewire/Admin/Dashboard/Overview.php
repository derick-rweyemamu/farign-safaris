<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\ActivityLog;
use App\Models\Client;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('admin::layouts.app')]
#[Title('Dashboard')]
class Overview extends Component
{
    public array $stats = [];
    public array $chartData = [];
    public array $recentActivity = [];
    public array $recentClients = [];

    public function mount(): void
    {
        if (auth()->user()?->client) {
            $this->redirect(route('client.dashboard'), navigate: true);
            return;
        }

        $this->loadStats();
        $this->loadChartData();
        $this->loadRecentActivity();
        $this->loadRecentClients();
    }

    private function loadStats(): void
    {
        $clientCount = Client::where('status', 'active')->count();
        $newClientsThisMonth = Client::whereMonth('created_at', now()->month)->count();

        $requestedToursCount = \App\Models\Itinerary::count();
        $newToursThisMonth = \App\Models\Itinerary::whereMonth('created_at', now()->month)->count();

        $leadsThisMonthCount = \App\Models\Lead::whereMonth('created_at', now()->month)->count();
        $leadsTotalCount = \App\Models\Lead::count();

        $logCount = ActivityLog::count();

        $this->stats = [
            [
                'label' => __('Active Clients'),
                'value' => number_format($clientCount),
                'change' => '+' . $newClientsThisMonth . ' ' . __('This month'),
                'positive' => true,
                'icon' => 'users',
                'gradient' => 'gradient-blue',
                'bg' => 'bg-indigo-50 dark:bg-indigo-900/20',
            ],
            [
                'label' => __('Requested Tours'),
                'value' => number_format($requestedToursCount),
                'change' => '+' . $newToursThisMonth . ' ' . __('This month'),
                'positive' => true,
                'icon' => 'globe',
                'gradient' => 'gradient-green',
                'bg' => 'bg-emerald-50 dark:bg-emerald-900/20',
            ],
            [
                'label' => __('Monthly Leads'),
                'value' => number_format($leadsThisMonthCount),
                'change' => round($leadsTotalCount > 0 ? ($leadsThisMonthCount / $leadsTotalCount) * 100 : 0) . ' % ' . __('of total leads'),
                'positive' => true,
                'icon' => 'funnel',
                'gradient' => 'gradient-orange',
                'bg' => 'bg-amber-50 dark:bg-amber-900/20',
            ],
            [
                'label' => __('Activity Logs'),
                'value' => number_format($logCount),
                'change' => ActivityLog::today()->count() . ' ' . __('today'),
                'positive' => null,
                'icon' => 'clipboard-list',
                'gradient' => 'gradient-purple',
                'bg' => 'bg-purple-50 dark:bg-purple-900/20',
            ],
        ];
    }

    private function loadChartData(): void
    {
        $months = collect(range(6, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            return [
                'month' => $date->format('M'),
                'clients' => \App\Models\Client::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        });

        $this->chartData = $months->toArray();
    }

    private function loadRecentActivity(): void
    {
        $this->recentActivity = ActivityLog::with('user')
            ->latest()
            ->take(8)
            ->get()
            ->map(fn($log) => [
                'action' => $log->action,
                'description' => $log->description,
                'user' => $log->user?->name ?? 'System',
                'initials' => $log->user?->initials ?? 'SY',
                'time' => $log->created_at->diffForHumans(),
                'ip' => $log->ip_address,
            ])
            ->toArray();
    }

    private function loadRecentClients(): void
    {
        $this->recentClients = \App\Models\Client::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($client) => [
                'id' => $client->id,
                'name' => $client->user?->name ?? __('Unknown'),
                'email' => $client->user?->email ?? __('N/A'),
                'initials' => $client->user?->initials ?? '??',
                'status' => $client->status->value,
                'joined' => $client->created_at?->format('M d, Y'),
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.dashboard.overview');
    }
}
