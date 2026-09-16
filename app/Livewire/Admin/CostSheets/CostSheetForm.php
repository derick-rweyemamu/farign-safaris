<?php

namespace App\Livewire\Admin\CostSheets;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\CostSheet;
use App\Models\Destination;
use App\Models\Activity;
use App\Models\Transport;
use App\Models\MealPlan;
use App\Models\Accommodation;
use App\Models\AccommodationRate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;

#[Layout('admin::layouts.app')]
#[Title('Cost Sheet Form')]
class CostSheetForm extends Component
{
    use AuthorizesRequests;

    public bool   $isEdit           = false;
    public ?int   $editingId        = null;
    public int    $step             = 1;
    public int    $currentDayStep   = 0;

    // Form fields
    public string $calculation_type = 'matrix';
    public string $title         = '';
    public string $season        = 'high';
    public string $start_date    = '';
    public string $end_date      = '';
    public          $duration_days = 1;  // untyped: accepts empty string from cleared input
    public ?int   $num_adults    = 2;
    public ?int   $num_children  = 0;
    public ?int   $num_vehicles  = 1;
    public string $notes         = '';
    public string $status        = 'draft';

    // Fee snapshots
    public float $snap_concession_fee_adult_high = 0.00;
    public float $snap_concession_fee_adult_low  = 0.00;
    public float $snap_concession_fee_child_high = 0.00;
    public float $snap_concession_fee_child_low  = 0.00;

    public float $snap_crater_fee_adult_high = 0.00;
    public float $snap_crater_fee_adult_low  = 0.00;
    public float $snap_crater_fee_child_high = 0.00;
    public float $snap_crater_fee_child_low  = 0.00;

    public float $snap_park_fee_adult_high = 0.00;
    public float $snap_park_fee_adult_low  = 0.00;
    public float $snap_park_fee_child_high = 0.00;
    public float $snap_park_fee_child_low  = 0.00;

    public float $snap_pass_fee_adult_high = 0.00;
    public float $snap_pass_fee_adult_low  = 0.00;
    public float $snap_pass_fee_child_high = 0.00;
    public float $snap_pass_fee_child_low  = 0.00;

    // Markup
    public ?float $markup_percentage = 0.00;

    // Matrix
    public array  $matrixDays       = [];
    public array  $matrixRows       = [];
    public int    $matrixNumPax     = 1;
    public string $newRowType       = 'car';
    public string $newRowLabel      = '';
    public bool   $newRowIsPerPax   = false;
    public bool   $newRowIsShared   = false;  // ÷ Pax: divide fixed cost by total pax
    public ?int   $newRowItemId     = null;
    public float  $newRowBaseAmount = 0.0;

    // Computed totals
    public float $subtotal_park_fees       = 0.00;
    public float $subtotal_concession_fees = 0.00;
    public float $subtotal_crater_fees     = 0.00;
    public float $subtotal_pass_fees       = 0.00;
    public float $subtotal_activities      = 0.00;
    public float $subtotal_transport       = 0.00;
    public float $subtotal_meals           = 0.00;
    public float $subtotal_accommodation   = 0.00;
    public float $subtotal_custom          = 0.00;
    public float $subtotal                 = 0.00;
    public float $markup_amount            = 0.00;
    public float $grand_total              = 0.00;

