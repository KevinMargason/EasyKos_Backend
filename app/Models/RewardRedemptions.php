<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardRedemptions extends Model
{
    protected $table = 'reward_redemption';
    protected $fillable = ['users_id', 'voucher_id', 'tanggal_tukar', 'jumlah_koin_tukar', 'status'];
}
