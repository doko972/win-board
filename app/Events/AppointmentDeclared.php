<?php

namespace App\Events;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentDeclared implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $appointment;
    public array $leaderboard;
    public array $stats;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = [
            'id'           => $appointment->id,
            'user_name'    => $appointment->user->name,
            'title'        => $appointment->title,
            'client_name'  => $appointment->client_name,
            'level'        => $appointment->level,
            'points_value' => $appointment->points_value,
        ];

        $this->leaderboard = User::where('role', 'commercial')
            ->orderByDesc('points')
            ->withCount('appointments')
            ->with('badges')
            ->get()
            ->map(fn($u) => [
                'id'                 => $u->id,
                'name'               => $u->name,
                'points'             => $u->points,
                'appointments_count' => $u->appointments_count,
                'badges'             => $u->badges->map(fn($b) => $b->icon)->values()->toArray(),
            ])->toArray();

        $all   = Appointment::selectRaw('level, COUNT(*) as count')->groupBy('level')->pluck('count', 'level');
        $today = Appointment::whereDate('created_at', Carbon::today());
        $week  = Appointment::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);

        $bronze = (int) ($all['bronze'] ?? 0);
        $silver = (int) ($all['silver'] ?? 0);
        $gold   = (int) ($all['gold']   ?? 0);
        $total  = $bronze + $silver + $gold;

        $this->stats = [
            'bronze'       => $bronze,
            'silver'       => $silver,
            'gold'         => $gold,
            'gold_pct'     => $total > 0 ? round($gold / $total * 100) : 0,
            'today_count'  => $today->count(),
            'today_points' => $today->sum('points_value'),
            'week_count'   => $week->count(),
        ];
    }

    public function broadcastOn(): array
    {
        return [new Channel('winboard')];
    }

    public function broadcastAs(): string
    {
        return 'appointment.declared';
    }
}
