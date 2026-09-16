<?php

namespace App\Livewire\Admin\Itineraries;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Itinerary;
use App\Models\Currency;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Itineraries & Quotes')]
class ItineraryList extends Component
{
    use WithPagination, AuthorizesRequests, \App\Traits\InteractsWithMediaUploads;
    use \Livewire\WithFileUploads;

    public function mount()
    {
        $this->currency = app(\App\Settings\GeneralSetting::class)->currency_default ?? 'USD';
    }

    #[Url(history: true)]
    public $search = '';

    #[Url]
    public $statusFilter = '';

    #[Url]
    public int $perPage = 10;

    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    public bool $showPriceBreakdownModal = false;
    public ?Itinerary $breakdownItinerary = null;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }





    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $itinerary_id = null;
    public $slug = '';
    public $description = '';
    public $overview = '';
    public $min_age = 8;
    public $max_age = 90;
    public $accepts_children = true;
    public $children_free_under_age = 6;
    public $accepts_solo_travelers = true;
    public $duration_days = null;
    public $duration_nights = null;
    public $difficulty = '';
    public $seasonal_months = [];
    public $max_guests = 6;
    public $min_guests = 1;
    public $featured_image;
    public $gallery = [];
    public $existingGallery = [];

    // Category (single tag/filtering)
    public $selectedCategory = null;

    public $activeTab = 'general';
    public $included_features = [];
    public $excluded_features = [];
    public $new_included_feature = '';
    public $new_excluded_feature = '';
    public $existingImage;
    public $price_per_adult = null;
    public $price_per_child = null;
    public bool $shouldUpdateSlug = false;
    
    public $title = '';
    public $client_name = '';
    public $client_email = '';
    public $start_date = '';
    public $end_date = '';
    public $adults = 1;
    public $children = 0;
    public $currency = 'USD';
    public $estimation_id = '';
    public $base_tour_id = ''; // For cloning

    public function updatedBaseTourId($value)
    {
        if ($value) {
            $tour = \App\Models\Tour::with(['categories', 'itineraries'])->find($value);
            if ($tour) {
                if (empty($this->title)) $this->title = $tour->name;
                $this->description = $tour->description;
                $this->overview = $tour->overview;
                $this->min_age = $tour->min_age;
                $this->max_age = $tour->max_age;
                $this->accepts_children = $tour->accepts_children;
                $this->children_free_under_age = $tour->children_free_under_age;
                $this->accepts_solo_travelers = $tour->accepts_solo_travelers;
                $dayCount = $tour->itineraries->count();
                $this->duration_days = $dayCount > 0 ? $dayCount : $tour->duration_days;
                $this->duration_nights = max(0, $this->duration_days - 1);
                $this->difficulty = $tour->difficulty;
                $shortToFull = [
                    'jan' => 'January', 'feb' => 'February', 'mar' => 'March', 'apr' => 'April',
                    'may' => 'May', 'jun' => 'June', 'jul' => 'July', 'aug' => 'August',
                    'sep' => 'September', 'oct' => 'October', 'nov' => 'November', 'dec' => 'December'
                ];
                $this->seasonal_months = collect($tour->seasonal_months ?? [])
                    ->map(function($m) use ($shortToFull) {
                        $key = strtolower(substr(trim($m), 0, 3));
                        return $shortToFull[$key] ?? trim($m);
                    })->toArray();
                $this->max_guests = $tour->max_guests;
                $this->min_guests = $tour->min_guests ?? 1;
                $this->existingGallery = $tour->gallery ?? [];
                $this->existingImage = $tour->featured_image;
                $this->price_per_adult = $tour->adult_price;
                $this->price_per_child = $tour->child_price;
                $this->included_features = is_array($tour->included_features) ? $tour->included_features : [];
                $this->excluded_features = is_array($tour->excluded_features) ? $tour->excluded_features : [];
                
                $this->selectedCategory = $tour->categories->first()?->id;
            }
        }
    }

    // ── Auto-calculate child price at 50% of adult price ──────────────────

    public function updatedPricePerAdult($value)
    {
        $this->price_per_child = round(floatval($value) * 0.5, 2);
    }

    public function removeGalleryImage($index)
    {
        if (isset($this->existingGallery[$index])) {
            unset($this->existingGallery[$index]);
            $this->existingGallery = array_values($this->existingGallery);

            if ($this->itinerary_id) {
                \App\Models\Itinerary::find($this->itinerary_id)->update([
                    'gallery' => $this->existingGallery
                ]);
            }
        }
    }

    public function removeTempGalleryImage($index)
    {
        if (isset($this->gallery[$index])) {
            unset($this->gallery[$index]);
            $this->gallery = array_values($this->gallery);
        }
    }
    
    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function resetFields()
    {
        $this->isEdit = false;
        $this->itinerary_id = null;
        $this->title = '';
        $this->slug = '';
        $this->description = '';
        $this->overview = '';
        $this->min_age = 8;
        $this->max_age = 90;
        $this->accepts_children = true;
        $this->children_free_under_age = 6;
        $this->accepts_solo_travelers = true;
        $this->duration_days = null;
        $this->duration_nights = null;
        $this->difficulty = '';
        $this->seasonal_months = [];
        $this->max_guests = 6;
        $this->min_guests = 1;
        $this->featured_image = null;
        $this->gallery = [];
        $this->existingGallery = [];
        $this->included_features = [];
        $this->excluded_features = [];
        $this->new_included_feature = '';
        $this->new_excluded_feature = '';
        $this->existingImage = null;
        $this->price_per_adult = null;
        $this->price_per_child = null;
        $this->shouldUpdateSlug = false;
        
        $this->selectedCategory = null;

        $this->client_name = '';
        $this->client_email = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->adults = 1;
        $this->children = 0;
        $this->currency = app(\App\Settings\GeneralSetting::class)->currency_default ?? 'USD';
        $this->estimation_id = '';
        $this->base_tour_id = '';
        $this->activeTab = 'general';
    }

    public function create()
    {
        $this->authorize('create itineraries');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('edit itineraries');
        $this->resetFields();
        $itinerary = Itinerary::with(['categories'])->findOrFail($id);
        
        $this->itinerary_id = $itinerary->id;
        $this->title = $itinerary->title;
        $this->slug = $itinerary->slug;
        $this->description = $itinerary->description;
        $this->overview = $itinerary->overview;
        $this->min_age = $itinerary->min_age;
        $this->max_age = $itinerary->max_age;
        $this->accepts_children = $itinerary->accepts_children;
        $this->children_free_under_age = $itinerary->children_free_under_age;
        $this->accepts_solo_travelers = $itinerary->accepts_solo_travelers;
        $this->duration_days = $itinerary->duration_days;
        $this->duration_nights = $itinerary->duration_nights;
        $this->difficulty = $itinerary->difficulty;
        $shortToFull = [
            'jan' => 'January', 'feb' => 'February', 'mar' => 'March', 'apr' => 'April',
            'may' => 'May', 'jun' => 'June', 'jul' => 'July', 'aug' => 'August',
            'sep' => 'September', 'oct' => 'October', 'nov' => 'November', 'dec' => 'December'
        ];
        $this->seasonal_months = collect($itinerary->seasonal_months ?? [])
            ->map(function($m) use ($shortToFull) {
                $key = strtolower(substr(trim($m), 0, 3));
                return $shortToFull[$key] ?? trim($m);
            })->toArray();
        $this->max_guests = $itinerary->max_guests;
        $this->min_guests = $itinerary->min_guests ?? 1;
        $this->existingGallery = $itinerary->gallery ?? [];
        $this->existingImage = $itinerary->cover_image;
        $this->price_per_adult = $itinerary->price_per_adult;
        $this->price_per_child = $itinerary->price_per_child;
        $this->included_features = is_array($itinerary->included) ? $itinerary->included : [];
        $this->excluded_features = is_array($itinerary->excluded) ? $itinerary->excluded : [];
        
        $this->selectedCategory = $itinerary->categories->first()?->id;

        $this->client_name = $itinerary->client_name;
        $this->client_email = $itinerary->client_email;
        $this->start_date = $itinerary->start_date ? $itinerary->start_date->format('Y-m-d') : '';
        $this->end_date = $itinerary->end_date ? $itinerary->end_date->format('Y-m-d') : '';
        $this->adults = $itinerary->adults;
        $this->children = $itinerary->children;
        $this->currency = $itinerary->currency;
        
        $linkedEstimate = \App\Models\Estimation::where('itinerary_id', $itinerary->id)->first();
        $this->estimation_id = $linkedEstimate ? $linkedEstimate->id : '';
        
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->authorize($this->isEdit ? 'edit itineraries' : 'create itineraries');
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'adults' => 'required|integer|min:1',
            'children' => 'required|integer|min:0',
            'currency' => 'required|string|size:3',
            'estimation_id' => 'nullable|exists:estimations,id',
            'base_tour_id' => 'nullable|exists:tours,id',
            'description' => 'nullable|string',
            'min_age' => 'required|integer|min:0',
            'max_age' => 'nullable|integer|min:0',
            'accepts_children' => 'boolean',
            'children_free_under_age' => 'required|integer|min:0',
            'accepts_solo_travelers' => 'boolean',
            'duration_days' => 'nullable|integer|min:0',
            'duration_nights' => 'nullable|integer|min:0',
            'difficulty' => 'nullable|string|max:50',
            'seasonal_months' => 'nullable|array',
            'max_guests' => 'nullable|integer|min:0',
            'min_guests' => 'required|integer|min:1',
            'featured_image' => $this->imageRule('featured_image', 600),
            'gallery.*' => $this->galleryRule(600),
            'selectedCategory' => 'nullable|integer|exists:tour_categories,id',
        ]);

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'overview' => $this->overview,
            'min_age' => $this->min_age,
            'max_age' => $this->max_age,
            'accepts_children' => $this->accepts_children,
            'children_free_under_age' => $this->children_free_under_age,
            'accepts_solo_travelers' => $this->accepts_solo_travelers,
            'duration_days' => $this->duration_days ?: null,
            'duration_nights' => $this->duration_nights ?: null,
            'difficulty' => $this->difficulty ?: null,
            'seasonal_months' => !empty($this->seasonal_months) ? array_values(array_filter($this->seasonal_months)) : null,
            'max_guests' => $this->max_guests ?: null,
            'min_guests' => $this->min_guests,
            'client_name' => $this->client_name,
            'client_email' => $this->client_email,
            'start_date' => $this->start_date ?: null,
            'end_date' => $this->end_date ?: null,
            'adults' => $this->adults,
            'children' => $this->children,
            'currency' => $this->currency,
            'included' => $this->included_features,
            'excluded' => $this->excluded_features,
            'price_per_adult' => $this->price_per_adult ?? 0,
            'price_per_child' => $this->price_per_child ?? 0,
            'product_id' => null, // We no longer use product_id here
        ];

        if ($this->featured_image) {
            $data['cover_image'] = $this->saveImageField($this->featured_image, 'itineraries');
        }

        if (!empty($this->gallery)) {
            $galleryPaths = $this->saveGalleryField($this->gallery, 'itineraries/gallery');
            if ($this->isEdit) {
                $existing = is_array($this->existingGallery) ? $this->existingGallery : [];
                $data['gallery'] = array_merge($existing, $galleryPaths);
            } else {
                $data['gallery'] = $galleryPaths;
            }
        } else if ($this->isEdit) {
            $data['gallery'] = $this->existingGallery;
        }

        if ($this->isEdit) {
            $itinerary = Itinerary::findOrFail($this->itinerary_id);
            $itinerary->update($data);
            
            // Attach Estimate Logic after creation or update
            if ($this->estimation_id) {
                \App\Models\Estimation::where('itinerary_id', $itinerary->id)->update(['itinerary_id' => null]);
                $estimation = \App\Models\Estimation::find($this->estimation_id);
                if ($estimation) {
                    $estimation->update(['itinerary_id' => $itinerary->id]);
                }
            } else {
                \App\Models\Estimation::where('itinerary_id', $itinerary->id)->update(['itinerary_id' => null]);
            }
            
            $this->dispatch('notify', message: __('Proposal updated successfully!'), type: 'success');
        } else {
            $data['status'] = 'draft';
            $data['reference_number'] = date('Y') . '-' . rand(1000, 9999) . '.' . rand(1, 9);
            $itinerary = Itinerary::create($data);
            
            // AUTOFILL CLONING LOGIC
            if ($this->base_tour_id) {
                $tour = \App\Models\Tour::with('itineraries.pivotActivities', 'itineraries.transports', 'itineraries.animals')->findOrFail($this->base_tour_id);
                
                foreach ($tour->itineraries as $tourDay) {
                    $cleanTitle = preg_replace('/^Day\s+\d+\s*[-–:]\s*/iu', '', $tourDay->title ?? '');

                    $newDay = $itinerary->days()->create([
                        'day_number' => $tourDay->day_number,
                        'day' => 'Day ' . $tourDay->day_number,
                        'description' => $tourDay->description,
                        'location' => $cleanTitle,
                        'accommodation' => $tourDay->accommodation,
                        'accommodation_id' => $tourDay->accommodation_id,
                        'accommodation_amount' => $tourDay->accommodation_amount,
                        'meals' => $tourDay->meals,
                        'meal_plan_id' => $tourDay->meal_plan_id,
                        'meal_plan_amount' => $tourDay->meal_plan_amount,
                        'adult_price' => $tourDay->adult_price,
                        'child_price' => $tourDay->child_price,
                        'featured_image' => $tourDay->featured_image,
                    ]);

                    if ($tourDay->animals->isNotEmpty()) {
                        $newDay->animals()->sync($tourDay->animals->pluck('id'));
                    }

                    if ($tourDay->pivotActivities->isNotEmpty()) {
                        $syncData = [];
                        foreach ($tourDay->pivotActivities as $act) {
                            $syncData[$act->id] = ['amount' => $act->pivot->amount];
                        }
                        $newDay->activities()->sync($syncData);
                    }

                    if ($tourDay->transports->isNotEmpty()) {
                        $syncData = [];
                        foreach ($tourDay->transports as $trans) {
                            $syncData[$trans->id] = ['amount' => $trans->pivot->amount];
                        }
                        $newDay->transports()->sync($syncData);
                    }
                }
            }
            
            // Attach Estimate Logic after creation or update
            if ($this->estimation_id) {
                \App\Models\Estimation::where('itinerary_id', $itinerary->id)->update(['itinerary_id' => null]);
                $estimation = \App\Models\Estimation::find($this->estimation_id);
                if ($estimation) {
                    $estimation->update(['itinerary_id' => $itinerary->id]);
                }
            } else {
                \App\Models\Estimation::where('itinerary_id', $itinerary->id)->update(['itinerary_id' => null]);
            }
            
            $this->dispatch('notify', message: __('Proposal created successfully!'), type: 'success');
        }

        $itinerary->categories()->sync($this->selectedCategory ? [$this->selectedCategory] : []);
        $this->showModal = false;
        $this->resetFields();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete itineraries');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteItinerary(): void
    {
        $this->authorize('delete itineraries');
        Itinerary::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', message: __('Itinerary deleted successfully!'), type: 'success');
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function duplicate($id)
    {
        $this->authorize('create itineraries');
        $original = Itinerary::with('days.activities', 'days.transports')->findOrFail($id);
        
        $newItinerary = $original->replicate();
        $newItinerary->title = $original->title . ' (Copy)';
        $newItinerary->reference_number = date('Y') . '-' . rand(1000, 9999) . '.' . rand(1, 9);
        $newItinerary->status = 'draft';
        $newItinerary->save();

        foreach ($original->days as $day) {
            $newDay = $day->replicate();
            $newDay->itinerary_id = $newItinerary->id;
            $newDay->save();

            // Sync activities
            $activities = [];
            foreach ($day->activities as $activity) {
                $activities[$activity->id] = ['time_of_day' => $activity->pivot->time_of_day];
            }
            $newDay->activities()->sync($activities);

            // Sync transports
            $transports = [];
            foreach ($day->transports as $transport) {
$transports[$transport->id] = ['time_of_day' => $transport->pivot->time_of_day];
            }
            $newDay->transports()->sync($transports);
        }

        $this->dispatch('notify', message: __('Itinerary duplicated successfully!'), type: 'success');
    }

    public function generateCostSheet($id)
    {
        $this->authorize('create cost sheets');

        $itinerary = Itinerary::with([
            'days.accommodation',
            'days.mealPlan',
            'days.activities',
            'days.transports',
        ])->findOrFail($id);

        $numDays = $itinerary->days->count();
        $durationDays = $itinerary->duration_days ?? $numDays;

        $matrixDays = [];
        $matrixRowsMap = [];

        foreach ($itinerary->days as $index => $day) {
            $matrixDays[] = [
                'day' => $day->day_number,
                'destination_id' => $day->destination_id ?? null,
                'addons' => []
            ];

            // Accommodation
            if ($day->accommodation_amount) {
                $accName = is_object($day->accommodation) ? $day->accommodation->name : ($day->accommodation ?: 'Accommodation');
                $key = "accommodation_" . $day->accommodation_id;
                if (!isset($matrixRowsMap[$key])) {
                    $matrixRowsMap[$key] = [
                        'id' => uniqid(),
                        'type' => 'accommodation',
                        'item_id' => $day->accommodation_id,
                        'label' => "Accommodation - {$accName}",
                        'is_per_pax' => true,
                        'is_shared' => false,
                        'amounts' => array_fill(0, $durationDays, 0.0)
                    ];
                }
                $matrixRowsMap[$key]['amounts'][$index] = (float) $day->accommodation_amount;
            }

            // Meals
            if ($day->meal_plan_amount) {
                $mealName = $day->mealPlan ? $day->mealPlan->name : 'Meal Plan';
                $key = "snacks_" . $day->meal_plan_id;
                if (!isset($matrixRowsMap[$key])) {
                    $matrixRowsMap[$key] = [
                        'id' => uniqid(),
                        'type' => 'snacks',
                        'item_id' => $day->meal_plan_id,
                        'label' => "Meal Plan - {$mealName}",
                        'is_per_pax' => true,
                        'is_shared' => false,
                        'amounts' => array_fill(0, $durationDays, 0.0)
                    ];
                }
                $matrixRowsMap[$key]['amounts'][$index] = (float) $day->meal_plan_amount;
            }

            // Transport
            foreach ($day->transports as $trans) {
                $amount = $trans->pivot->amount ?? 0;
                if ($amount) {
                    $key = "car_" . $trans->id;
                    if (!isset($matrixRowsMap[$key])) {
                        $matrixRowsMap[$key] = [
                            'id' => uniqid(),
                            'type' => 'car',
                            'item_id' => $trans->id,
                            'label' => "Transport - {$trans->name}",
                            'is_per_pax' => false,
                            'is_shared' => true,
                            'amounts' => array_fill(0, $durationDays, 0.0)
                        ];
                    }
                    $matrixRowsMap[$key]['amounts'][$index] = (float) $amount;
                }
            }

            // Activities
            foreach ($day->activities as $act) {
                $amount = $act->pivot->amount ?? 0;
                if ($amount) {
                    $key = "activity_" . $act->id;
                    if (!isset($matrixRowsMap[$key])) {
                        $matrixRowsMap[$key] = [
                            'id' => uniqid(),
                            'type' => 'activity',
                            'item_id' => $act->id,
                            'label' => "Activity - {$act->name}",
                            'is_per_pax' => true,
                            'is_shared' => false,
                            'amounts' => array_fill(0, $durationDays, 0.0)
                        ];
                    }
                    $matrixRowsMap[$key]['amounts'][$index] = (float) $amount;
                }
            }
        }

        $matrixRows = array_values($matrixRowsMap);

        // Calculate subtotals (these will be re-calculated by the form anyway)
        $subtotalAccommodation = 0;
        $subtotalMeals = 0;
        $subtotalTransport = 0;
        $subtotalActivities = 0;

        foreach ($matrixRows as $row) {
            $rowSum = array_sum($row['amounts']);
            if ($row['is_per_pax']) {
                $rowSum *= max(1, (int) $itinerary->adults);
            }
            switch ($row['type']) {
                case 'accommodation': $subtotalAccommodation += $rowSum; break;
                case 'snacks': $subtotalMeals += $rowSum; break;
                case 'car': $subtotalTransport += $rowSum; break;
                case 'activity': $subtotalActivities += $rowSum; break;
            }
        }
        $subtotal = $subtotalAccommodation + $subtotalMeals + $subtotalTransport + $subtotalActivities;

        $costSheet = \App\Models\CostSheet::where('itinerary_id', $itinerary->id)->first();

        if ($costSheet) {
            $costSheet->update([
                'title'                 => $itinerary->title,
                'calculation_type'      => 'matrix',
                'num_adults'            => $itinerary->adults ?? 1,
                'num_children'          => $itinerary->children ?? 0,
                'duration_days'         => $durationDays,
                'start_date'            => $itinerary->start_date,
                'end_date'              => $itinerary->end_date,
                'subtotal_accommodation'=> $subtotalAccommodation,
                'subtotal_meals'        => $subtotalMeals,
                'subtotal_transport'    => $subtotalTransport,
                'subtotal_activities'   => $subtotalActivities,
                'subtotal'              => $subtotal,
                'grand_total'           => $subtotal,
                'matrix_days'           => $matrixDays,
                'matrix_rows'           => $matrixRows,
                'status'                => 'draft',
            ]);
            $costSheet->items()->delete(); // clear any old items
            $isNew = false;
        } else {
            $costSheet = \App\Models\CostSheet::create([
                'itinerary_id'          => $itinerary->id,
                'title'                 => $itinerary->title,
                'calculation_type'      => 'matrix',
                'num_adults'            => $itinerary->adults ?? 1,
                'num_children'          => $itinerary->children ?? 0,
                'num_vehicles'          => 1,
                'duration_days'         => $durationDays,
                'start_date'            => $itinerary->start_date,
                'end_date'              => $itinerary->end_date,
                'season'                => 'high',
                'subtotal_accommodation'=> $subtotalAccommodation,
                'subtotal_meals'        => $subtotalMeals,
                'subtotal_transport'    => $subtotalTransport,
                'subtotal_activities'   => $subtotalActivities,
                'subtotal'              => $subtotal,
                'grand_total'           => $subtotal,
                'matrix_days'           => $matrixDays,
                'matrix_rows'           => $matrixRows,
                'status'                => 'draft',
                'created_by'            => auth()->id(),
            ]);
            $isNew = true;
        }

        $message = $isNew
            ? 'Cost Sheet generated successfully!'
            : 'Cost Sheet updated with latest itinerary data!';

        $this->dispatch('notify', message: $message, type: 'success');
    }

    public function sendEmail($id)
    {
        $this->authorize('view itineraries');

        $itinerary = Itinerary::findOrFail($id);

        $clientEmail = $itinerary->client_email ?: ($itinerary->client->email ?? null);

        if (!$clientEmail) {
            $this->dispatch('notify', message: __('No client email found for this itinerary.'), type: 'error');
            return;
        }

        try {
            \Illuminate\Support\Facades\Mail::to($clientEmail)->send(new \App\Mail\ItineraryMail($itinerary));
            \App\Models\ActivityLog::record('itinerary.email_sent', "Sent Itinerary Proposal ({$itinerary->reference_number}) to {$clientEmail}");
            $this->dispatch('notify', message: "Itinerary sent to {$clientEmail} successfully!", type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: __('Failed to send email:') . $e->getMessage(), type: 'error');
        }
    }

    public function viewPriceBreakdown($id)
    {
        $this->authorize('view itineraries');
        $this->breakdownItinerary = Itinerary::with('days.activities', 'days.transports', 'days.accommodation', 'days.mealPlan')->findOrFail($id);
        $this->showPriceBreakdownModal = true;
    }

    public function closePriceBreakdown()
    {
        $this->showPriceBreakdownModal = false;
        $this->breakdownItinerary = null;
    }

    public function render()
    {
        $this->authorize('view itineraries');
        $itineraries = Itinerary::query()
            ->with('costSheet')
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('reference_number', 'like', '%' . $this->search . '%')
                    ->orWhere('client_name', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        $tours = \App\Models\Tour::orderBy('name')->get();
        // Fetch estimates that are either not linked to any itinerary, or linked to the currently edited one
        $estimations = \App\Models\Estimation::whereNull('itinerary_id')
            ->when($this->itinerary_id, function($query) {
                return $query->orWhere('itinerary_id', $this->itinerary_id);
            })
            ->orderBy('id', 'desc')->get();
            
        $categories = \App\Models\TourCategory::orderBy('name', 'asc')->get();
        $currencies = Currency::active()->orderBy('code')->get();

        return view('livewire.admin.itineraries.itinerary-list', [
            'itineraries' => $itineraries,
            'tours' => $tours,
            'estimations' => $estimations,
            'categories' => $categories,
            'currencies' => $currencies,
        ]);
    }

    public function addIncludedFeature()
    {
        if (!empty(trim($this->new_included_feature))) {
            $this->included_features[] = trim($this->new_included_feature);
            $this->new_included_feature = '';
        }
    }

    public function removeIncludedFeature($index)
    {
        unset($this->included_features[$index]);
        $this->included_features = array_values($this->included_features);
    }

    public function addExcludedFeature()
    {
        if (!empty(trim($this->new_excluded_feature))) {
            $this->excluded_features[] = trim($this->new_excluded_feature);
            $this->new_excluded_feature = '';
        }
    }

    public function removeExcludedFeature($index)
    {
        unset($this->excluded_features[$index]);
        $this->excluded_features = array_values($this->excluded_features);
    }
}
