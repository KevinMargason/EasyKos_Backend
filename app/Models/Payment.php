<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payment';

    protected $fillable = [
        'rooms_id',
        'tenant',
        'status',
        'tanggal_bayar',
        'jenis_pembayaran',
        'voucher_id',
        'amount',
    ];

    public function room()
    {
        return $this->belongsTo(Rooms::class, 'rooms_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'tenant', 'id');
    }
}
