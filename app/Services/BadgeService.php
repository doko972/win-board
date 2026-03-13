<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;

class BadgeService
{
    public function checkAndAward(User $user): void
    {
        $alreadyObtained = $user->badges()->pluck('badges.id')->toArray();
        $badges = Badge::whereNotIn('id', $alreadyObtained)->get();

        foreach ($badges as $badge) {
            if ($this->meetsCondition($user, $badge)) {
                $user->badges()->attach($badge->id, [
                    'obtained_at' => now(),
                ]);
            }
        }
    }

    private function meetsCondition(User $user, Badge $badge): bool
    {
        return match($badge->condition_type) {
            'appointments_count' => $user->appointments()->count() >= $badge->condition_value,
            'gold_count'         => $user->appointments()->where('level', 'gold')->count() >= $badge->condition_value,
            'points_total'       => $user->points >= $badge->condition_value,
            default              => false,
        };
    }
}
