<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DisplayController extends Controller
{
    // ---------------------------------------------------------------
    // Accès kiosque Raspberry Pi — vérifie le token avant d'afficher
    // ---------------------------------------------------------------

    public function kiosk(string $token)
    {
        abort_unless(
            hash_equals(config('services.display_token'), $token),
            403,
            'Token invalide.'
        );

        return $this->index();
    }

    public function kioskLatest(string $token)
    {
        abort_unless(
            hash_equals(config('services.display_token'), $token),
            403,
            'Token invalide.'
        );

        return $this->latest();
    }

    // ---------------------------------------------------------------

    public function index()
    {
        $leaderboard  = $this->getLeaderboard();
        $latestAppt   = Appointment::with('user')->latest()->first();
        $latestApptId = $latestAppt?->id ?? 0;
        $recentAppts  = $this->getRecentAppts();
        $stats        = $this->getStats();

        return view('display', compact('leaderboard', 'latestApptId', 'recentAppts', 'stats'));
    }

    public function latest()
    {
        $leaderboard = $this->getLeaderboard();
        $latestAppt  = Appointment::with('user')->latest()->first();

        return response()->json([
            'latest_appt_id' => $latestAppt?->id ?? 0,
            'latest_appt'    => $latestAppt ? [
                'id'           => $latestAppt->id,
                'user_name'    => $latestAppt->user->name,
                'title'        => $latestAppt->title,
                'client_name'  => $latestAppt->client_name,
                'level'        => $latestAppt->level,
                'points_value' => $latestAppt->points_value,
            ] : null,
            'leaderboard' => $leaderboard->map(fn($u) => [
                'id'                 => $u->id,
                'name'               => $u->name,
                'points'             => $u->points,
                'appointments_count' => $u->appointments_count,
                'badges'             => $u->badges->map(fn($b) => $b->icon)->values(),
            ]),
            'stats' => $this->getStats(),
        ]);
    }

    private function getLeaderboard()
    {
        return User::where('role', 'commercial')
            ->orderByDesc('points')
            ->withCount('appointments')
            ->with('badges')
            ->get();
    }

    private function getRecentAppts()
    {
        return Appointment::with('user')
            ->latest()
            ->take(20)
            ->get()
            ->map(fn($a) => [
                'id'           => $a->id,
                'client_name'  => $a->client_name,
                'user_name'    => $a->user->name,
                'level'        => $a->level,
                'points_value' => $a->points_value,
                'scheduled_at' => $a->scheduled_at->format('d/m H\hi'),
            ]);
    }

    private function getStats(): array
    {
        $all   = Appointment::selectRaw('level, COUNT(*) as count')->groupBy('level')->pluck('count', 'level');
        $today = Appointment::whereDate('created_at', Carbon::today());
        $week  = Appointment::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);

        $bronze = (int) ($all['bronze'] ?? 0);
        $silver = (int) ($all['silver'] ?? 0);
        $gold   = (int) ($all['gold']   ?? 0);
        $total  = $bronze + $silver + $gold;

        return [
            'bronze'       => $bronze,
            'silver'       => $silver,
            'gold'         => $gold,
            'total'        => $total,
            'bronze_pct'   => $total > 0 ? round($bronze / $total * 100) : 0,
            'silver_pct'   => $total > 0 ? round($silver / $total * 100) : 0,
            'gold_pct'     => $total > 0 ? round($gold   / $total * 100) : 0,
            'today_count'  => $today->count(),
            'today_points' => $today->sum('points_value'),
            'week_count'   => $week->count(),
        ];
    }
}
