<table>
    <thead>
        <tr>
            <th colspan="5" style="font-size: 14px; font-weight: bold;">Cost Sheet: {{ $costSheet->title }}</th>
        </tr>
        <tr>
            <th>{{ __('Reference Number') }}</th>
            <th colspan="4">{{ $costSheet->reference_number }}</th>
        </tr>
        <tr>
            <th>{{ __('Destination') }}</th>
            <th colspan="4">{{ $costSheet->destination?->name }}</th>
        </tr>
        <tr>
            <th>{{ __('Guests') }}</th>
            <th colspan="4">{{ $costSheet->num_adults }} Adults, {{ $costSheet->num_children }} Children</th>
        </tr>
        <tr>
            <th>{{ __('Duration') }}</th>
            <th colspan="4">{{ $costSheet->duration_days }} Days</th>
        </tr>
        <tr>
            <th>{{ __('Season') }}</th>
            <th colspan="4">{{ ucfirst($costSheet->season) }}</th>
        </tr>
        <tr>
            <th>{{ __('Status') }}</th>
            <th colspan="4">{{ ucfirst($costSheet->status) }}</th>
        </tr>
        <tr>
            <th colspan="5"></th>
        </tr>
    </thead>
    <tbody>
        @if(($costSheet->calculation_type ?? 'standard') === 'standard')
            <tr>
                <th style="font-weight: bold; background-color: #f3f4f6;">{{ __('Category') }}</th>
                <th style="font-weight: bold; background-color: #f3f4f6;">{{ __('Description') }}</th>
                <th style="font-weight: bold; background-color: #f3f4f6;">{{ __('Unit Cost') }}</th>
                <th style="font-weight: bold; background-color: #f3f4f6;">{{ __('Quantity') }}</th>
                <th style="font-weight: bold; background-color: #f3f4f6;">{{ __('Line Total') }}</th>
            </tr>
            @foreach($costSheet->items as $item)
                <tr>
                    <td>{{ $item->item_type }}</td>
                    <td>{{ $item->name }} {{ $item->rate_label ? '- ' . $item->rate_label : '' }}</td>
                    <td>{{ $item->unit_cost }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->line_total }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold;">{{ __('Subtotal') }}</td>
                <td style="font-weight: bold;">{{ $costSheet->subtotal }}</td>
            </tr>
            @if($costSheet->markup_percentage > 0)
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold;">Markup ({{ $costSheet->markup_percentage }}%)</td>
                <td style="font-weight: bold;">{{ $costSheet->markup_amount }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold; color: #047857;">{{ __('Grand Total') }}</td>
                <td style="font-weight: bold; color: #047857;">{{ $costSheet->grand_total }}</td>
            </tr>
        @else
            <tr>
                <th style="font-weight: bold; background-color: #f3f4f6;">{{ __('Category') }}</th>
                <th style="font-weight: bold; background-color: #f3f4f6;">{{ __('Description') }}</th>
                @foreach($costSheet->matrix_days ?: [] as $di => $day)
                    <th style="font-weight: bold; background-color: #f3f4f6;">{{ $day['label'] ?? 'Day '.($di+1) }}</th>
                @endforeach
                <th style="font-weight: bold; background-color: #f3f4f6;">{{ __('Row Total') }}</th>
            </tr>
            @foreach($costSheet->matrix_rows ?: [] as $ri => $row)
                <tr>
                    <td>{{ $row['type'] ?? 'custom' }}</td>
                    <td>{{ $row['label'] ?? 'Custom' }}</td>
                    @php $rowBaseTotal = 0; @endphp
                    @foreach($costSheet->matrix_days ?: [] as $di => $day)
                        @php $amt = (float)($row['amounts'][$di] ?? 0); $rowBaseTotal += $amt; @endphp
                        <td>{{ $amt }}</td>
                    @endforeach
                    <td>{{ $rowBaseTotal }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="{{ 2 + count($costSheet->matrix_days ?: []) }}" style="text-align: right; font-weight: bold;">{{ __('Subtotal') }}</td>
                <td style="font-weight: bold;">{{ $costSheet->subtotal }}</td>
            </tr>
            @if($costSheet->markup_percentage > 0)
            <tr>
                <td colspan="{{ 2 + count($costSheet->matrix_days ?: []) }}" style="text-align: right; font-weight: bold;">Markup ({{ $costSheet->markup_percentage }}%)</td>
                <td style="font-weight: bold;">{{ $costSheet->markup_amount }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="{{ 2 + count($costSheet->matrix_days ?: []) }}" style="text-align: right; font-weight: bold; color: #047857;">{{ __('Grand Total') }}</td>
                <td style="font-weight: bold; color: #047857;">{{ $costSheet->grand_total }}</td>
            </tr>
        @endif
    </tbody>
</table>
