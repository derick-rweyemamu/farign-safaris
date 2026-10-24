<?php

namespace App\Livewire\Frontend;

use App\Models\Destination;
use Livewire\Component;
use Livewire\WithPagination;

class DestinationTours extends Component
{
    use WithPagination;

    public int $destinationId;

    public function mount(int $destinationId)
    {
        $this->destinationId = $destinationId;
    }

    public function render()
    {
        $destination = Destination::find($this->destinationId);

        if (!$destination) {
            return view('livewire.frontend.destination-tours', [
                'destination' => null,
                'tours' => collect(),
            ]);
        }

        $tours = $destination->tours()
            ->with(['category', 'destinations'])
            ->orderBy('name')
            ->paginate(6);

        return view('livewire.frontend.destination-tours', [
            'destination' => $destination,
            'tours' => $tours,
        ]);
    }
}
