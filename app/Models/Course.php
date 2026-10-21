<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'course_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'course_id');
    }
}

