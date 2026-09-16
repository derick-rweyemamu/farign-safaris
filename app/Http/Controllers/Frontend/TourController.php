<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourCategory;

class TourController extends Controller
{
    /**
     * Display the full tours listing with filter sidebar data.
     */
    public function index()
    {
        $tours = Tour::with(['category', 'destinations', 'tags'])
            ->orderBy('name')
            ->get();

        $categories = TourCategory::withCount('tours')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $tags = \App\Models\TourTag::where('is_active', true)
            ->orderBy('name')
            ->get();

        $totalTours = $tours->count();

        return view('frontend.tours.index', compact('tours', 'categories', 'tags', 'totalTours'));
    }

    /**
     * Display a single tour and its day-by-day itinerary.
     */
    public function show($slug)
    {
        $tour = Tour::with([
            'category',
            'itineraries' => function ($query) {
                $query->orderBy('day_number', 'asc');
            },
            'itineraries.accommodationModel',
            'itineraries.mealPlan',
            'itineraries.animals',
            'itineraries.pivotActivities',
            'itineraries.transports',
            'destinations',
        ])->where('slug', $slug)->firstOrFail();

        return view('frontend.tours.show', compact('tour'));
    }
}
