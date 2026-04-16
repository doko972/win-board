<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    public function index()
    {
        $badges      = Badge::with('users')->get();
        $commerciaux = User::where('role', 'commercial')->orderBy('name')->get();

        return view('admin.badges.index', compact('badges', 'commerciaux'));
    }

    public function award(Request $request)
    {
        $validated = $request->validate([
            'user_id'  => 'required|exists:users,id',
            'badge_id' => 'required|exists:badges,id',
        ]);

        $user  = User::findOrFail($validated['user_id']);
        $badge = Badge::findOrFail($validated['badge_id']);

        if ($user->badges()->where('badge_id', $badge->id)->exists()) {
            return back()->with('error', "« {$user->name} » possède déjà ce badge.");
        }

        $user->badges()->attach($badge->id, ['obtained_at' => now()]);

        return back()->with('success', "Badge {$badge->icon} {$badge->name} attribué à {$user->name}.");
    }

    public function revoke(Request $request)
    {
        $validated = $request->validate([
            'user_id'  => 'required|exists:users,id',
            'badge_id' => 'required|exists:badges,id',
        ]);

        User::findOrFail($validated['user_id'])
            ->badges()->detach($validated['badge_id']);

        return back()->with('success', 'Badge retiré.');
    }
}
