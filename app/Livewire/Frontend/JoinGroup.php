<?php

namespace App\Livewire\Frontend;

use App\Models\GroupTour;
use App\Models\Lead;
use App\Models\Itinerary;
use App\Models\Country;
use App\Enums\LeadStatus;
use Livewire\Component;

class JoinGroup extends Component
{
    public GroupTour $groupTour;

    // Modal state
    public bool $showModal = false;
    public bool $success = false;
    public string $referenceNumber = '';

    // Contact Details
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public $country_id = '';
    public int $seats = 1;
    public string $additional_information = '';

    public function mount(GroupTour $groupTour)
    {
        $this->groupTour = $groupTour;

        // Autofill if logged in
        if (auth()->check()) {
            $user = auth()->user();
            $this->name = $user->name ?? '';
            $this->email = $user->email ?? '';
            $this->phone = $user->phone ?? $user->client?->phone ?? '';
        }
    }

    public function openModal()
    {
        $this->success = false;
        $this->showModal = true;
    }

    public function submit()
    {
        $maxSeatsRule = $this->groupTour->available_seats !== null ? '|max:' . $this->groupTour->available_seats : '';
        
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'country_id' => 'required|exists:countries,id',
            'seats' => 'required|integer|min:1' . $maxSeatsRule,
            'additional_information' => 'nullable|string',
        ]);

        $isLoggedIn = auth()->check();
        $client = $isLoggedIn ? auth()->user()->client : null;
        $clientName = $isLoggedIn ? auth()->user()->name : $this->name;
        $clientEmail = $isLoggedIn ? auth()->user()->email : $this->email;

        // Fetch selected country
        $country = Country::find($this->country_id);

        // Calculate days duration
        $daysCount = 0;
        if ($this->groupTour->start_date && $this->groupTour->end_date) {
            $daysCount = $this->groupTour->start_date->diffInDays($this->groupTour->end_date) + 1;
        }

        $totalPrice = $this->groupTour->price * $this->seats;

        // 1. Create Itinerary (Proposal)
        $proposal = Itinerary::create([
            'client_id' => $client?->id,
            'title' => "Proposal: Group Tour - " . $this->groupTour->title,
            'duration_days' => $daysCount,
            'client_name' => $clientName,
            'client_email' => $clientEmail,
            'start_date' => $this->groupTour->start_date,
            'end_date' => $this->groupTour->end_date,
            'adults' => $this->seats,
            'children' => 0,
            'price_per_adult' => $this->groupTour->price,
            'total_price' => $totalPrice,
            'currency' => '$',
            'status' => 'draft',
            'cover_image' => $this->groupTour->featured_image
        ]);

        // 2. Create Lead (Guest only or we can do it for everyone if we want, but following RequestQuote's pattern of guest only)
        if (!$isLoggedIn) {
            $lead = Lead::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'source' => 'Website',
                'status' => LeadStatus::NEW,
                'travel_date' => $this->groupTour->start_date,
                'estimated_budget' => $totalPrice,
                'notes' => "Group Tour Booking Request: " . $this->groupTour->title . ".\n" .
                           "Travel Dates: " . ($this->groupTour->start_date ? $this->groupTour->start_date->format('Y-m-d') : 'Flexible') . " to " . ($this->groupTour->end_date ? $this->groupTour->end_date->format('Y-m-d') : 'Flexible') . ".\n" .
                           "Seats Requested: " . $this->seats . ".\n" .
                           "Country of Origin: " . ($country ? $country->name : 'Not specified') . ".\n" .
                           "Additional Info: " . $this->additional_information,
            ]);

            $lead->activities()->create([
                'type' => 'note',
                'content' => "Proposal automatically created with reference number: " . $proposal->reference_number,
            ]);
        }

        // Set success states
        $this->referenceNumber = $proposal->reference_number;
        $this->success = true;

        // Reset fields
        $this->reset(['name', 'email', 'phone', 'country_id', 'seats', 'additional_information']);
        if (auth()->check()) {
            $user = auth()->user();
            $this->name = $user->name ?? '';
            $this->email = $user->email ?? '';
            $this->phone = $user->phone ?? $user->client?->phone ?? '';
        }
    }

    public function render()
    {
        $countries = Country::orderBy('name')->get();

        return view('livewire.frontend.join-group', [
            'countries' => $countries
        ]);
    }
}
