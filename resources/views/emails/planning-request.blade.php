<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('New Planning Request') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            border-bottom: 2px solid #d97706;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            color: #d97706;
        }
        .field {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 5px;
        }
        .value {
            font-size: 16px;
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #eee;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #888;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ __('New Safari Planning Request') }}</h2>
        </div>
        
        <p>{{ __('A new request has been submitted via the website\'s "Start Planning" form. Here are the details:') }}</p>

        <div class="field">
            <span class="label">{{ __('Email Address:') }}</span>
            <div class="value">{{ $data['email'] ?? __('N/A') }}</div>
        </div>

        <div class="field">
            <span class="label">{{ __('Arrival Date:') }}</span>
            <div class="value">{{ $data['arrival_date'] ?? __('N/A') }}</div>
        </div>

        <div class="field">
            <span class="label">{{ __('Trip Type:') }}</span>
            <div class="value">{{ $data['trip_type'] ?? __('N/A') }}</div>
        </div>

        <div class="field">
            <span class="label">{{ __('Adults:') }}</span>
            <div class="value">{{ $data['adults'] ?? __('N/A') }}</div>
        </div>

        <div class="field">
            <span class="label">{{ __('Children:') }}</span>
            <div class="value">{{ $data['children'] ?? __('N/A') }}</div>
        </div>
        
        <div class="footer">
            <p>{{ __('This email was automatically generated from your website.') }}</p>
        </div>
    </div>
</body>
</html>
