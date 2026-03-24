<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rooms extends Model
{
    protected $table = 'rooms';

    protected $fillable = [
        'kos_id',
        'harga',
        'nomor_kamar',
        'ukuran_kamar',
        'listrik',
        'users_id',
        'fasilitas_kamar',
        'foto'
    ];

    public function kos()
    {
        return $this->belongsTo(Kos::class, 'kos_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
