<?php

namespace App\Livewire\Frontend;

use App\Models\Tour;
use App\Models\Lead;
use App\Models\Itinerary;
use App\Models\Destination;
use App\Enums\LeadStatus;
use Livewire\Component;
use Carbon\Carbon;

class RequestQuote extends Component
{
    public Tour $tour;

    // Modal state
    public bool $showModal = false;
    public bool $success = false;
    public string $referenceNumber = '';

    // Contact Details (Guest only)
    public string $name = '';
    public string $email = '';
    public string $phone = '';

    // Extra details
    public string $estimated_budget = '';
    public string $additional_information = '';

    // Travel Details
    public $start_date;
    public $end_date;
    public int $adults = 1;
    public int $children = 0;

    public array $available_extra_activities = [];
    public array $selected_extra_activities = [];
    public string $currency_symbol = '$';

    public function mount(Tour $tour)
    {
        $this->tour = $tour;
        $this->start_date = date('Y-m-d');
        $this->end_date = date('Y-m-d', strtotime("+" . $tour->duration_days . " days"));

        $settings = app(\App\Settings\GeneralSetting::class);
        $defaultCurrency = \App\Models\Currency::where('code', $settings->currency_default)->first();
        $this->currency_symbol = $defaultCurrency?->symbol ?? '$';

        $extras = collect();
        if ($tour->itineraries) {
            foreach ($tour->itineraries as $day) {
                if ($day->pivotActivities) {
                    foreach ($day->pivotActivities as $act) {
                        if (!empty($act->pivot->amount) && $act->pivot->amount > 0) {
                            $extras->put($act->id, [
                                'id' => $act->id,
                                'name' => $act->name,
                                'price' => $act->pivot->amount,
                            ]);
                        }
                    }
                }
            }
        }
        $this->available_extra_activities = $extras->values()->toArray();
    }

    public function openQuoteModal()
    {
        $this->success = false;
        $this->showModal = true;
    }

    public function updatedStartDate($value)
    {
        if ($value) {
            $this->end_date = date('Y-m-d', strtotime($value . " +" . $this->tour->duration_days . " days"));
        }
    }

