<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyTupai extends Model
{
    protected $table = 'myTupai';

    protected $fillable = [
        'nama',
        'level',
        'xp',
        'level_lapar',
        'level_stamina',
        'status',
        'terakhir_makan',
        'terakhir_tidur',
        'tidur_sampai',
        'users_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
