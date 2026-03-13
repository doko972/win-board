<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'client_name',
        'description',
        'level',
        'points_value',
        'scheduled_at',
        'google_event_id',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function pointsForLevel(string $level): int
    {
        return match($level) {
            'silver' => 20,
            'gold'   => 30,
            default  => 10, // bronze
        };
    }
}
