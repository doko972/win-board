<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointReset extends Model
{
    protected $fillable = ['label', 'reset_by', 'snapshot', 'badges_reset'];

    protected function casts(): array
    {
        return ['snapshot' => 'array', 'badges_reset' => 'boolean'];
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'reset_by');
    }
}
