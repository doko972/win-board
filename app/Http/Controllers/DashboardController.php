<?php

namespace App\Http\Controllers;

use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $leaderboard = User::where('role', 'commercial')
            ->orderByDesc('points')
            ->withCount('appointments')
            ->with('badges')
            ->get();

        $myAppointments = auth()->user()->appointments()
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('dashboard', compact('leaderboard', 'myAppointments'));
    }
}
