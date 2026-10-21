<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function from()
    {
        return $this->belongsTo(Admin::class, 'from_admin');
    }

    public function to()
    {
        return $this->belongsTo(Admin::class, 'to_admin');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}

