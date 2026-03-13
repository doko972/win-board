<?php

namespace App\Http\Controllers;

use App\Events\AppointmentDeclared;
use App\Models\Appointment;
use App\Services\BadgeService;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function create()
    {
        return view('appointments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'client_name'  => 'required|string|max:255',
            'description'  => 'nullable|string|max:1000',
            'level'        => 'required|in:bronze,silver,gold',
            'scheduled_at' => 'required|date|after:now',
        ]);

        $points = Appointment::pointsForLevel($validated['level']);

        $appointment = Auth::user()->appointments()->create([
            'title'        => $validated['title'],
            'client_name'  => $validated['client_name'],
            'description'  => $validated['description'] ?? null,
            'level'        => $validated['level'],
            'points_value' => $points,
            'scheduled_at' => $validated['scheduled_at'],
        ]);

        Auth::user()->increment('points', $points);

        app(BadgeService::class)->checkAndAward(Auth::user());

        // Créer l'événement dans Google Calendar
        $calendarDescription = "👤 Commercial : " . Auth::user()->name
            . "\n🏆 Niveau : " . ucfirst($validated['level']) . " (+" . $points . " pts)";

        if (!empty($validated['description'])) {
            $calendarDescription .= "\n\n📝 " . $validated['description'];
        }

        $googleEventId = app(GoogleCalendarService::class)->createEvent(
            title:           $validated['title'] . " — " . $validated['client_name'],
            description:     $calendarDescription,
            start:           new \DateTime($validated['scheduled_at']),
            durationMinutes: 60,
            level:           $validated['level'],
        );

        if ($googleEventId) {
            $appointment->update(['google_event_id' => $googleEventId]);
        }

        $appointment->load('user');
        broadcast(new AppointmentDeclared($appointment));

        return redirect()->route('dashboard')
            ->with('success', "RDV déclaré ! +" . $points . " points 🎉");
    }
}