    public function mount($id = null): void
    {
        if ($id) {
            $this->authorize('edit cost sheets');
            $this->editingId = $id;
            $this->isEdit = true;
            
            $cs = CostSheet::findOrFail($id);
            $this->title           = $cs->title;
            $this->calculation_type = 'matrix';
            $this->season          = $cs->season;
            $this->start_date      = $cs->start_date?->format('Y-m-d') ?? '';
            $this->end_date        = $cs->end_date?->format('Y-m-d') ?? '';
            $this->duration_days   = $cs->duration_days;

            // Recalculate duration from dates in case the saved value is stale
            $this->recalculateDuration();
            $this->num_adults      = $cs->num_adults;
            $this->num_children    = $cs->num_children;
            $this->num_vehicles    = $cs->num_vehicles;
            $this->notes           = $cs->notes ?? '';
            $this->status          = $cs->status;
            $this->markup_percentage = (float) $cs->markup_percentage;

            // Fee snapshots
            $this->snap_concession_fee_adult_high = (float) $cs->snap_concession_fee_adult_high;
            $this->snap_concession_fee_adult_low  = (float) $cs->snap_concession_fee_adult_low;
            $this->snap_concession_fee_child_high = (float) $cs->snap_concession_fee_child_high;
            $this->snap_concession_fee_child_low  = (float) $cs->snap_concession_fee_child_low;

            $this->snap_crater_fee_adult_high = (float) $cs->snap_crater_fee_adult_high;
            $this->snap_crater_fee_adult_low  = (float) $cs->snap_crater_fee_adult_low;
            $this->snap_crater_fee_child_high = (float) $cs->snap_crater_fee_child_high;
            $this->snap_crater_fee_child_low  = (float) $cs->snap_crater_fee_child_low;

            $this->snap_park_fee_adult_high = (float) $cs->snap_park_fee_adult_high;
            $this->snap_park_fee_adult_low  = (float) $cs->snap_park_fee_adult_low;
            $this->snap_park_fee_child_high = (float) $cs->snap_park_fee_child_high;
            $this->snap_park_fee_child_low  = (float) $cs->snap_park_fee_child_low;

            $this->snap_pass_fee_adult_high = (float) $cs->snap_pass_fee_adult_high;
            $this->snap_pass_fee_adult_low  = (float) $cs->snap_pass_fee_adult_low;
            $this->snap_pass_fee_child_high = (float) $cs->snap_pass_fee_child_high;
            $this->snap_pass_fee_child_low  = (float) $cs->snap_pass_fee_child_low;

            // Matrix
            $this->matrixDays = array_map(fn($day) => array_merge(
                ['destination_id' => null, 'addons' => []], $day
            ), $cs->matrix_days ?? []);
            $this->matrixRows               = $cs->matrix_rows ?? [];
            $this->matrixNumPax             = $cs->num_adults;
            
            $this->recalculate();
        } else {
            $this->authorize('create cost sheets');
            $this->step = 1;
        }
    }

    public function updatedSeason(): void          { $this->recalculate(); }
    public function updatedStartDate(): void        { $this->recalculateDuration(); $this->recalculate(); }
    public function updatedEndDate(): void          { $this->recalculateDuration(); $this->recalculate(); }
    public function updatedDurationDays($value): void
    {
        // Guard against empty input — reset to minimum of 1
        $this->duration_days = max(1, (int) $value);
        $this->syncMatrixDaysWithDuration();
        $this->recalculate();
    }
    public function updatedNumAdults($value): void
    {
        // Guard against empty input — reset to minimum of 1
        $this->num_adults   = max(1, (int) $value);
        $this->matrixNumPax = $this->num_adults;
        $this->recalculate();
    }

    public function updatedNumChildren($value): void
    {
        // Guard against empty input — reset to minimum of 0
        $this->num_children = max(0, (int) $value);
        $this->recalculate();
    }

    public function updatedNumVehicles($value): void
    {
        // Guard against empty input — reset to minimum of 1
        $this->num_vehicles = max(1, (int) $value);
        $this->recalculate();
    }
    public function updatedMarkupPercentage(): void { $this->recalculate(); }
    public function updatedMatrixRows(): void       { $this->recalculate(); }

    public function updatedNewRowType($value): void
    {
        $this->newRowItemId = null;
        $this->newRowBaseAmount = 0.0;
        $this->newRowLabel = '';
        $this->newRowIsShared = false;

        // Merged types (park_fee, pass_fee, crater, concession) are always per-pax
        $perPaxTypes = [
            'park_fee', 'pass_fee', 'crater', 'concession',
            'snacks', 'accommodation',
        ];

        $this->newRowIsPerPax = in_array($value, $perPaxTypes);
    }

    // Mutual exclusion: × Pax and ÷ Pax cannot both be active
    public function updatedNewRowIsPerPax(bool $value): void
    {
        if ($value) $this->newRowIsShared = false;
    }

    public function updatedNewRowIsShared(bool $value): void
    {
        if ($value) $this->newRowIsPerPax = false;
    }

