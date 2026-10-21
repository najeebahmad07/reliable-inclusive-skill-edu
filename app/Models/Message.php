<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}

