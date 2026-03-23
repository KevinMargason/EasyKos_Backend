<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table = 'payment_methods';

    protected $fillable = [
        'nama_metode',
        'tipe',
        'nomor_rekening',
        'atas_nama',
        'is_active'
    ];
}
