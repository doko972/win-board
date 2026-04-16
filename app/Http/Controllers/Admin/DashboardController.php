<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users_total'       => User::where('role', 'commercial')->count(),
            'appts_total'       => Appointment::count(),
            'appts_today'       => Appointment::whereDate('created_at', Carbon::today())->count(),
            'appts_week'        => Appointment::whereBetween('created_at', [
                                    Carbon::now()->startOfWeek(),
                                    Carbon::now()->endOfWeek(),
                                  ])->count(),
            'points_distributed' => User::where('role', 'commercial')->sum('points'),
        ];

        $topUser       = User::where('role', 'commercial')->orderByDesc('points')->first();
        $recentAppts   = Appointment::with('user')->latest()->take(8)->get();

        return view('admin.dashboard', compact('stats', 'topUser', 'recentAppts'));
    }
}
