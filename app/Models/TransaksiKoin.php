<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiKoin extends Model
{
    protected $table = 'transaksi_koin';
    protected $fillable = [
        'users_id',
        'dompet_koin_id',
        'asal_koin',
        'reference_id',
        'direction',
        'jumlah',
        'koin_sebelum',
        'koin_setelah',
        'deskripsi'
    ];
}
