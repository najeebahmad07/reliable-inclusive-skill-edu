<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $table = 'job_applications';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'applied_at' => 'datetime',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }
}

