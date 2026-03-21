<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Misi extends Model
{
    protected $table = 'misi';
    protected $fillable = ['nama', 'deskripsi', 'periode', 'jenis', 'coin', 'xp', 'is_active'];

    public function userMissions()
    {
        return $this->hasMany(UMisi::class, 'misi_id', 'id');
    }
}
