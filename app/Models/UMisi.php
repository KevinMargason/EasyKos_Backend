<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UMisi extends Model
{
    protected $table = 'misi_user';
    protected $fillable = [
        'misi_id',
        'users_id',
        'id',
        'tanggal',
        'progress_level',
        'status',
        'completed_at',
        'claimed_at'
    ];

    public function mission()
    {
        return $this->belongsTo(Misi::class, 'misi_id', 'id');
    }
}
