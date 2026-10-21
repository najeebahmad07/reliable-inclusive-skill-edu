<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';

    protected $guarded = ['id'];

    protected $casts = [
        'dob' => 'date',
        'is_disabled' => 'boolean',
        'tenth_percentage' => 'decimal:2',
        'twelfth_percentage' => 'decimal:2',
        'ug_percentage' => 'decimal:2',
        'pg_percentage' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function marks()
    {
        return $this->hasMany(Mark::class, 'student_id');
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class, 'student_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'student_id');
    }

    public function isApproved(): bool
    {
        return $this->status === 'Approved';
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['Pending', 'WaitingApproval']);
    }
}

