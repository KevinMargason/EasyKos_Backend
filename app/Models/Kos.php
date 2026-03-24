<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kos extends Model
{
    protected $table = 'kos';

    protected $fillable = [
        'nama',
        'alamat',
        'jumlah_kamar',
        'gender',
        'foto',
        'rating',
        'region_idregion',
        'user_id',
        'peraturan',
        'fasilitas_umum',
    ];

    public function rooms()
    {
        return $this->hasMany(Rooms::class, 'kos_id', 'id');
    }

    public function aturans()
    {
        return $this->belongsToMany(Aturan::class, 'kos_aturan', 'kos_id', 'aturan_id');
    }

    public function fasilitas()
    {
        return $this->belongsToMany(Fasilitas::class, 'kos_fasilitas', 'kos_id', 'fasilitas_id');
    }

    public function owner()
    {
        return $this->hasOneThrough(User::class, Rooms::class, 'kos_id', 'id', 'id', 'users_id')
            ->where('users.role', 'owner');
    }
}
