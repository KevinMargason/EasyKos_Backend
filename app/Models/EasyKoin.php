<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EasyKoin extends Model
{
    protected $table = 'dompet_koin';
    protected $fillable = ['total_koin', 'total_dapat', 'total_pakai', 'users_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