    public function updatedNewRowItemId($value): void
    {
        if (!$value) {
            $this->newRowBaseAmount = 0.0;
            $this->newRowLabel = '';
            return;
        }

        if ($this->newRowType === 'car') {
            $transport = \App\Models\Transport::find($value);
            if ($transport) {
                $this->newRowBaseAmount = (float) $transport->price;
                $this->newRowLabel = $transport->name;
                $this->newRowIsPerPax = false; // vehicle cost is fixed, not per person
            }
        } elseif ($this->newRowType === 'snacks') {
            $meal = \App\Models\MealPlan::find($value);
            if ($meal) {
                $this->newRowBaseAmount = (float) $meal->price;
                $this->newRowLabel = $meal->name . ' (' . $meal->type . ')';
                $this->newRowIsPerPax = true;
            }
        } elseif ($this->newRowType === 'accommodation') {
            $rate = \App\Models\AccommodationRate::with('accommodation')->find($value);
            if ($rate) {
                $this->newRowBaseAmount = (float) ($rate->sto ?? $rate->rack ?? 0);
                $this->newRowLabel = ($rate->accommodation->name ?? 'Acc') . ' - ' . $rate->room_type;
                $this->newRowIsPerPax = true;
            }
        } elseif ($this->newRowType === 'activity') {
            $activity = \App\Models\Activity::find($value);
            if ($activity) {
                $this->newRowBaseAmount = 0.0;
                $this->newRowLabel = $activity->name;
                $this->newRowIsPerPax = true;
            }
        }
    }
    
    public function updatedMatrixDays(mixed $value, ?string $key = null): void
    {
        if ($key === null) {
            return;
        }

        if (str_ends_with($key, '.destination_id') && $value) {
            $dayIndex = (int) explode('.', $key)[0];
            $dest = Destination::find($value);
            if ($dest) {
                $s = $this->season === 'high' ? 'high' : 'low';
                $feeMap = [
                    'park_fee_adult'   => (float) $dest->{"park_fee_adult_{$s}_season"},
                    'park_fee_child'   => (float) $dest->{"park_fee_child_{$s}_season"},
                    'concession_adult' => (float) $dest->{"concession_fee_adult_{$s}_season"},
                    'concession_child' => (float) $dest->{"concession_fee_child_{$s}_season"},
                    'crater_adult'     => (float) $dest->{"crater_fee_adult_{$s}_season"},
                    'crater_child'     => (float) $dest->{"crater_fee_child_{$s}_season"},
                    'pass_fee_adult'   => (float) $dest->{"pass_fee_adult_{$s}_season"},
                    'pass_fee_child'   => (float) $dest->{"pass_fee_child_{$s}_season"},
                    // legacy compatibility
                    'park_fee'         => (float) $dest->{"park_fee_adult_{$s}_season"},
                    'concession'       => (float) $dest->{"concession_fee_adult_{$s}_season"},
                    'crater'           => (float) $dest->{"crater_fee_adult_{$s}_season"},
                    'pass_fee'         => (float) $dest->{"pass_fee_adult_{$s}_season"},
                ];
                foreach ($this->matrixRows as $ri => $row) {
                    $type = $row['type'] ?? 'custom';
                    if (isset($feeMap[$type]) && $feeMap[$type] > 0) {
                        $this->matrixRows[$ri]['amounts'][$dayIndex] = $feeMap[$type];
                    }
                }
            }
        }
        $this->recalculate();
    }

    public function updatedMatrixNumPax($value): void { $this->num_adults = max(1, (int) $value); $this->recalculate(); }
    protected function syncMatrixDaysWithDuration(): void
    {
        if (empty($this->matrixDays)) {
            return;
        }

        $currentDaysCount = count($this->matrixDays);
        $targetDaysCount = max(1, (int) $this->duration_days);

        if ($currentDaysCount < $targetDaysCount) {
            for ($d = $currentDaysCount + 1; $d <= $targetDaysCount; $d++) {
                $this->matrixDays[] = [
                    'label'          => 'Day ' . $d,
                    'destination_id' => $this->destination_id ?: null,
                    'addons'         => [],
                ];
                foreach ($this->matrixRows as $ri => $row) {
                    $this->matrixRows[$ri]['amounts'][] = 0;
                }
            }
        } elseif ($currentDaysCount > $targetDaysCount) {
            $this->matrixDays = array_slice($this->matrixDays, 0, $targetDaysCount);
            foreach ($this->matrixRows as $ri => $row) {
                $this->matrixRows[$ri]['amounts'] = array_slice($row['amounts'], 0, $targetDaysCount);
            }
        }
    }

