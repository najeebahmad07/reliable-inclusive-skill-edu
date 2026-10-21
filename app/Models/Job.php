<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'jobs';
    protected $guarded = ['id'];

    protected $casts = [
        'is_published' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function applications()
    {
        return $this->hasMany(JobApplication::class, 'job_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}

