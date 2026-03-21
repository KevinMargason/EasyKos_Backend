<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TupaiHistori extends Model
{
    protected $table = 'tupai_histori';

    protected $fillable = [
        'myTupai_id',
        'aktivitas_tipe',
        'aktivitas_waktu',
        'lapar_sebelum',
        'lapar_setelah',
        'energi_sebelum',
        'energi_setelah',
        'notes'
    ];

    public function tupai()
    {
        return $this->belongsTo(MyTupai::class, 'myTupai_id', 'id');
    }
}