    protected function recalculateFromMatrix(): void
    {
        $this->subtotal_park_fees       = 0.00;
        $this->subtotal_concession_fees = 0.00;
        $this->subtotal_crater_fees     = 0.00;
        $this->subtotal_pass_fees       = 0.00;
        $this->subtotal_activities      = 0.00;
        $this->subtotal_transport       = 0.00;
        $this->subtotal_meals           = 0.00;
        $this->subtotal_accommodation   = 0.00;
        $this->subtotal_custom          = 0.00;

        $numDays = count($this->matrixDays);
        $adults = max(1, (int) $this->num_adults);
        $children = max(0, (int) $this->num_children);

        foreach ($this->matrixRows as $row) {
            $type = $row['type'] ?? 'custom';
            if ($row['is_per_pax'] ?? false) {
                if (str_ends_with($type, '_child')) {
                    $mult = $children;
                } else {
                    $mult = $adults;
                }
            } else {
                $mult = 1;
            }
            $rowTotal = 0.0;
            for ($di = 0; $di < $numDays; $di++) {
                $rowTotal += (float) ($row['amounts'][$di] ?? 0) * $mult;
            }

            match ($type) {
                'park_fee', 'park_fee_adult', 'park_fee_child'             => $this->subtotal_park_fees       += $rowTotal,
                'concession', 'concession_adult', 'concession_child'       => $this->subtotal_concession_fees += $rowTotal,
                'crater', 'crater_adult', 'crater_child'                   => $this->subtotal_crater_fees     += $rowTotal,
                'pass_fee', 'pass_fee_adult', 'pass_fee_child'             => $this->subtotal_pass_fees       += $rowTotal,
                'activity'                                                 => $this->subtotal_activities      += $rowTotal,
                'car'                                                      => $this->subtotal_transport       += $rowTotal,
                'snacks'                                                   => $this->subtotal_meals           += $rowTotal,
                'accommodation'                                            => $this->subtotal_accommodation   += $rowTotal,
                default                                                    => $this->subtotal_custom          += $rowTotal,
            };
        }

        foreach ($this->matrixDays as $day) {
            foreach ($day['addons'] ?? [] as $addon) {
                $lineTotal = (float) ($addon['unit_cost'] ?? 0) * max(1, (int) ($addon['quantity'] ?? 1));
                match ($addon['item_type'] ?? 'custom') {
                    'activity'      => $this->subtotal_activities    += $lineTotal,
                    'transport'     => $this->subtotal_transport     += $lineTotal,
                    'meal_plan'     => $this->subtotal_meals         += $lineTotal,
                    'accommodation' => $this->subtotal_accommodation += $lineTotal,
                    default         => $this->subtotal_custom        += $lineTotal,
                };
            }
        }

        $this->subtotal =
            $this->subtotal_park_fees +
            $this->subtotal_concession_fees +
            $this->subtotal_crater_fees +
            $this->subtotal_pass_fees +
            $this->subtotal_activities +
            $this->subtotal_transport +
            $this->subtotal_meals +
            $this->subtotal_accommodation +
            $this->subtotal_custom;

        $markupPct = max(0, (float) $this->markup_percentage);
        $this->markup_amount = round($this->subtotal * ($markupPct / 100), 2);
        $this->grand_total   = round($this->subtotal + $this->markup_amount, 2);
    }

    protected function recalculateDuration(): void
    {
        if ($this->start_date && $this->end_date) {
            try {
                $start = Carbon::parse($this->start_date)->startOfDay();
                $end   = Carbon::parse($this->end_date)->startOfDay();

                if ($end->lt($start)) {
                    // End date is before start date — skip calculation
                    return;
                }

                // Inclusive day count: Jun 19 → Jun 22 = 4 days
                $this->duration_days = max(1, (int) $start->diffInDays($end) + 1);
                $this->syncMatrixDaysWithDuration();
            } catch (\Exception $e) {
                $this->duration_days = 1;
            }
        }
    }

    public function recalculate(): void
    {
        $this->recalculateFromMatrix();
    }

