<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subjects';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'has_practical' => 'boolean',
        'created_at' => 'datetime',
    ];

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
        return $this->hasMany(Mark::class, 'subject_id');
    }
}

