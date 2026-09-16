<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Estimate {{ $estimate->estimate_number }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Merriweather', serif;
            font-size: 14px;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            position: relative;
        }

        .container {
            padding: 30px;
        }

        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 20px;
        }

        .header table {
            width: 100%;
        }

        .logo {
            max-width: 150px;
            height: auto;
        }

        .company-info {
            text-align: right;
        }

        .company-name {
            font-size: 20px;
            font-bold: bold;
            color: #4f46e5;
            margin: 0;
        }

        .details-section {
            margin-bottom: 30px;
        }

        .details-section table {
            width: 100%;
        }

        .bill-to {
            width: 50%;
        }

        .invoice-details {
            width: 50%;
            text-align: right;
        }

        .label {
            color: #6b7280;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background-color: #f9fafb;
            border-bottom: 2px solid #f3f4f6;
            padding: 12px;
            text-align: left;
            font-size: 12px;
            color: #374151;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
        }

        .items-table .text-right {
            text-align: right;
        }

        .totals-section {
            width: 40%;
            margin-left: 60%;
        }

        .totals-section table {
            width: 100%;
        }

        .totals-section td {
            padding: 8px 0;
        }

        .grand-total td {
            border-top: 2px solid #4f46e5;
            border-bottom: 3px solid #4f46e5;
            font-weight: bold;
            font-size: 18px;
            color: #4f46e5;
        }

        .footer {
            position: relative;
            bottom: 30px;
            left: 30px;
            right: 30px;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
            border-top: 1px solid #f3f4f6;
            padding-top: 20px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-accepted {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-draft {
            background-color: #f3f4f6;
            color: #374151;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <table>
                <tr>
                    <td>
                        @if($settings->company_logo)
                            <img src="{{ public_path('storage/' . $settings->company_logo) }}" class="logo">
                        @else
                            <h1 class="company-name">{{ $settings->company_name }}</h1>
                        @endif
                    </td>
                    <td class="company-info">
                        <h1 class="company-name" style="margin-bottom: 5px;">{{ __('ESTIMATE') }}</h1>
                        <p style="margin: 0;">{!! strip_tags($settings->address, '<br>') !!}</p>
                        <p style="margin: 0;">{{ $settings->email }} | {{ $settings->phone }}</p>
                        @if($settings->website)
                        <p style="margin: 0;">{{ $settings->website }}</p>@endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="details-section">
            <table>
                <tr>
                    <td class="bill-to">
                        @if($estimate->client)
                            <p class="label">{{ __('Client Information:') }}</p>
                            <p style="margin: 0; font-weight: bold; font-size: 16px;">{{ $estimate->client->name }}</p>
                            <p style="margin: 0;">{{ $estimate->client->email }}</p>
                            <p style="margin: 0;">{{ $estimate->client->phone }}</p>
                            <p style="margin: 0;">{{ $estimate->client->address ?? $estimate->client->address_line1 ?? '' }}
                            </p>
                        @elseif($estimate->lead)
                            <p class="label">{{ __('Lead Information:') }}</p>
                            <p style="margin: 0; font-weight: bold; font-size: 16px;">{{ $estimate->lead->name }}</p>
                            <p style="margin: 0;">{{ $estimate->lead->email }}</p>
                            <p style="margin: 0;">{{ $estimate->lead->phone }}</p>
                        @endif
                    </td>
                    <td class="invoice-details">
                        <p style="margin: 5px 0;"><span class="label">{{ __('Estimate Number:') }}</span>
                            #{{ $estimate->estimate_number }}</p>
                        <p style="margin: 5px 0;"><span class="label">{{ __('Date:') }}</span>
                            {{ $estimate->date->format('M d, Y') }}
                        </p>
                        <p style="margin: 5px 0;"><span class="label">{{ __('Expiry Date:') }}</span>
                            {{ $estimate->expiry_date ? $estimate->expiry_date->format('M d, Y') : '—' }}
                        </p>
                        <p style="margin: 10px 0;">
                            <span class="status-badge status-{{ $estimate->status }}">
                                {{ $estimate->status }}
                            </span>
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>{{ __('Description') }}</th>
                    <th class="text-right" style="width: 80px;">{{ __('Qty') }}</th>
                    <th class="text-right" style="width: 100px;">{{ __('Price') }}</th>
                    <th class="text-right" style="width: 120px;">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($estimate->items as $item)
                    <tr>
                        <td>{{ $item->item_name }}</td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">{{ $estimate->currency_symbol }} {{ number_format($item->unit_price, 2) }}
                        </td>
                        <td class="text-right">{{ $estimate->currency_symbol }} {{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-section">
            <table>
                <tr>
                    <td>{{ __('Subtotal') }}</td>
                    <td class="text-right">{{ $estimate->currency_symbol }} {{ number_format($estimate->subtotal, 2) }}
                    </td>
                </tr>
                @if($estimate->tax > 0)
                    <tr>
                        <td>{{ __('Tax') }}</td>
                        <td class="text-right">{{ $estimate->currency_symbol }} {{ number_format($estimate->tax, 2) }}</td>
                    </tr>
                @endif
                @if($estimate->discount > 0)
                    <tr>
                        <td>{{ __('Discount') }}</td>
                        <td class="text-right">-{{ $estimate->currency_symbol }} {{ number_format($estimate->discount, 2) }}
                        </td>
                    </tr>
                @endif
                <tr class="grand-total">
                    <td>{{ __('Grand Total') }}</td>
                    <td class="text-right">{{ $estimate->currency_symbol }} {{ number_format($estimate->total, 2) }}
                    </td>
                </tr>
            </table>
        </div>

        @php
            $finalNotes = $estimate->notes ?: $settings->estimate_note;
            $finalTerms = $estimate->terms ?: $settings->estimate_terms;
        @endphp
        @if($finalNotes || $finalTerms)
            <div style="margin-top: 20px; page-break-inside: avoid; margin-bottom: 10px;">
                @if($finalNotes)
                    <p class="label">{{ __('Notes:') }}</p>
                    <div style="font-size: 12px; margin-bottom: 10px;">{!! $finalNotes !!}</div>
                @endif
                @if($finalTerms)
                    <p class="label">{{ __('Terms & Conditions:') }}</p>
                    <div style="font-size: 11px;">{!! $finalTerms !!}</div>
                @endif
            </div>
        @endif

        <div class="footer">
            <p>{{ $settings->estimate_footer ?? 'Looking forward to working with you!' }}</p>
        </div>
    </div>
</body>

</html>