    public function save()
    {
        $this->authorize($this->isEdit ? 'edit cost sheets' : 'create cost sheets');
        
        // Sync duration days with matrix if dates were not used
        if ($this->calculation_type === 'matrix') {
            $this->duration_days = max((int)$this->duration_days, count($this->matrixDays));
        }
        
        $this->recalculate();

        $this->validate([
            'title'             => 'required|string|max:255',
            'season'            => 'required|in:high,low',
            'start_date'        => 'nullable|date',
            'end_date'          => 'nullable|date|after_or_equal:start_date',
            'num_adults'        => 'required|integer|min:1',
            'num_children'      => 'required|integer|min:0',
            'num_vehicles'      => 'required|integer|min:1',
            'markup_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $data = [
            'title'          => $this->title,
            'calculation_type' => 'matrix',
            'destination_id' => null,
            'season'         => $this->season,
            'start_date'     => $this->start_date ?: null,
            'end_date'       => $this->end_date ?: null,
            'duration_days'  => $this->duration_days,
            'num_adults'     => $this->num_adults,
            'num_children'   => $this->num_children,
            'num_vehicles'   => $this->num_vehicles,
            'notes'          => $this->notes ?: null,
            'matrix_days'    => $this->matrixDays,
            'matrix_rows'    => $this->matrixRows,
            'status'         => $this->status,

            'snap_concession_fee_adult_high' => $this->snap_concession_fee_adult_high,
            'snap_concession_fee_adult_low'  => $this->snap_concession_fee_adult_low,
            'snap_concession_fee_child_high' => $this->snap_concession_fee_child_high,
            'snap_concession_fee_child_low'  => $this->snap_concession_fee_child_low,
            'snap_crater_fee_adult_high'     => $this->snap_crater_fee_adult_high,
            'snap_crater_fee_adult_low'      => $this->snap_crater_fee_adult_low,
            'snap_crater_fee_child_high'     => $this->snap_crater_fee_child_high,
            'snap_crater_fee_child_low'      => $this->snap_crater_fee_child_low,
            'snap_park_fee_adult_high'       => $this->snap_park_fee_adult_high,
            'snap_park_fee_adult_low'        => $this->snap_park_fee_adult_low,
            'snap_park_fee_child_high'       => $this->snap_park_fee_child_high,
            'snap_park_fee_child_low'        => $this->snap_park_fee_child_low,
            'snap_pass_fee_adult_high'       => $this->snap_pass_fee_adult_high,
            'snap_pass_fee_adult_low'        => $this->snap_pass_fee_adult_low,
            'snap_pass_fee_child_high'       => $this->snap_pass_fee_child_high,
            'snap_pass_fee_child_low'        => $this->snap_pass_fee_child_low,

            'subtotal_park_fees'       => $this->subtotal_park_fees,
            'subtotal_concession_fees' => $this->subtotal_concession_fees,
            'subtotal_crater_fees'     => $this->subtotal_crater_fees,
            'subtotal_pass_fees'       => $this->subtotal_pass_fees,
            'subtotal_activities'      => $this->subtotal_activities,
            'subtotal_transport'       => $this->subtotal_transport,
            'subtotal_meals'           => $this->subtotal_meals,
            'subtotal_accommodation'   => $this->subtotal_accommodation,
            'subtotal_custom'          => $this->subtotal_custom,
            'subtotal'                 => $this->subtotal,

            'markup_percentage' => $this->markup_percentage,
            'markup_amount'     => $this->markup_amount,
            'grand_total'       => $this->grand_total,
        ];

        if (!$this->isEdit) {
            $data['created_by'] = auth()->id();
            $cs = CostSheet::create($data);
            session()->flash('notify', ['message' => __('Cost sheet created successfully!'), 'type' => 'success']);
        } else {
            $cs = CostSheet::findOrFail($this->editingId);
            $cs->update($data);
            session()->flash('notify', ['message' => __('Cost sheet updated successfully!'), 'type' => 'success']);
        }

        $cs->items()->delete(); // sync clean

        return redirect()->route('admin.cost-sheets.index');
    }

    public function cancel()
    {
        return redirect()->route('admin.cost-sheets.index');
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validate([
                'title'          => 'required|string|max:255',
                'season'         => 'required|in:high,low',
                'start_date'     => 'nullable|date',
                'end_date'       => 'nullable|date|after_or_equal:start_date',
                'num_adults'     => 'required|integer|min:1',
                'num_children'   => 'required|integer|min:0',
                'num_vehicles'   => 'required|integer|min:1',
            ]);

            // Auto-generate matrix days from duration if empty
            if (empty($this->matrixDays)) {
                $numDays = max(1, (int) $this->duration_days);
                for ($d = 1; $d <= $numDays; $d++) {
                    $this->matrixDays[] = [
                        'label'          => 'Day ' . $d,
                        'destination_id' => null,
                        'addons'         => [],
                    ];
                    foreach ($this->matrixRows as $ri => $row) {
                        $this->matrixRows[$ri]['amounts'][] = 0;
                    }
                }
            }

            $this->currentDayStep = 0;
            $this->recalculate();
            $this->step = 2;
        } elseif ($this->step === 2) {
            $this->recalculate();
            $this->step = 4;
        }
    }

    public function prevStep(): void
    {
        if ($this->step === 4) {
            $this->step = 2;
        } elseif ($this->step === 2) {
            $this->step = 1;
        }
    }

