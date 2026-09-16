<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Settings\GeneralSetting;
use Illuminate\Support\Facades\Mail;
use App\Mail\PlanningRequestMail;
use App\Models\Lead;
use App\Enums\LeadStatus;

class PlanningController extends Controller
{
    public function submit(Request $request, GeneralSetting $settings)
    {
        $validated = $request->validate([
            'adults'       => 'required|integer|min:1|max:50',
            'children'     => 'required|integer|min:0|max:50',
            'arrival_date' => 'required|date|after:today',
            'trip_type'    => 'required|string|exists:tour_types,name|max:100',
            'email'        => 'required|email:rfc|max:255',
        ]);

        $companyEmail = $settings->email;

        if ($companyEmail) {
            try {
                Mail::to($companyEmail)->send(new PlanningRequestMail($validated));
            } catch (\Exception $e) {
                \Log::error('Failed to send planning request email: ' . $e->getMessage());
            }
        } else {
            \Log::warning('No company email set in GeneralSetting for PlanningRequestMail');
        }

        try {
            $name = explode('@', $validated['email'])[0];
            
            Lead::create([
                'name' => 'Planning Request: ' . ucfirst($name),
                'email' => $validated['email'],
                'source' => 'Website Planning Form',
                'status' => LeadStatus::NEW,
                'travel_date' => $validated['arrival_date'],
                'notes' => "Trip Type: {$validated['trip_type']}\nAdults: {$validated['adults']}\nChildren: {$validated['children']}",
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create lead from planning form: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Your request has been sent successfully! We will get back to you shortly.'
        ]);
    }
}
