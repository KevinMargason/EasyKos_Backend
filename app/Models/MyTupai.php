<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyTupai extends Model
{
    protected $table = 'myTupai';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