    public function nextDayStep(): void
    {
        $this->recalculate();
        if ($this->currentDayStep < count($this->matrixDays) - 1) {
            $this->currentDayStep++;
        } else {
            // Last day done — go to matrix/summary
            $this->step = 4;
        }
    }

    public function prevDayStep(): void
    {
        if ($this->currentDayStep > 0) {
            $this->currentDayStep--;
        } else {
            $this->step = 1;
        }
    }

    public function generateGroupPricingChart(): array
    {
        $days      = max(1, (int) $this->duration_days);
        $children  = max(0, (int) $this->num_children);
        $vehicles  = max(1, (int) $this->num_vehicles);
        $s         = $this->season === 'high' ? 'high' : 'low';
        $markupPct = max(0, (float) $this->markup_percentage);

        $numDays = count($this->matrixDays);
        $chart = [];
        for ($a = 1; $a <= 7; $a++) {
            $subtotal = 0.0;
            $fixedCrater = 0.0;

            foreach ($this->matrixRows as $row) {
                $type = $row['type'] ?? 'custom';
                if ($row['is_per_pax'] ?? false) {
                    if (str_ends_with($type, '_child')) {
                        $mult = $children;
                    } else {
                        $mult = $a;
                    }
                } else {
                    $mult = 1;
                }
                $rowTotal = 0.0;
                for ($di = 0; $di < $numDays; $di++) {
                    $rowTotal += (float) ($row['amounts'][$di] ?? 0) * $mult;
                }
                $subtotal += $rowTotal;

                if (in_array($type, ['crater', 'crater_adult', 'crater_child', 'car']) && !($row['is_per_pax'] ?? false)) {
                    $fixedCrater += $rowTotal;
                }
            }

            $markupAmount = round($subtotal * ($markupPct / 100), 2);
            $grandTotal   = round($subtotal + $markupAmount, 2);
            $guests       = $a + $children;
            $perPerson    = $guests > 0 ? round($grandTotal / $guests, 2) : $grandTotal;
            $perPersonCrater = $guests > 0 ? round($fixedCrater / $guests, 2) : $fixedCrater;

            $chart[$a] = [
                'adults'            => $a,
                'guests'            => $guests,
                'grand_total'       => $grandTotal,
                'per_person'        => $perPerson,
                'per_person_crater' => $perPersonCrater,
                'crater_total'      => $fixedCrater,
                'is_current'        => ($a === (int) $this->num_adults),
            ];
        }
        return $chart;
    }

    public function getMatrixTotals(): array
    {
        $numDays    = count($this->matrixDays);
        $dayTotals  = array_fill(0, $numDays, 0.0);
        $rowTotals  = [];
        $grandTotal = 0.0;

        foreach ($this->matrixRows as $ri => $row) {
            $type = $row['type'] ?? 'custom';
            if ($row['is_per_pax'] ?? false) {
                if (str_ends_with($type, '_child')) {
                    $mult = max(0, $this->num_children);
                } else {
                    $mult = max(1, $this->matrixNumPax);
                }
            } else {
                $mult = 1;
            }
            $rowTotal = 0.0;

            for ($di = 0; $di < $numDays; $di++) {
                $amt           = (float) ($row['amounts'][$di] ?? 0) * $mult;
                $dayTotals[$di] += $amt;
                $rowTotal       += $amt;
            }

            $rowTotals[$ri] = round($rowTotal, 2);
            $grandTotal    += $rowTotal;
        }

        return [
            'day_totals'  => $dayTotals,
            'row_totals'  => $rowTotals,
            'grand_total' => round($grandTotal, 2),
        ];
    }

    public function addMatrixDay(): void
    {
        $this->matrixDays[] = [
            'label'          => 'Day ' . (count($this->matrixDays) + 1),
            'destination_id' => null,
            'addons'         => [],
        ];
        foreach ($this->matrixRows as $ri => $row) {
            $this->matrixRows[$ri]['amounts'][] = 0;
        }
    }

    public function removeMatrixDay(int $i): void
    {
        if (!isset($this->matrixDays[$i])) return;
        unset($this->matrixDays[$i]);
        $this->matrixDays = array_values($this->matrixDays);
        foreach ($this->matrixRows as $ri => $row) {
            $amounts = $row['amounts'];
            unset($amounts[$i]);
            $this->matrixRows[$ri]['amounts'] = array_values($amounts);
        }
    }

    public function removeMatrixRow(int $index): void
    {
        if (isset($this->matrixRows[$index])) {
            unset($this->matrixRows[$index]);
            $this->matrixRows = array_values($this->matrixRows);
            $this->recalculate();
        }
    }

