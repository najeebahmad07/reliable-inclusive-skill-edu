<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    protected $table = 'marks';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'marks_obtained' => 'integer',
        'th_marks' => 'integer',
        'pr_marks' => 'integer',
        'created_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}

