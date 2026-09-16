<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cost Sheet {{ $costSheet->reference_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Merriweather', serif;
            font-size: 12px;
            color: #1f2937;
            line-height: 1.5;
        }
        .container { width: 100%; padding: 28px 32px; }

        /* Header */
        .header { display: table; width: 100%; margin-bottom: 24px; border-bottom: 2px solid #e5e7eb; padding-bottom: 20px; }
        .header-left { display: table-cell; width: 50%; vertical-align: middle; }
        .header-right { display: table-cell; width: 50%; text-align: right; vertical-align: middle; }
        .logo { max-width: 140px; height: auto; }
        .doc-title { font-size: 22px; font-weight: bold; color: #4f46e5; margin-bottom: 2px; }
        .doc-ref { font-size: 11px; color: #6b7280; }
        .company-name { font-size: 15px; font-weight: bold; color: #111827; }

        /* Meta Info */
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .meta-table td { padding: 4px 0; font-size: 11px; }
        .meta-label { color: #6b7280; text-transform: uppercase; font-weight: bold; font-size: 9px; letter-spacing: 0.05em; }
        .meta-value { color: #111827; font-weight: bold; }

        /* Trip Info Banner */
        .trip-banner { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 3px; padding: 12px 16px; margin-bottom: 20px; }
        .trip-banner-grid { display: table; width: 100%; }
        .trip-banner-cell { display: table-cell; text-align: center; vertical-align: top; }
        .trip-banner-label { font-size: 9px; color: #6b7280; text-transform: uppercase; font-weight: bold; letter-spacing: 0.05em; }
        .trip-banner-value { font-size: 14px; font-weight: bold; color: #1f2937; margin-top: 2px; }
        .season-badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: bold; }
        .season-high { background: #fef3c7; color: #92400e; }
        .season-low  { background: #dbeafe; color: #1e40af; }

        /* Section Header */
        .section-header { background: #4f46e5; color: white; padding: 7px 12px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.08em; border-radius: 3px 4px 0 0; margin-bottom: 0; }
        .section-alt-header { background: #6b7280; color: white; padding: 7px 12px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.08em; border-radius: 3px 4px 0 0; }

        /* Fee Table */
        .fee-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .fee-table th { background: #f9fafb; border-bottom: 1px solid #e5e7eb; padding: 7px 10px; text-align: left; font-size: 9px; color: #374151; text-transform: uppercase; }
        .fee-table td { padding: 8px 10px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        .fee-table .text-right { text-align: right; }
        .fee-table .amount { font-family: monospace; font-weight: bold; color: #111827; }
        .fee-table tr.subtotal td { background: #f3f4f6; font-weight: bold; }
        .fee-table .note-row td { color: #6b7280; font-size: 10px; padding-top: 2px; padding-bottom: 6px; border-bottom: none; }

        /* Category color stripes */
        .cat-park         { border-left: 3px solid #f59e0b; }
        .cat-concession   { border-left: 3px solid #6366f1; }
        .cat-crater       { border-left: 3px solid #10b981; }
        .cat-pass         { border-left: 3px solid #ef4444; }
        .cat-activity     { border-left: 3px solid #8b5cf6; }
        .cat-transport    { border-left: 3px solid #06b6d4; }
        .cat-meal         { border-left: 3px solid #f97316; }
        .cat-accomm       { border-left: 3px solid #ec4899; }
        .cat-custom       { border-left: 3px solid #6b7280; }

        /* Grand Total */
        .totals-box { width: 45%; margin-left: 55%; }
        .totals-row { display: table; width: 100%; padding: 6px 0; border-bottom: 1px solid #f3f4f6; }
        .totals-label { display: table-cell; color: #6b7280; font-size: 11px; }
        .totals-value { display: table-cell; text-align: right; font-family: monospace; font-weight: bold; color: #1f2937; font-size: 11px; }
        .grand-total-row { display: table; width: 100%; padding: 10px 0; border-top: 2px solid #4f46e5; margin-top: 4px; }
        .grand-total-label { display: table-cell; font-weight: bold; font-size: 14px; color: #4f46e5; }
        .grand-total-value { display: table-cell; text-align: right; font-family: monospace; font-weight: bold; font-size: 18px; color: #059669; }
        .per-person-row { display: table; width: 100%; padding: 3px 0; }
        .per-person-label { display: table-cell; color: #9ca3af; font-size: 10px; }
        .per-person-value { display: table-cell; text-align: right; font-family: monospace; color: #9ca3af; font-size: 10px; }

        /* Status */
        .status-draft     { background: #fef3c7; color: #92400e; }
        .status-finalized { background: #d1fae5; color: #065f46; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 3px; font-size: 10px; font-weight: bold; text-transform: uppercase; }

        /* Footer */
        .footer { margin-top: 36px; padding-top: 16px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 10px; color: #9ca3af; }
        .notes-box { margin-top: 16px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 3px; padding: 10px 12px; font-size: 11px; color: #4b5563; }

        /* Matrix Table Styles */
        .matrix-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .matrix-table th { background: #1e293b; color: white; border: 1px solid #475569; padding: 6px 8px; font-size: 9px; text-align: center; }
        .matrix-table th.label-col { text-align: left; width: 150px; }
        .matrix-table td { border: 1px solid #e2e8f0; padding: 6px 8px; font-size: 10px; text-align: center; }
        .matrix-table td.label-col { text-align: left; font-weight: bold; background: #f8fafc; }
        .matrix-table td.total-col { text-align: right; font-weight: bold; font-family: monospace; }
        .matrix-table tr.total-row td { background: #f1f5f9; font-weight: bold; }
        .matrix-table tr.cum-row td { background: #0f172a; color: white; font-weight: bold; }
        .matrix-table .badge-pax { display: inline-block; padding: 1px 4px; background: #e0e7ff; color: #3730a3; border-radius: 3px; font-size: 8px; font-weight: bold; margin-left: 4px; }
        .matrix-table .badge-fixed { display: inline-block; padding: 1px 4px; background: #f1f5f9; color: #475569; border-radius: 3px; font-size: 8px; font-weight: bold; margin-left: 4px; }
        .matrix-table .scaled-hint { display: block; font-size: 8px; color: #64748b; font-weight: normal; margin-top: 1px; }

        /* Group Pricing Table Styles */
        .group-table { width: 100%; border-collapse: collapse; margin-top: 16px; margin-bottom: 20px; }
        .group-table th { background: #4f46e5; color: white; border: 1px solid #c7d2fe; padding: 6px 8px; font-size: 10px; text-align: left; text-transform: uppercase; }
        .group-table th.text-right { text-align: right; }
        .group-table td { border-bottom: 1px solid #e5e7eb; padding: 8px 8px; font-size: 11px; }
        .group-table tr.current-group td { background: #e0e7ff; font-weight: bold; color: #312e81; }
        .group-table .amount { font-family: monospace; font-weight: bold; }
        .group-table .text-right { text-align: right; }
    </style>
</head>
<body>
<div class="container">

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            @if($settings->company_logo)
                <img src="{{ public_path('storage/' . $settings->company_logo) }}" class="logo">
            @else
                <div class="company-name">{{ $settings->company_name }}</div>
                <div style="font-size:10px; color:#6b7280;">{!! strip_tags($settings->address, '<br>') !!}</div>
            @endif
        </div>
        <div class="header-right">
            <div class="doc-title">{{ __('COST SHEET') }}</div>
            <div class="doc-ref">{{ $costSheet->reference_number }}</div>
            <div style="margin-top: 6px;">
                <span class="status-badge status-{{ $costSheet->status }}">{{ ucfirst($costSheet->status) }}</span>
            </div>
            <div style="margin-top:8px; font-size:10px; color:#6b7280;">
                {{ $settings->email }} | {{ $settings->phone }}
            </div>
        </div>
    </div>

    {{-- Title & Trip Banner --}}
    <div style="margin-bottom: 16px;">
        <div style="font-size: 16px; font-weight: bold; color: #111827;">{{ $costSheet->title }}</div>
        <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">{{ __('Destination:') }}<strong>{{ $costSheet->destination?->name ?? '—' }}</strong> &nbsp;|&nbsp;
            Prepared by: <strong>{{ $costSheet->createdBy?->name ?? '—' }}</strong> &nbsp;|&nbsp;
            Date: <strong>{{ $costSheet->created_at->format('M d, Y') }}</strong>
        </div>
    </div>

    <div class="trip-banner">
        <div class="trip-banner-grid">
            <div class="trip-banner-cell">
                <div class="trip-banner-label">{{ __('Season') }}</div>
                <div class="trip-banner-value">
                    <span class="season-badge {{ $costSheet->season === 'high' ? 'season-high' : 'season-low' }}">
                        {{ ucfirst($costSheet->season) }} Season
                    </span>
                </div>
            </div>
            <div class="trip-banner-cell">
                <div class="trip-banner-label">{{ __('Travel Dates') }}</div>
                <div class="trip-banner-value">
                    @if($costSheet->start_date && $costSheet->end_date)
                        {{ $costSheet->start_date->format('M d') }} – {{ $costSheet->end_date->format('M d, Y') }}
                    @else
                        TBD
                    @endif
                </div>
            </div>
            <div class="trip-banner-cell">
                <div class="trip-banner-label">{{ __('Duration') }}</div>
                <div class="trip-banner-value">{{ $costSheet->duration_days }} Day{{ $costSheet->duration_days != 1 ? 's' : '' }}</div>
            </div>
            <div class="trip-banner-cell">
                <div class="trip-banner-label">{{ __('Adults / Children') }}</div>
                <div class="trip-banner-value">{{ $costSheet->num_adults }} / {{ $costSheet->num_children }}</div>
            </div>
            <div class="trip-banner-cell">
                <div class="trip-banner-label">{{ __('Vehicles') }}</div>
                <div class="trip-banner-value">{{ $costSheet->num_vehicles }}</div>
            </div>
        </div>
    </div>

    @if(($costSheet->calculation_type ?? 'standard') === 'standard')
        {{-- Government / Park Fees --}}
        @php
            $hasParkFees = $costSheet->subtotal_park_fees > 0 || $costSheet->subtotal_concession_fees > 0
                        || $costSheet->subtotal_crater_fees > 0 || $costSheet->subtotal_pass_fees > 0;
            $season = $costSheet->season;
            $feeRows = [
                ['label' => 'Park Entry Fees (Adult)',    'class' => 'cat-park',       'subtotal' => $costSheet->subtotal_park_fees,       'unit' => $season === 'high' ? $costSheet->snap_park_fee_adult_high       : $costSheet->snap_park_fee_adult_low,       'qty_label' => $costSheet->num_adults . ' adults × ' . $costSheet->duration_days . ' days'],
                ['label' => 'Concession Fees (Adult)',    'class' => 'cat-concession', 'subtotal' => $costSheet->subtotal_concession_fees, 'unit' => $season === 'high' ? $costSheet->snap_concession_fee_adult_high : $costSheet->snap_concession_fee_adult_low, 'qty_label' => $costSheet->num_vehicles . ' vehicle(s) × ' . $costSheet->duration_days . ' days'],
                ['label' => 'Crater Fees (Adult)',        'class' => 'cat-crater',     'subtotal' => $costSheet->subtotal_crater_fees,     'unit' => $season === 'high' ? $costSheet->snap_crater_fee_adult_high     : $costSheet->snap_crater_fee_adult_low,     'qty_label' => $costSheet->num_adults . ' adults (one-time)'],
                ['label' => 'Pass Fees (Adult)',          'class' => 'cat-pass',       'subtotal' => $costSheet->subtotal_pass_fees,       'unit' => $season === 'high' ? $costSheet->snap_pass_fee_adult_high       : $costSheet->snap_pass_fee_adult_low,       'qty_label' => $costSheet->num_adults . ' adults (one-time)'],
            ];
        @endphp

        @if($hasParkFees)
        <div class="section-header">{{ __('Government & Park Fees') }}</div>
        <table class="fee-table">
            <thead>
                <tr>
                    <th>{{ __('Fee Type') }}</th>
                    <th>{{ __('Unit Rate') }}</th>
                    <th>{{ __('Quantity / Basis') }}</th>
                    <th class="text-right">{{ __('Amount (USD)') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($feeRows as $row)
                @if($row['subtotal'] > 0)
                <tr class="{{ $row['class'] }}">
                    <td>{{ $row['label'] }}</td>
                    <td class="amount">$ {{ number_format($row['unit'], 2) }}</td>
                    <td style="color:#6b7280; font-size:10px;">{{ $row['qty_label'] }}</td>
                    <td class="text-right amount">$ {{ number_format($row['subtotal'], 2) }}</td>
                </tr>
                @endif
                @endforeach
                <tr class="subtotal">
                    <td colspan="3" style="text-align:right; color:#4b5563;">{{ __('Government Fees Subtotal') }}</td>
                    <td class="text-right amount">
                        $ {{ number_format($costSheet->subtotal_park_fees + $costSheet->subtotal_concession_fees + $costSheet->subtotal_crater_fees + $costSheet->subtotal_pass_fees, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
        @endif

        {{-- Add-on Items --}}
        @php
            $itemsByType = $costSheet->items->groupBy('item_type');
            $typeConfig  = [
                'activity'      => ['label' => 'Activities',    'class' => 'cat-activity'],
                'transport'     => ['label' => 'Transport',     'class' => 'cat-transport'],
                'meal_plan'     => ['label' => 'Meal Plans',    'class' => 'cat-meal'],
                'accommodation' => ['label' => 'Accommodation', 'class' => 'cat-accomm'],
                'custom'        => ['label' => 'Other Items',   'class' => 'cat-custom'],
            ];
        @endphp

        @if($costSheet->items->isNotEmpty())<div class="section-alt-header">{{ __('Add-on Items') }}</div>
        <table class="fee-table">
            <thead>
                <tr>
                    <th>{{ __('Category') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Unit Cost') }}</th>
                    <th>{{ __('Qty') }}</th>
                    <th class="text-right">{{ __('Amount (USD)') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($costSheet->items as $item)
                <tr class="{{ $typeConfig[$item->item_type]['class'] ?? '' }}">
                    <td style="font-size:9px; color:#6b7280; text-transform:uppercase; font-weight:bold;">
                        {{ $typeConfig[$item->item_type]['label'] ?? $item->item_type }}
                    </td>
                    <td>
                        {{ $item->name }}
                        @if($item->rate_label)<span style="font-size:10px; color:#9ca3af;"> – {{ $item->rate_label }}</span>@endif
                    </td>
                    <td class="amount">$ {{ number_format($item->unit_cost, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td class="text-right amount">$ {{ number_format($item->line_total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

    @else
        {{-- Custom Cost Matrix --}}
        @php
            $matrixDays = $costSheet->matrix_days ?: [];
            $matrixRows = $costSheet->matrix_rows ?: [];
            $numMxDays = count($matrixDays);
            $matrixNumPax = max(1, $costSheet->num_adults);

            $dayTotals  = array_fill(0, $numMxDays, 0.0);
            $rowTotals  = [];
            $grandTotal = 0.0;

            foreach ($matrixRows as $ri => $row) {
                $mult     = ($row['is_per_pax'] ?? false) ? $matrixNumPax : 1;
                $rowTotal = 0.0;

                for ($di = 0; $di < $numMxDays; $di++) {
                    $amt           = (float) ($row['amounts'][$di] ?? 0) * $mult;
                    $dayTotals[$di] += $amt;
                    $rowTotal       += $amt;
                }

                $rowTotals[$ri] = $rowTotal;
                $grandTotal    += $rowTotal;
            }
        @endphp

        <div class="section-header">Custom Cost Matrix ({{ $matrixNumPax }} Pax)</div>
        <table class="matrix-table">
            <thead>
                <tr>
                    @for($pax = 7; $pax >= 1; $pax--)
                    <th>{{ $pax }} person</th>
                    @endfor
                    <th class="label-col">{{ __('cost category') }}</th>
                    @foreach($matrixDays as $di => $day)
                    <th>
                        {{ !empty($day['label']) ? $day['label'] : ('Day ' . ($di + 1)) }}
                    </th>
                    @endforeach
                    <th style="text-align: right; width: 80px;">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $paxTotals = [7=>0, 6=>0, 5=>0, 4=>0, 3=>0, 2=>0, 1=>0];
                    $rowColors = [
                        'car'              => ['bg' => '#ecfdf5', 'text' => '#047857', 'border' => '#10b981'],
                        'park_fee'         => ['bg' => '#fffbeb', 'text' => '#92400e', 'border' => '#f59e0b'],
                        'park_fee_adult'   => ['bg' => '#fffbeb', 'text' => '#92400e', 'border' => '#f59e0b'],
                        'park_fee_child'   => ['bg' => '#fef3c7', 'text' => '#b45309', 'border' => '#f59e0b'],
                        'pass_fee'         => ['bg' => '#fdf4ff', 'text' => '#7e22ce', 'border' => '#d946ef'],
                        'pass_fee_adult'   => ['bg' => '#fdf4ff', 'text' => '#7e22ce', 'border' => '#d946ef'],
                        'pass_fee_child'   => ['bg' => '#fae8ff', 'text' => '#a21caf', 'border' => '#d946ef'],
                        'crater'           => ['bg' => '#f0fdf4', 'text' => '#166534', 'border' => '#10b981'],
                        'crater_adult'     => ['bg' => '#f0fdf4', 'text' => '#166534', 'border' => '#10b981'],
                        'crater_child'     => ['bg' => '#dcfce7', 'text' => '#15803d', 'border' => '#10b981'],
                        'snacks'           => ['bg' => '#fff7ed', 'text' => '#c2410c', 'border' => '#f97316'],
                        'concession'       => ['bg' => '#eef2ff', 'text' => '#3730a3', 'border' => '#6366f1'],
                        'concession_adult' => ['bg' => '#eef2ff', 'text' => '#3730a3', 'border' => '#6366f1'],
                        'concession_child' => ['bg' => '#e0e7ff', 'text' => '#4338ca', 'border' => '#6366f1'],
                        'accommodation'    => ['bg' => '#faf5ff', 'text' => '#6b21a8', 'border' => '#ec4899'],
                        'activity'         => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'border' => '#3b82f6'],
                        'custom'           => ['bg' => '#f9fafb', 'text' => '#374151', 'border' => '#6b7280'],
                    ];
                @endphp
                @foreach($matrixRows as $ri => $row)
                @php
                    $rc   = $rowColors[$row['type'] ?? 'custom'] ?? $rowColors['custom'];
                    $rowBaseTotal = 0;
                    for ($di = 0; $di < $numMxDays; $di++) {
                        $rowBaseTotal += (float) ($row['amounts'][$di] ?? 0);
                    }
                @endphp
                <tr>
                    @for($pax = 7; $pax >= 1; $pax--)
                    @php
                        $groupRowTotal = 0;
                        $type = $row['type'] ?? 'custom';
                        if ($row['is_per_pax'] ?? false) {
                            if (str_ends_with($type, '_child')) {
                                $groupRowTotal = ($rowBaseTotal * $costSheet->num_children);
                            } else {
                                $groupRowTotal = ($rowBaseTotal * $pax);
                            }
                        } else {
                            $groupRowTotal = $rowBaseTotal;
                        }
                        $paxTotals[$pax] += $groupRowTotal;
                    @endphp
                    <td>$ {{ number_format($groupRowTotal, 2) }}</td>
                    @endfor
                    <td class="label-col" style="border-left: 3px solid {{ $rc['border'] }}; background: {{ $rc['bg'] }}20; color: {{ $rc['text'] }}; font-size: 9px; line-height: 1.2;">
                        {{ $row['label'] ?? 'Custom' }}
                        @if($row['is_per_pax'] ?? false)
                            @if(str_ends_with($row['type'] ?? '', '_child'))
                                <span class="badge-pax">{{ __('per child') }}</span>
                            @else
                                <span class="badge-pax">{{ __('per adult') }}</span>
                            @endif
                        @elseif(in_array($row['type'] ?? '', ['car', 'crater', 'crater_adult', 'crater_child']))
                            <span class="badge-fixed">{{ __('fixed (group)') }}</span>
                        @else
                            <span class="badge-fixed">{{ __('fixed') }}</span>
                        @endif
                    </td>
                    @foreach($matrixDays as $di => $day)
                    @php $cellAmt = (float) ($row['amounts'][$di] ?? 0); @endphp
                    <td>
                        $ {{ number_format($cellAmt, 2) }}
                    </td>
                    @endforeach
                    <td class="total-col" style="background: {{ $rc['bg'] }}30; color: {{ $rc['text'] }};">
                        $ {{ number_format($rowBaseTotal, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                @php
                    $groupChart = $costSheet->generateGroupPricingChart();
                @endphp
                <tr class="total-row">
                    @for($pax = 7; $pax >= 1; $pax--)
                    <td style="font-family: monospace; font-weight: bold; background: #e0e7ff; color: #312e81;">$ {{ number_format($paxTotals[$pax], 2) }}</td>
                    @endfor
                    <td class="label-col">{{ __('Subtotal') }}</td>
                    @foreach($matrixDays as $di => $day)
                    <td style="font-family: monospace; font-weight: bold;">
                        $ {{ number_format($dayTotals[$di], 0) }}
                    </td>
                    @endforeach
                    <td style="text-align: right; font-family: monospace; font-weight: bold;">
                        $ {{ number_format($grandTotal, 2) }}
                    </td>
                </tr>
                @if($costSheet->markup_percentage > 0)
                <tr>
                    @for($pax = 7; $pax >= 1; $pax--)
                    @php
                        $exactGrandTotal = $groupChart[$pax]['grand_total'] ?? 0;
                        $markupAmount = $exactGrandTotal - $paxTotals[$pax];
                    @endphp
                    <td style="font-family: monospace; color: #4b5563;">$ {{ number_format($markupAmount, 2) }}</td>
                    @endfor
                    <td class="label-col">Markup ({{ number_format($costSheet->markup_percentage, 1) }}%)</td>
                    @foreach($matrixDays as $di => $day)
                    <td></td>
                    @endforeach
                    <td style="text-align: right; font-family: monospace; color: #4b5563;">
                        $ {{ number_format($costSheet->markup_amount, 2) }}
                    </td>
                </tr>
                <tr>
                    @for($pax = 7; $pax >= 1; $pax--)
                    @php
                        $exactGrandTotal = $groupChart[$pax]['grand_total'] ?? 0;
                    @endphp
                    <td style="font-family: monospace; font-weight: bold; background: #d1fae5; color: #065f46;">$ {{ number_format($exactGrandTotal, 2) }}</td>
                    @endfor
                    <td class="label-col" style="font-weight: bold;">{{ __('Grand Total (Group)') }}</td>
                    @foreach($matrixDays as $di => $day)
                    <td></td>
                    @endforeach
                    <td style="text-align: right; font-family: monospace; font-weight: bold; background: #d1fae5; color: #065f46;">
                        $ {{ number_format($costSheet->grand_total, 2) }}
                    </td>
                </tr>
                @endif
                <tr>
                    @for($pax = 7; $pax >= 1; $pax--)
                    @php
                        $exactPerPerson = $groupChart[$pax]['per_person'] ?? ($paxTotals[$pax] / max(1, $pax + $costSheet->num_children));
                    @endphp
                    <td style="font-family: monospace; font-weight: bold; background: #fef3c7; color: #92400e;">$ {{ number_format($exactPerPerson, 2) }}</td>
                    @endfor
                    <td class="label-col" style="font-weight: bold; color: #92400e;">{{ __('Per Person Rate') }}</td>
                    @foreach($matrixDays as $di => $day)
                    <td></td>
                    @endforeach
                    <td style="text-align: right; font-family: monospace; font-weight: bold;">
                    </td>
                </tr>
                @if($numMxDays > 1)
                <tr class="cum-row">
                    @for($pax = 7; $pax >= 1; $pax--)
                    <td></td>
                    @endfor
                    <td class="label-col" style="background: #0f172a; color: white;">{{ __('Cumulative') }}</td>
                    @php $running = 0; @endphp
                    @foreach($matrixDays as $di => $day)
                    @php $running += $dayTotals[$di]; @endphp
                    <td style="font-family: monospace;">
                        $ {{ number_format($running, 0) }}
                    </td>
                    @endforeach
                    <td style="text-align: right; font-family: monospace;">
                        $ {{ number_format($grandTotal, 2) }}
                    </td>
                </tr>
                @endif
            </tfoot>
        </table>
    @endif

    {{-- Grand Total --}}
    <div class="totals-box">
        <div class="totals-row">
            <div class="totals-label">{{ __('Subtotal') }}</div>
            <div class="totals-value">$ {{ number_format($costSheet->subtotal, 2) }}</div>
        </div>
        @if($costSheet->markup_percentage > 0)
        <div class="totals-row">
            <div class="totals-label">Markup ({{ number_format($costSheet->markup_percentage, 1) }}%)</div>
            <div class="totals-value">$ {{ number_format($costSheet->markup_amount, 2) }}</div>
        </div>
        @endif
        <div class="grand-total-row">
            <div class="grand-total-label">{{ __('GRAND TOTAL') }}</div>
            <div class="grand-total-value">$ {{ number_format($costSheet->grand_total, 2) }}</div>
        </div>
        @if($costSheet->num_adults > 0)
        <div class="per-person-row">
            <div class="per-person-label">{{ __('Per adult') }}</div>
            <div class="per-person-value">$ {{ number_format($costSheet->grand_total / $costSheet->num_adults, 2) }}</div>
        </div>
        @if(($costSheet->num_adults + $costSheet->num_children) > 0)
        <div class="per-person-row">
            <div class="per-person-label">{{ __('Per person (incl. children)') }}</div>
            <div class="per-person-value">$ {{ number_format($costSheet->grand_total / ($costSheet->num_adults + $costSheet->num_children), 2) }}</div>
        </div>
        @endif
        @endif
    </div>

    {{-- Notes --}}
    @if($costSheet->notes)<div class="notes-box">
        <strong style="font-size:10px; text-transform:uppercase; color:#6b7280; letter-spacing:0.05em;">{{ __('Internal Notes') }}</strong>
        <p style="margin-top:4px;">{{ $costSheet->notes }}</p>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p>{{ $settings->company_name }} &nbsp;|&nbsp; {{ $settings->email }} &nbsp;|&nbsp; {{ $settings->phone }}</p>
        <p style="margin-top:4px;">{{ __('This is an internal cost sheet for planning purposes only. Prices subject to change.') }}</p>
    </div>

</div>
</body>
</html>