    public function addMatrixRow(): void
    {
        $numDays  = count($this->matrixDays);
        $dayFees  = $this->getMatrixDayFees();
        $children = max(0, (int) $this->num_children);

        // Types that should be split into adult + child sub-rows automatically
        $splitTypes = ['park_fee', 'pass_fee', 'crater', 'concession'];

        if (in_array($this->newRowType, $splitTypes)) {
            // ── Adult row ──────────────────────────────────────────────────
            $adultType  = $this->newRowType . '_adult';
            $adultLabel = $this->defaultRowLabel($adultType);
            $adultFee   = $dayFees[(int)$this->currentDayStep][$adultType] ?? 0;
            $adultAmount = $adultFee > 0 ? (float) $adultFee : 0.0;

            $existingAdultIndex = collect($this->matrixRows)->search(fn($r) => ($r['type'] ?? '') === $adultType);

            if ($existingAdultIndex !== false) {
                // Add to existing day amount
                $this->matrixRows[$existingAdultIndex]['amounts'][(int)$this->currentDayStep] += $adultAmount;
                if (!in_array((int)$this->currentDayStep, $this->matrixRows[$existingAdultIndex]['active_days'] ?? [])) {
                    $this->matrixRows[$existingAdultIndex]['active_days'][] = (int)$this->currentDayStep;
                }
            } else {
                $amounts = array_fill(0, $numDays, 0.0);
                $amounts[(int)$this->currentDayStep] = $adultAmount;
                $this->matrixRows[] = [
                    'type'       => $adultType,
                    'label'      => $adultLabel,
                    'is_per_pax' => true,
                    'is_shared'  => false,
                    'amounts'    => $amounts,
                    'active_days'=> [(int)$this->currentDayStep],
                ];
            }

            // ── Child row (only if the group has children) ──────────────────
            if ($children > 0) {
                $childType  = $this->newRowType . '_child';
                $childLabel = $this->defaultRowLabel($childType);
                $childFee   = $dayFees[(int)$this->currentDayStep][$childType] ?? 0;
                $childAmount = $childFee > 0 ? (float) $childFee : 0.0;

                $existingChildIndex = collect($this->matrixRows)->search(fn($r) => ($r['type'] ?? '') === $childType);

                if ($existingChildIndex !== false) {
                    $this->matrixRows[$existingChildIndex]['amounts'][(int)$this->currentDayStep] += $childAmount;
                    if (!in_array((int)$this->currentDayStep, $this->matrixRows[$existingChildIndex]['active_days'] ?? [])) {
                        $this->matrixRows[$existingChildIndex]['active_days'][] = (int)$this->currentDayStep;
                    }
                } else {
                    $amounts = array_fill(0, $numDays, 0.0);
                    $amounts[(int)$this->currentDayStep] = $childAmount;
                    $this->matrixRows[] = [
                        'type'       => $childType,
                        'label'      => $childLabel,
                        'is_per_pax' => true,
                        'is_shared'  => false,
                        'amounts'    => $amounts,
                        'active_days'=> [(int)$this->currentDayStep],
                    ];
                }
            }
        } else {
            // ── All other types (car, snacks, accommodation, activity, custom) ──
            // Use default label to group predefined types, allow custom label for custom types
            $label = $this->newRowType === 'custom' && trim($this->newRowLabel) 
                ? trim($this->newRowLabel) 
                : $this->defaultRowLabel($this->newRowType);

            $fee = $dayFees[(int)$this->currentDayStep][$this->newRowType] ?? 0;
            $amount = $fee > 0 ? (float) $fee : (float) $this->newRowBaseAmount;

            // Find existing row by type (and label if it's custom)
            $existingIndex = collect($this->matrixRows)->search(function($r) use ($label) {
                if ($this->newRowType === 'custom') {
                    return ($r['type'] ?? '') === 'custom' && ($r['label'] ?? '') === $label;
                }
                return ($r['type'] ?? '') === $this->newRowType;
            });

            if ($existingIndex !== false) {
                $this->matrixRows[$existingIndex]['amounts'][(int)$this->currentDayStep] += $amount;
                if (!in_array((int)$this->currentDayStep, $this->matrixRows[$existingIndex]['active_days'] ?? [])) {
                    $this->matrixRows[$existingIndex]['active_days'][] = (int)$this->currentDayStep;
                }
            } else {
                $amounts = array_fill(0, $numDays, 0.0);
                $amounts[(int)$this->currentDayStep] = $amount;
                $this->matrixRows[] = [
                    'type'       => $this->newRowType,
                    'label'      => $label,
                    'is_per_pax' => $this->newRowIsPerPax,
                    'is_shared'  => $this->newRowIsShared,
                    'amounts'    => $amounts,
                    'active_days'=> [(int)$this->currentDayStep],
                ];
            }
        }

        $this->newRowIsShared = false;

        $this->newRowLabel = '';
        $this->newRowItemId = null;
        $this->newRowBaseAmount = 0.0;
        $this->recalculate();
    }

