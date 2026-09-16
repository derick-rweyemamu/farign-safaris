<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payroll Report</title>
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
            font-weight: bold;
            color: #4f46e5;
            margin: 0;
        }
        .details-section {
            margin-bottom: 30px;
            text-align: center;
        }
        .details-section h2 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }
        .details-section p {
            color: #6b7280;
            margin: 5px 0 0 0;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .report-table th, .report-table td {
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
        }
        .report-table th {
            background-color: #f9fafb;
            text-align: left;
            font-size: 12px;
            color: #374151;
            text-transform: uppercase;
        }
        .text-right {
            text-align: right;
        }
        .section-header {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .grand-total td {
            border-top: 2px solid #4f46e5;
            border-bottom: 3px solid #4f46e5;
            font-weight: bold;
            font-size: 18px;
        }
        .profit {
            color: #047857;
        }
        .loss {
            color: #dc2626;
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
                        <p style="margin: 0; font-weight: bold; font-size: 16px;">{{ $settings->company_name }}</p>
                        <p style="margin: 0;">{!! strip_tags($settings->address, '<br>') !!}</p>
                        <p style="margin: 0;">{{ $settings->email }} | {{ $settings->phone }}</p>
                        @if($settings->website)<p style="margin: 0;">{{ $settings->website }}</p>@endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="details-section">
            <h2>{{ __('Payroll Report') }}</h2>
            <p>{{ __('For the period') }} {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} {{ __('to') }} {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
        </div>

        @php
            $currency = app(\App\Settings\GeneralSetting::class)->currency_default ?? 'USD';
            $currencyObj = \App\Models\Currency::where('code', $currency)->first();
            $symbol = $currencyObj ? $currencyObj->symbol : '$';
        @endphp

        <table class="report-table">
            <thead>
                <tr>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Employee') }}</th>
                    <th>{{ __('Period') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th class="text-right">{{ __('Net Salary') }}</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach($payrolls as $payroll)
                @php 
                    $period = date("M", mktime(0, 0, 0, $payroll->month, 10)) . ' ' . $payroll->year; 
                @endphp
                <tr>
                    <td>{{ $payroll->payment_date ? $payroll->payment_date->format('M d, Y') : '-' }}</td>
                    <td>{{ $payroll->employee ? $payroll->employee->first_name . ' ' . $payroll->employee->last_name : '-' }}</td>
                    <td>{{ $period }}</td>
                    <td>{{ ucfirst($payroll->status) }}</td>
                    <td class="text-right">{{ $symbol }} {{ number_format($payroll->net_salary, 2) }}</td>
                </tr>
                @php $total += $payroll->net_salary; @endphp
                @endforeach
                
                <tr class="grand-total">
                    <td colspan="4">{{ __('Total Payroll') }}</td>
                    <td class="text-right" style="color: #dc2626;">{{ $symbol }} {{ number_format($total, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <p>{{ __('Generated on') }} {{ now()->format('M d, Y h:i A') }}</p>
        </div>
    </div>
</body>
</html>
