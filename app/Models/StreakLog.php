<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StreakLog extends Model
{
    protected $table = 'streak_log';

    protected $fillable = [
        'jenis',
        'streak_sekarang',
        'top_streak',
        'users_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
