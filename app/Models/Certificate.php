<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $table = 'certificates';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'issue_date' => 'date',
        'created_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}

