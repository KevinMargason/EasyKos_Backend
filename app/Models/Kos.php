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
        'region_idregion'
    ];

    public function rooms()
    {
        return $this->hasMany(Rooms::class, 'kos_id', 'id');
    }
}
