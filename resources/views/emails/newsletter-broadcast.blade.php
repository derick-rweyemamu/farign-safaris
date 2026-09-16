<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 40px;
            margin-bottom: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .header {
            background-color: #111827;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .content {
            padding: 40px 30px;
            color: #374151;
            line-height: 1.6;
            font-size: 16px;
        }

        .footer {
            background-color: #2E1F12;
            padding: 40px 30px;
            text-align: center;
            color: #FAF6EF;
        }

        .footer p {
            color: #EEE5D4;
            font-size: 13px;
            margin: 5px 0;
            line-height: 1.5;
        }

        .footer a {
            color: #EC9824;
            text-decoration: underline;
        }

        .social-links {
            margin: 20px 0 30px;
        }

        .social-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            text-decoration: underline;
            color: #EC9824;
        }

        .social-icon {
            display: inline-block;
            width: 36px;
            height: 36px;
            border: 1px solid #ffffff;
            border-radius: 50%;
            margin: 0 5px;
            text-align: center;
            line-height: 36px;
            text-decoration: none !important;
            color: #ffffff !important;
        }

        .company-info {
            margin-bottom: 25px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ app(\App\Settings\GeneralSetting::class)->company_name ?? config('app.name') }}</h1>
        </div>

        <div class="content">
            @php
                $baseUrl = rtrim(config('app.url'), '/');
                $body = preg_replace('/src="(?:\.\.\/)*\/?storage\//i', 'src="' . $baseUrl . '/storage/', $campaign->body);
            @endphp
            {!! $body !!}
        </div>

        @php
            $settings = app(\App\Settings\GeneralSetting::class);
        @endphp
        <div class="footer">
            <div class="social-title">Follow Us</div>
            <div class="social-links">
                @if($settings->facebook_url)
                    <a href="{{ $settings->facebook_url }}" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="Facebook">
                        <img src="https://img.icons8.com/ios-filled/50/ffffff/facebook-new.png" alt="Facebook" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin-top: -2px; border: 0; outline: none; text-decoration: none;">
                    </a>
                @endif
                @if($settings->x_url)
                    <a href="{{ $settings->x_url }}" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="X">
                        <img src="https://img.icons8.com/ios-filled/50/ffffff/twitterx--v1.png" alt="X" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin-top: -2px; border: 0; outline: none; text-decoration: none;">
                    </a>
                @endif
                @if($settings->instagram_url)
                    <a href="{{ $settings->instagram_url }}" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="Instagram">
                        <img src="https://img.icons8.com/ios-filled/50/ffffff/instagram-new--v1.png" alt="Instagram" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin-top: -2px; border: 0; outline: none; text-decoration: none;">
                    </a>
                @endif
                @if($settings->youtube_url)
                    <a href="{{ $settings->youtube_url }}" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="YouTube">
                        <img src="https://img.icons8.com/ios-filled/50/ffffff/youtube-play.png" alt="YouTube" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin-top: -2px; border: 0; outline: none; text-decoration: none;">
                    </a>
                @endif
            </div>

            <div class="company-info">
                <p>{{ $settings->company_name ?? config('app.name') }} All Rights Reserved</p>
                <p>{!! strip_tags($settings->address) !!} | {{ $settings->email }}<br>
                    <a href="{{ $settings->website }}">{{ $settings->website }}</a>
                </p>
            </div>

            <p>
                Don't want to receive this email? <a
                    href="{{ route('newsletter.unsubscribe', ['token' => $subscriber->unsubscribe_token]) }}">Unsubscribe
                    here</a>
            </p>
        </div>
    </div>
</body>

</html>