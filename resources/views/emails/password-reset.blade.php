<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Password Reset') }}</title>
    <style>
        body {
            font-family: 'Merriweather', serif;
            line-height: 1.6;
            color: #1f2937;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 3px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.025em;
        }
        .content {
            padding: 40px;
        }
        .content h2 {
            margin-top: 0;
            font-size: 20px;
            font-weight: 700;
        }
        .password-box {
            background-color: #f3f4f6;
            border: 2px dashed #e5e7eb;
            border-radius: 3px;
            padding: 20px;
            text-align: center;
            margin: 24px 0;
        }
        .password-text {
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 24px;
            font-weight: 700;
            color: #4f46e5;
            letter-spacing: 2px;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #f3f4f6;
        }
        .btn {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff !important;
            padding: 12px 24px;
            border-radius: 3px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="content">
            <h2>Hello, {{ $name }}!</h2>
            <p>{{ __('Your account password has been reset by an administrator. You can now use the temporary password below to log in to your account.') }}</p>
            
            <div class="password-box">
                <div class="password-text">{{ $password }}</div>
            </div>

            <p>{{ __('For security reasons, we recommend that you change this password immediately after logging in.') }}</p>
            
            <a href="{{ url('/login') }}" class="btn">{{ __('Log In Now') }}</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}</p>
            <p>{{ __('If you did not expect this change, please contact support immediately.') }}</p>
        </div>
    </div>
</body>
</html>
