<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $invoice->invoice_number }}</title>
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
            position: absolute;
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
        .status-paid { background-color: #d1fae5; color: #065f46; }
        .status-unpaid { background-color: #fee2e2; color: #991b1b; }
        .status-draft { background-color: #f3f4f6; color: #374151; }
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
                        <h1 class="company-name" style="margin-bottom: 5px;">{{ __('OFFICIAL RECEIPT') }}</h1>
                        <p style="margin: 0;">{!! strip_tags($settings->address, '<br>') !!}</p>
                        <p style="margin: 0;">{{ $settings->email }} | {{ $settings->phone }}</p>
                        @if($settings->website)<p style="margin: 0;">{{ $settings->website }}</p>@endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="details-section">
            <table>
                <tr>
                    <td class="bill-to">
                        <p class="label">{{ __('Bill To:') }}</p>
                        <p style="margin: 0; font-weight: bold; font-size: 16px;">{{ $invoice->client?->name ?? 'Unknown Client' }}</p>
                        <p style="margin: 0;">{{ $invoice->client?->email }}</p>
                        <p style="margin: 0;">{{ $invoice->client?->phone }}</p>
                        <p style="margin: 0;">{{ $invoice->client?->address }}</p>
                    </td>
                    <td class="invoice-details">
                        <p style="margin: 5px 0;"><span class="label">{{ __('Invoice Number:') }}</span> #{{ $invoice->invoice_number }}</p>
                        <p style="margin: 5px 0;"><span class="label">{{ __('Date:') }}</span> {{ $invoice->created_date->format('M d, Y') }}</p>
                        <p style="margin: 5px 0;"><span class="label">{{ __('Due Date:') }}</span> {{ $invoice->due_date->format('M d, Y') }}</p>
                        <p style="margin: 10px 0;">
                            <span class="status-badge status-{{ $invoice->status }}">
                                {{ $invoice->status }}
                            </span>
                        </p>
                    </td>
                </tr>
                    </td>
                </tr>
            </table>
            @if($invoice->balance_due <= 0)
            <div style="position: absolute; top: 150px; right: 50px; opacity: 0.1; transform: rotate(-30deg);">
                <span style="font-size: 80px; font-weight: bold; color: #047857; border: 8px solid #047857; padding: 10px 40px; border-radius: 10px;">{{ __('PAID') }}</span>
            </div>
            @endif
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>{{ __('Description') }}</th>
                    <th class="text-right" style="width: 80px;">{{ __('Qty') }}</th>
                    <th class="text-right" style="width: 100px;">{{ __('Price') }}</th>
                    <th class="text-right" style="width: 80px;">{{ __('Tax') }}</th>
                    <th class="text-right" style="width: 120px;">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>
                        {{ $item->item_name }}
                        @if($item->tour_id)
                            <br><span style="font-size: 10px; color: #666;">
                                ({{ __('Adults') }}: {{ $item->adults }} | {{ __('Children') }}: {{ $item->children }})
                            </span>
                        @endif
                    </td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ $invoice->currency_symbol }} {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->tax_total, 2) }}</td>
                    <td class="text-right">{{ $invoice->currency_symbol }} {{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-section">
            <table>
                <tr>
                    <td>{{ __('Subtotal') }}</td>
                    <td class="text-right">{{ $invoice->currency_symbol }} {{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                @if($invoice->tax_total > 0)
                <tr>
                    <td>{{ __('Tax') }}</td>
                    <td class="text-right">{{ $invoice->currency_symbol }} {{ number_format($invoice->tax_total, 2) }}</td>
                </tr>
                @endif
                @if($invoice->discount > 0)
                <tr>
                    <td>{{ __('Discount') }}</td>
                    <td class="text-right">-{{ $invoice->currency_symbol }} {{ number_format($invoice->discount, 2) }}</td>
                </tr>
                @endif
                <tr class="grand-total">
                    <td>{{ __('Grand Total') }}</td>
                    <td class="text-right">{{ $invoice->currency_symbol }} {{ number_format($invoice->total, 2) }}</td>
                </tr>
                @php $amountPaid = $invoice->total - $invoice->balance_due; @endphp
                @if($amountPaid > 0)
                <tr style="color: #047857; font-weight: bold;">
                    <td>{{ __('Amount Paid') }}</td>
                    <td class="text-right">-{{ $invoice->currency_symbol }} {{ number_format($amountPaid, 2) }}</td>
                </tr>
                @endif
                <tr style="color: #991b1b; font-weight: bold;">
                    <td>{{ __('Balance Due') }}</td>
                    <td class="text-right">{{ $invoice->currency_symbol }} {{ number_format($invoice->balance_due, 2) }}</td>
                </tr>
            </table>
        </div>

        @if($invoice->payments && $invoice->payments->count() > 0)
        <div style="margin-top: 30px;">
            <p class="label">{{ __('Payment History') }}</p>
            <table style="width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 20px;">
                <thead>
                    <tr>
                        <th style="border-bottom: 2px solid #f3f4f6; padding: 8px; text-align: left;">{{ __('Date') }}</th>
                        <th style="border-bottom: 2px solid #f3f4f6; padding: 8px; text-align: left;">{{ __('Method') }}</th>
                        <th style="border-bottom: 2px solid #f3f4f6; padding: 8px; text-align: left;">{{ __('Reference') }}</th>
                        <th style="border-bottom: 2px solid #f3f4f6; padding: 8px; text-align: right;">{{ __('Amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->payments as $payment)
                    <tr>
                        <td style="border-bottom: 1px solid #f3f4f6; padding: 8px;">{{ $payment->payment_date->format('M d, Y') }}</td>
                        <td style="border-bottom: 1px solid #f3f4f6; padding: 8px; text-transform: capitalize;">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                        <td style="border-bottom: 1px solid #f3f4f6; padding: 8px;">{{ $payment->reference_number ?: '-' }}</td>
                        <td style="border-bottom: 1px solid #f3f4f6; padding: 8px; text-align: right; color: #047857; font-weight: bold;">{{ $invoice->currency_symbol }} {{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif



        <div class="footer">
            <p>{{ $settings->invoice_footer ?? 'Thank you for your business!' }}</p>
            @if($settings->bank_name)
                <p style="margin-top: 10px;">
                    Bank: {{ $settings->bank_name }} | 
                    Account Name: {{ $settings->bank_account_name }} | 
                    Account Number: {{ $settings->bank_account_number }}
                </p>
            @endif
        </div>
    </div>
</body>
</html>
