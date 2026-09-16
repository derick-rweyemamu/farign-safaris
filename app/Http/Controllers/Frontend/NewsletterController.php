<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        // Honeypot check
        if (!empty($request->website_url)) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for subscribing to our newsletter!'
            ]);
        }

        $request->validate([
            'email' => 'required|email:rfc,dns|max:255',
        ]);

        $subscriber = NewsletterSubscriber::where('email', $request->email)->first();

        if ($subscriber) {
            if ($subscriber->status === 'unsubscribed') {
                $subscriber->update(['status' => 'active']);
                return response()->json([
                    'success' => true,
                    'message' => 'Welcome back! You have been re-subscribed to our newsletter.'
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'This email is already subscribed. Please use a different email address.'
            ]);
        }

        NewsletterSubscriber::create([
            'email' => $request->email,
            'status' => 'active',
            'unsubscribe_token' => bin2hex(random_bytes(32))
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for subscribing to our newsletter!'
        ]);
    }

    public function unsubscribe($token)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->firstOrFail();
        $subscriber->update(['status' => 'unsubscribed']);

        return view('frontend.newsletter.unsubscribed');
    }
}
