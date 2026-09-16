<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CostSheet extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'itinerary_id',
        'reference_number',
        'title',
        'calculation_type',
        'destination_id',
        'season',
        'start_date',
        'end_date',
        'duration_days',
        'num_adults',
        'num_children',
        'num_vehicles',

        // Fee snapshots
        'snap_concession_fee_adult_high',
        'snap_concession_fee_adult_low',
        'snap_concession_fee_child_high',
        'snap_concession_fee_child_low',
        'snap_crater_fee_adult_high',
        'snap_crater_fee_adult_low',
        'snap_crater_fee_child_high',
        'snap_crater_fee_child_low',
        'snap_park_fee_adult_high',
        'snap_park_fee_adult_low',
        'snap_park_fee_child_high',
        'snap_park_fee_child_low',
        'snap_pass_fee_adult_high',
        'snap_pass_fee_adult_low',
        'snap_pass_fee_child_high',
        'snap_pass_fee_child_low',

        // Subtotals
        'subtotal_park_fees',
        'subtotal_concession_fees',
        'subtotal_crater_fees',
        'subtotal_pass_fees',
        'subtotal_activities',
        'subtotal_transport',
        'subtotal_meals',
        'subtotal_accommodation',
        'subtotal_custom',
        'subtotal',

        // Markup & Grand Total
        'markup_percentage',
        'markup_amount',
        'grand_total',

        'notes',
        'matrix_days',
        'matrix_rows',
        'status',
        'created_by',
    ];

    protected $casts = [
        'start_date'              => 'date',
        'end_date'                => 'date',
        'duration_days'           => 'integer',
        'num_adults'              => 'integer',
        'num_children'            => 'integer',
        'num_vehicles'            => 'integer',
        'snap_concession_fee_adult_high' => 'decimal:2',
        'snap_concession_fee_adult_low'  => 'decimal:2',
        'snap_concession_fee_child_high' => 'decimal:2',
        'snap_concession_fee_child_low'  => 'decimal:2',
        'snap_crater_fee_adult_high'     => 'decimal:2',
        'snap_crater_fee_adult_low'      => 'decimal:2',
        'snap_crater_fee_child_high'     => 'decimal:2',
        'snap_crater_fee_child_low'      => 'decimal:2',
        'snap_park_fee_adult_high'       => 'decimal:2',
        'snap_park_fee_adult_low'        => 'decimal:2',
        'snap_park_fee_child_high'       => 'decimal:2',
        'snap_park_fee_child_low'        => 'decimal:2',
        'snap_pass_fee_adult_high'       => 'decimal:2',
        'snap_pass_fee_adult_low'        => 'decimal:2',
        'snap_pass_fee_child_high'       => 'decimal:2',
        'snap_pass_fee_child_low'        => 'decimal:2',
        'subtotal_park_fees'       => 'decimal:2',
        'subtotal_concession_fees' => 'decimal:2',
        'subtotal_crater_fees'     => 'decimal:2',
        'subtotal_pass_fees'       => 'decimal:2',
        'subtotal_activities'      => 'decimal:2',
        'subtotal_transport'       => 'decimal:2',
        'subtotal_meals'           => 'decimal:2',
        'subtotal_accommodation'   => 'decimal:2',
        'subtotal_custom'          => 'decimal:2',
        'subtotal'                 => 'decimal:2',
        'markup_percentage'        => 'decimal:2',
        'markup_amount'            => 'decimal:2',
        'grand_total'              => 'decimal:2',
        'matrix_days'              => 'array',
        'matrix_rows'              => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->reference_number)) {
                $year  = date('Y');
                $count = static::whereYear('created_at', $year)->withTrashed()->count() + 1;
                $model->reference_number = 'CS-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function itinerary(): BelongsTo
    {
        return $this->belongsTo(Itinerary::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CostSheetItem::class)->orderBy('sort_order')->orderBy('id');
    }

    public function itemsByType(string $type): HasMany
    {
        return $this->hasMany(CostSheetItem::class)->where('item_type', $type)->orderBy('sort_order');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Returns the applicable fee for a given fee group based on season and age group.
     */
    public function getFee(string $feeGroup, string $ageGroup = 'adult'): float
    {
        $column = "snap_{$feeGroup}_{$ageGroup}_" . $this->season;
        return (float) ($this->$column ?? 0);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'finalized' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            default     => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
        };
    }

    /**
     * Generate group pricing chart for 1-7 pax.
     */
    public function generateGroupPricingChart(): array
    {
        $days      = max(1, (int) $this->duration_days);
        $children  = max(0, (int) $this->num_children);
        $vehicles  = max(1, (int) $this->num_vehicles);
        $s         = $this->season === 'high' ? 'high' : 'low';
        $markupPct = max(0, (float) $this->markup_percentage);

        if (($this->calculation_type ?? 'standard') === 'matrix') {
            $matrixDays = $this->matrix_days ?: [];
            $matrixRows = $this->matrix_rows ?: [];
            $numDays = count($matrixDays);
            $chart = [];
            for ($a = 1; $a <= 7; $a++) {
                $subtotal = 0.0;
                $fixedCrater = 0.0;

                foreach ($matrixRows as $row) {
                    $mult = ($row['is_per_pax'] ?? false) ? $a : 1;
                    $rowTotal = 0.0;
                    for ($di = 0; $di < $numDays; $di++) {
                        $rowTotal += (float) ($row['amounts'][$di] ?? 0) * $mult;
                    }
                    $subtotal += $rowTotal;

                    if (in_array($row['type'] ?? '', ['crater', 'car']) && !($row['is_per_pax'] ?? false)) {
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

        // Fee rates
        $parkFeeAdultCol = "snap_park_fee_adult_{$s}";
        $parkFeeChildCol = "snap_park_fee_child_{$s}";
        $concessionFeeAdultCol = "snap_concession_fee_adult_{$s}";
        $concessionFeeChildCol = "snap_concession_fee_child_{$s}";
        $craterFeeAdultCol = "snap_crater_fee_adult_{$s}";
        $craterFeeChildCol = "snap_crater_fee_child_{$s}";
        $passFeeAdultCol = "snap_pass_fee_adult_{$s}";
        $passFeeChildCol = "snap_pass_fee_child_{$s}";

        $parkFeeAdult    = (float) $this->$parkFeeAdultCol;
        $parkFeeChild    = (float) $this->$parkFeeChildCol;
        $concessionAdult = (float) $this->$concessionFeeAdultCol;
        $concessionChild = (float) $this->$concessionFeeChildCol;
        $craterAdult     = (float) $this->$craterFeeAdultCol;
        $craterChild     = (float) $this->$craterFeeChildCol;
        $passAdult       = (float) $this->$passFeeAdultCol;
        $passChild       = (float) $this->$passFeeChildCol;

        // Fixed costs
        $fixedConcession = $concessionAdult * $vehicles * $days;
        $fixedCrater = $craterAdult * $vehicles;

        // Line-item buckets
        $fixedLineItems    = 0.00;
        $perAdultLineItems = 0.00;
        $perGuestLineItems = 0.00;

        $baseAdults = max(1, (int) $this->num_adults);

        foreach ($this->items as $item) {
            $unitCost = (float) ($item->unit_cost ?? 0);
            $qty      = (int)   ($item->quantity  ?? 1);
            $type     = $item->item_type ?? 'custom';

            match ($type) {
                'transport'     => $fixedLineItems    += $unitCost * $qty,
                'accommodation' => $fixedLineItems    += $unitCost * $qty,
                'meal_plan'     => $perGuestLineItems += $unitCost * $days,
                'activity'      => $perAdultLineItems += $unitCost,
                default         => $fixedLineItems    += $unitCost * $qty,
            };
        }

        $chart = [];
        for ($a = 1; $a <= 7; $a++) {
            $guests = $a + $children;

            $parkFees = ($parkFeeAdult * $a + $parkFeeChild * $children) * $days;
            $passFees = ($passAdult * $a + $passChild * $children);

            $scaledMeals      = $perGuestLineItems * $guests;
            $scaledActivities = $perAdultLineItems * $a;

            $subtotal = $fixedConcession
                + $fixedCrater
                + $fixedLineItems
                + $parkFees
                + $passFees
                + $scaledMeals
                + $scaledActivities;

            $markupAmount = round($subtotal * ($markupPct / 100), 2);
            $grandTotal   = round($subtotal + $markupAmount, 2);

            $perPerson = $guests > 0 ? round($grandTotal / $guests, 2) : $grandTotal;
            $perPersonCrater = $guests > 0 ? round($fixedCrater / $guests, 2) : $fixedCrater;

            $chart[$a] = [
                'adults'            => $a,
                'guests'            => $guests,
                'grand_total'       => $grandTotal,
                'per_person'        => $perPerson,
                'per_person_crater' => $perPersonCrater,
                'crater_total'      => $fixedCrater,
                'is_current'        => ($a === $baseAdults),
            ];
        }

        return $chart;
    }
}

