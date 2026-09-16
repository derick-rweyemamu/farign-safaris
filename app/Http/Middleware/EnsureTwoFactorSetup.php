<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Settings\GeneralSetting;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

class EnsureTwoFactorSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip if not authenticated
        if (!auth()->check()) {
            return $next($request);
        }

        $allowedRoutes = [
            'settings.profile',
            'settings.company',
            'logout',
            'login',
            '2fa.challenge'
        ];

        // Allow livewire internal routes and allowed named routes
        $routeName = $request->route() ? $request->route()->getName() : '';
        if (in_array($routeName, $allowedRoutes) || str_contains((string) $routeName, 'livewire')) {
            return $next($request);
        }

        $settings = app(GeneralSetting::class);

        // If force_2fa is enabled and user hasn't set it up
        if ($settings->force_2fa && !Auth::user()->two_factor_confirmed_at) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            session()->flash('status', 'The system requires Two-Factor Authentication. Please log in again to set it up.');
            return redirect()->route('login');
        }

        return $next($request);
    }
}
