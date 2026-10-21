<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlacementStudent extends Model
{
    protected $table = 'placement_students';
    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}