    protected function defaultRowLabel(string $type): string
    {
        return match ($type) {
            'car'                            => 'Transport',
            'park_fee', 'park_fee_adult'     => 'Park Fee (Adult)',
            'park_fee_child'                 => 'Park Fee (Child)',
            'pass_fee', 'pass_fee_adult'     => 'Pass Fee (Adult)',
            'pass_fee_child'                 => 'Pass Fee (Child)',
            'crater', 'crater_adult'         => 'Crater Fee (Adult)',
            'crater_child'                   => 'Crater Fee (Child)',
            'concession', 'concession_adult' => 'Concession (Adult)',
            'concession_child'               => 'Concession (Child)',
            'snacks'                         => 'Snacks',
            'accommodation'                  => 'Accommodation',
            'activity'                       => 'Activity',
            default                          => 'Custom',
        };
    }


    public function getMatrixDayFees(): array
    {
        $fees = [];
        $s = $this->season === 'high' ? 'high' : 'low';
        foreach ($this->matrixDays as $di => $day) {
            $destId = $day['destination_id'] ?? null;
            if ($destId) {
                $dest = Destination::find($destId);
                if ($dest) {
                    $fees[$di] = [
                        'park_fee_adult'   => (float) $dest->{"park_fee_adult_{$s}_season"},
                        'park_fee_child'   => (float) $dest->{"park_fee_child_{$s}_season"},
                        'concession_adult' => (float) $dest->{"concession_fee_adult_{$s}_season"},
                        'concession_child' => (float) $dest->{"concession_fee_child_{$s}_season"},
                        'crater_adult'     => (float) $dest->{"crater_fee_adult_{$s}_season"},
                        'crater_child'     => (float) $dest->{"crater_fee_child_{$s}_season"},
                        'pass_fee_adult'   => (float) $dest->{"pass_fee_adult_{$s}_season"},
                        'pass_fee_child'   => (float) $dest->{"pass_fee_child_{$s}_season"},
                        // legacy compatibility
                        'park_fee'         => (float) $dest->{"park_fee_adult_{$s}_season"},
                        'concession'       => (float) $dest->{"concession_fee_adult_{$s}_season"},
                        'crater'           => (float) $dest->{"crater_fee_adult_{$s}_season"},
                        'pass_fee'         => (float) $dest->{"pass_fee_adult_{$s}_season"},
                    ];
                    continue;
                }
            }
            $fees[$di] = [
                'park_fee_adult' => 0, 'park_fee_child' => 0,
                'concession_adult' => 0, 'concession_child' => 0,
                'crater_adult' => 0, 'crater_child' => 0,
                'pass_fee_adult' => 0, 'pass_fee_child' => 0,
                'park_fee' => 0, 'concession' => 0, 'crater' => 0, 'pass_fee' => 0
            ];
        }
        return $fees;
    }


    public function render()
    {
        return view('livewire.admin.cost-sheets.cost-sheet-form', [
            'destinations'        => Destination::where('is_active', true)->orderBy('name')->get(),
            'activities'          => Activity::where('is_active', true)->orderBy('name')->get(),
            'transports'          => Transport::where('is_active', true)->orderBy('name')->get(),
            'mealPlans'           => MealPlan::where('is_active', true)->orderBy('name')->get(),
            'accommodations'      => Accommodation::with('rates')->where('is_active', true)->orderBy('name')->get(),
            // Explicitly expose properties used as raw PHP variables in blade @php blocks
            'num_adults'          => (int) $this->num_adults,
            'num_children'        => (int) $this->num_children,
            'num_vehicles'        => (int) $this->num_vehicles,
            'matrixRows'          => $this->matrixRows,
            'matrixDays'          => $this->matrixDays,
            'currentDayStep'      => (int) $this->currentDayStep,
            'markup_amount'       => (float) $this->markup_amount,
            'grand_total'         => (float) $this->grand_total,
            'groupPricingChart'   => $this->generateGroupPricingChart(),
        ]);
    }
}
