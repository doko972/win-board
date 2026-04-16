<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointReset;
use App\Models\User;
use Illuminate\Http\Request;

class ResetController extends Controller
{
    public function index()
    {
        $resets    = PointReset::with('admin')->latest()->get();
        $userCount = User::where('role', 'commercial')->count();

        return view('admin.resets.index', compact('resets', 'userCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label'        => 'required|string|max:100',
            'reset_badges' => 'nullable|boolean',
        ]);

        $commerciaux = User::where('role', 'commercial')
            ->withCount('appointments')
            ->orderByDesc('points')
            ->get();

        // Snapshot du classement avant reset
        $snapshot = $commerciaux->values()->map(fn ($u, $i) => [
            'rank'               => $i + 1,
            'user_id'            => $u->id,
            'name'               => $u->name,
            'points'             => $u->points,
            'appointments_count' => $u->appointments_count,
        ])->toArray();

        $resetBadges = !empty($validated['reset_badges']);

        PointReset::create([
            'label'        => $validated['label'],
            'reset_by'     => auth()->id(),
            'snapshot'     => $snapshot,
            'badges_reset' => $resetBadges,
        ]);

        // Remise à zéro des points
        User::where('role', 'commercial')->update(['points' => 0]);

        // Suppression des badges si demandé
        if ($resetBadges) {
            foreach ($commerciaux as $user) {
                $user->badges()->detach();
            }
        }

        return redirect()->route('admin.resets.index')
            ->with('success', "Remise à zéro effectuée — période « {$validated['label']} » archivée.");
    }
}
