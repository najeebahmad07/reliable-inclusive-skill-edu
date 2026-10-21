<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicMessage extends Model
{
    protected $table = 'public_messages';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
    ];
}

