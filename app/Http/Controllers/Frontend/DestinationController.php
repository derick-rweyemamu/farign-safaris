<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function index()
    {
        return view('frontend.destinations.index');
    }

    public function show($slug)
    {
        $destination = Destination::with([
            'city.country',
            'activities',
            'animals',
            'tours' => function ($query) {
                $query->with(['category', 'destinations'])
                      ->orderBy('name');
            },
        ])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('frontend.destinations.show', compact('destination'));
    }
}

