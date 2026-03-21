<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyLoginLog extends Model
{
    protected $table = 'daily_login_log';

    protected $fillable = [
        'login_date',
        'koin_didapat',
        'is_streak_bonus',
        'users_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
