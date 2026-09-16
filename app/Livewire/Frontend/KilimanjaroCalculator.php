<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use App\Models\Lead;
use App\Models\Tour;
use App\Enums\LeadStatus;

class KilimanjaroCalculator extends Component
{
    // Wizard Steps: 1 = Route/Days, 2 = Profile, 3 = Result
    public int $step = 1;

    // Selections
    public ?int $selectedTourId = null;
    public int $durationDays = 7;
    public string $climberAge = 'adult';
    public string $fitnessLevel = 'active';
    public bool $preAcclimatized = false;

    // Output Success Rate
    public int $successRate = 0;

    // Contact Details
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public bool $leadSubmitted = false;

    // Customizable content properties
    public ?string $customTitle = null;
    public ?string $customSubtitle = null;
    public ?string $customImage = null;
    public ?string $customBadge = null;
    public ?string $customHeading = null;
    public ?string $customDescription = null;

    public function mount(
        ?string $customTitle = null,
        ?string $customSubtitle = null,
        ?string $customImage = null,
        ?string $customBadge = null,
        ?string $customHeading = null,
        ?string $customDescription = null
    ) {
        $this->customTitle = $customTitle;
        $this->customSubtitle = $customSubtitle;
        $this->customImage = $customImage;
        $this->customBadge = $customBadge;
        $this->customHeading = $customHeading;
        $this->customDescription = $customDescription;
        $this->loadTours();
    }

    public function loadTours()
    {
        if ($this->selectedTourId) {
            return;
        }

        // Fetch the first hiking tour to set defaults
        $firstTour = Tour::whereHas('tourType', function ($query) {
            $query->where('name', 'like', '%Hiking%')->orWhere('slug', 'like', '%hiking%');
        })->first();

        if ($firstTour) {
            $this->selectedTourId = $firstTour->id;
            $this->durationDays = $firstTour->duration_days;
        }
    }

    public function updatedSelectedTourId($value)
    {
        $tour = Tour::find($value);
        if ($tour) {
            $this->durationDays = $tour->duration_days;
        }
    }

    public function calculate()
    {
        $tour = Tour::find($this->selectedTourId);
        if (!$tour) {
            return;
        }

        $tourName = $tour->name;
        $rate = 75; // default fallback base rate

        // Determine base success rates by matching route and climb duration
        if (stripos($tourName, 'Northern Circuit') !== false) {
            $rate = ($this->durationDays >= 9) ? 95 : 90;
        } elseif (stripos($tourName, 'Lemosho') !== false) {
            $rate = ($this->durationDays >= 8) ? 90 : 85;
        } elseif (stripos($tourName, 'Machame') !== false) {
            $rate = ($this->durationDays >= 7) ? 85 : 75;
        } elseif (stripos($tourName, 'Marangu') !== false) {
            $rate = ($this->durationDays >= 6) ? 65 : 60;
        } elseif (stripos($tourName, 'Rongai') !== false) {
            $rate = ($this->durationDays >= 7) ? 80 : 70;
        } elseif (stripos($tourName, 'Umbwe') !== false || stripos($tourName, 'Ubwe') !== false) {
            $rate = ($this->durationDays >= 6) ? 35 : 30;
        }

        // Adjust for Age Bracket
        if ($this->climberAge === 'youth') {
            $rate -= 3;
        } elseif ($this->climberAge === 'senior') {
            $rate -= 5;
        }

        // Adjust for Fitness Level
        if ($this->fitnessLevel === 'beginner') {
            $rate -= 5;
        } elseif ($this->fitnessLevel === 'moderate') {
            $rate -= 2;
        } elseif ($this->fitnessLevel === 'athletic') {
            $rate += 3;
        }

        // Adjust for Pre-acclimatization (e.g. climbed Mt. Meru first)
        if ($this->preAcclimatized) {
            $rate += 5;
        }

        // Constraints: absolute min 25%, absolute max 98%
        $this->successRate = max(25, min(98, $rate));

        // Advance to step 3
        $this->step = 3;
    }

    public function getAdviceProperty(): string
    {
        if ($this->successRate >= 85) {
            return __("Excellent probability of reaching Uhuru Peak! Your selected route and acclimatization options give you the best possible margins for success. Focus on pacing yourself ('pole pole') and maintaining hydration.");
        } elseif ($this->successRate >= 65) {
            return __("Good probability of success, but consider optimizing. Switching to a longer itinerary or planning a pre-climb (like Mt. Meru) will significantly reduce the risk of altitude sickness and increase your comfort.");
        } else {
            return __("Challenging acclimatization profile. Short itineraries (like 5-day Marangu) or highly direct routes (like Umbwe) have lower success rates. We strongly suggest discussing itinerary options with our experts to find a safer pace.");
        }
    }

    public function submitLead()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        $tour = Tour::find($this->selectedTourId);
        $tourLabel = $tour ? $tour->name : 'Unknown Climb';
        
        $notes = "--- KILIMANJARO SUCCESS CALCULATOR RESULT ---\n"
               . "Calculated Success Probability: {$this->successRate}%\n"
               . "Selected Tour: {$tourLabel} ({$this->durationDays} Days)\n"
               . "Age Bracket: " . ucfirst($this->climberAge) . "\n"
               . "Fitness Level: " . ucfirst($this->fitnessLevel) . "\n"
               . "Pre-acclimatized: " . ($this->preAcclimatized ? 'Yes (e.g. Mt. Meru)' : 'No') . "\n";

        Lead::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'source' => 'Kilimanjaro Success Calculator',
            'status' => LeadStatus::NEW,
            'notes' => $notes,
        ]);

        $this->leadSubmitted = true;
        $this->dispatch('notify', message: __('Calculations saved! Our team will contact you shortly.'), type: 'success');
    }

    public function restart()
    {
        $this->step = 1;
        $this->leadSubmitted = false;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
    }

    public function render()
    {
        // Query hiking tours
        $tours = Tour::whereHas('tourType', function ($query) {
            $query->where('name', 'like', '%Hiking%')->orWhere('slug', 'like', '%hiking%');
        })->get();

        return view('livewire.frontend.kilimanjaro-calculator', [
            'tours' => $tours
        ]);
    }
}