    public function submit()
    {
        $isLoggedIn = auth()->check();

        if ($isLoggedIn) {
            $this->validate([
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
                'adults' => 'required|integer|min:1',
                'children' => 'required|integer|min:0',
                'estimated_budget' => 'nullable|string|max:255',
                'additional_information' => 'nullable|string',
                'selected_extra_activities' => 'nullable|array',
            ]);
        } else {
            $this->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:50',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
                'adults' => 'required|integer|min:1',
                'children' => 'required|integer|min:0',
                'estimated_budget' => 'nullable|string|max:255',
                'additional_information' => 'nullable|string',
                'selected_extra_activities' => 'nullable|array',
            ]);
        }

        $adultPrice = $this->calculatedAdultPrice;
        $childPrice = $this->calculatedChildPrice;

        $extrasTotal = 0;
        $selectedExtrasNames = [];
        foreach($this->available_extra_activities as $extra) {
            if(in_array($extra['id'], $this->selected_extra_activities)) {
                $childExtraPrice = $extra['price'] * 0.5;
                $extrasTotal += ($extra['price'] * $this->adults) + ($childExtraPrice * $this->children);
                $selectedExtrasNames[] = $extra['name'] . ' (' . $this->currency_symbol . $extra['price'] . ' pp, ' . $this->currency_symbol . $childExtraPrice . ' per child)';
            }
        }

        // 1. Create Itinerary (Proposal)
        $client = $isLoggedIn ? auth()->user()->client : null;
        $clientName = $isLoggedIn ? auth()->user()->name : $this->name;
        $clientEmail = $isLoggedIn ? auth()->user()->email : $this->email;

        $totalPrice = ($adultPrice * $this->adults) + ($childPrice * $this->children) + $extrasTotal;

        $proposal = Itinerary::create([
            'client_id' => $client?->id,
            'title' => "Proposal: " . $this->tour->name,
            'duration_days' => $this->tour->duration_days,
            'client_name' => $clientName,
            'client_email' => $clientEmail,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'adults' => $this->adults,
            'children' => $this->children,
            'price_per_adult' => $adultPrice,
            'price_per_child' => $childPrice,
            'total_price' => $totalPrice,
            'currency' => $this->currency_symbol,
            'included' => $this->tour->included_features ?? [],
            'excluded' => $this->tour->excluded_features ?? [],
            'status' => 'draft',
            'cover_image' => $this->tour->featured_image
        ]);

        // Copy daily itinerary days
        foreach ($this->tour->itineraries as $tourDay) {
            $destination = Destination::where('name', $tourDay->location)->first();
            $dayDate = Carbon::parse($this->start_date)->addDays($tourDay->day_number - 1);

            $cleanTitle = preg_replace('/^Day\s+\d+\s*[-–:]\s*/iu', '', $tourDay->title ?? '');

            $proposal->days()->create([
                'day' => "Day " . $tourDay->day_number,
                'date' => $dayDate->format('Y-m-d'),
                'destination_id' => $destination?->id,
                'location' => $cleanTitle,
                'description' => $tourDay->description,
                'accommodation_id' => $tourDay->accommodation_id,
                'meal_plan_id' => $tourDay->meal_plan_id,
            ]);
        }

        // 2. Create Lead (Guest only)
        if (!$isLoggedIn) {
            // Parse budget if provided (remove currency symbols, commas, etc)
            $numericBudget = null;
            if ($this->estimated_budget) {
                $numericBudget = preg_replace('/[^0-9.]/', '', $this->estimated_budget);
                $numericBudget = $numericBudget ?: null;
            }

            $extrasNotes = count($selectedExtrasNames) > 0 ? "\nRequested Extras: " . implode(', ', $selectedExtrasNames) : "";

            $lead = Lead::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'source' => 'Website',
                'status' => LeadStatus::NEW,
                'travel_date' => $this->start_date,
                'tour_id' => $this->tour->id,
                'estimated_budget' => $totalPrice,
                'notes' => "Quote requested for tour: " . $this->tour->name . ".\nTravel dates: " . $this->start_date . " to " . $this->end_date . ".\nTravelers: " . ($this->adults + $this->children) . " (Adults: " . $this->adults . ", Children: " . $this->children . ")" . 
                           ($this->estimated_budget ? "\nBudget Noted: " . $this->estimated_budget : "") . 
                           $extrasNotes . 
                           ($this->additional_information ? "\nAdditional Info: " . $this->additional_information : ""),
            ]);

            $lead->activities()->create([
                'type' => 'note',
                'content' => "Proposal automatically created with reference number: " . $proposal->reference_number,
            ]);
        }

        // Set success states
        $this->referenceNumber = $proposal->reference_number;
        $this->success = true;

        // Reset inputs
        $this->reset(['name', 'email', 'phone', 'estimated_budget', 'additional_information', 'selected_extra_activities']);
    }

    public function getCalculatedAdultPriceProperty()
    {
        $tour = $this->tour;
        
        return match (true) {
            $this->adults == 1 => $tour->adult_price,
            $this->adults == 2 => $tour->price_2_pax ?? $tour->adult_price,
            $this->adults == 3 => $tour->price_3_pax ?? $tour->adult_price,
            $this->adults == 4 => $tour->price_4_pax ?? $tour->adult_price,
            $this->adults == 5 => $tour->price_5_pax ?? $tour->adult_price,
            $this->adults == 6 => $tour->price_6_pax ?? $tour->adult_price,
            $this->adults >= 7 => $tour->price_7_plus_pax ?? $tour->adult_price,
            default => $tour->adult_price,
        };
    }

    public function getCalculatedChildPriceProperty()
    {
        return $this->tour->child_price ?? ($this->calculatedAdultPrice * 0.5);
    }

    public function render()
    {
        return view('livewire.frontend.request-quote');
    }
}
