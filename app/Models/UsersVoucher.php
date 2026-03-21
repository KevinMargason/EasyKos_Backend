<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersVoucher extends Model
{
    protected $table = 'users_voucher';
    protected $fillable = ['users_id', 'voucher_id', 'is_used', 'claimed_at'];
}
