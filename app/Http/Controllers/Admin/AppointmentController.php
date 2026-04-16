<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    private function buildQuery(Request $request)
    {
        $query = Appointment::with('user')->latest();

        if ($request->filled('user_id'))  $query->where('user_id', $request->user_id);
        if ($request->filled('level'))    $query->where('level', $request->level);
        if ($request->filled('date_from')) $query->whereDate('scheduled_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('scheduled_at', '<=', $request->date_to);

        return $query;
    }

    public function index(Request $request)
    {
        $users        = User::where('role', 'commercial')->orderBy('name')->get();
        $appointments = $this->buildQuery($request)->paginate(20)->withQueryString();

        return view('admin.appointments.index', compact('appointments', 'users'));
    }

    public function export(Request $request)
    {
        $appointments = $this->buildQuery($request)->get();

        $levelLabels = ['gold' => 'Confirmé', 'silver' => 'Qualifié', 'bronze' => 'Découverte'];

        $filename = 'rdv-export-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($appointments, $levelLabels) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 pour Excel
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Commercial', 'Titre', 'Client', 'Niveau', 'Points', 'Date RDV', 'Déclaré le'], ';');

            foreach ($appointments as $appt) {
                fputcsv($handle, [
                    $appt->user->name,
                    $appt->title,
                    $appt->client_name,
                    $levelLabels[$appt->level] ?? $appt->level,
                    $appt->points_value,
                    $appt->scheduled_at->format('d/m/Y'),
                    $appt->created_at->format('d/m/Y H:i'),
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function edit(Appointment $appointment)
    {
        $users = User::where('role', 'commercial')->orderBy('name')->get();

        return view('admin.appointments.edit', compact('appointment', 'users'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'user_id'      => 'required|exists:users,id',
            'title'        => 'required|string|max:255',
            'client_name'  => 'required|string|max:255',
            'level'        => 'required|in:bronze,silver,gold',
            'scheduled_at' => 'required|date',
        ]);

        $newPoints = Appointment::pointsForLevel($validated['level']);
        $oldPoints = $appointment->points_value;
        $oldUserId = $appointment->user_id;
        $newUserId = (int) $validated['user_id'];

        if ($oldUserId !== $newUserId) {
            User::find($oldUserId)?->decrement('points', $oldPoints);
            User::find($newUserId)?->increment('points', $newPoints);
        } elseif ($newPoints !== $oldPoints) {
            $appointment->user->increment('points', $newPoints - $oldPoints);
        }

        $appointment->update([
            'user_id'      => $newUserId,
            'title'        => $validated['title'],
            'client_name'  => $validated['client_name'],
            'level'        => $validated['level'],
            'points_value' => $newPoints,
            'scheduled_at' => $validated['scheduled_at'],
        ]);

        return redirect()->route('admin.appointments.index')
            ->with('success', 'Rendez-vous mis à jour.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->user->decrement('points', $appointment->points_value);
        $appointment->delete();

        return redirect()->route('admin.appointments.index')
            ->with('success', 'Rendez-vous supprimé et points restitués.');
    }
}
