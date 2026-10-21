<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'programs';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function courses()
    {
        return $this->hasMany(Course::class, 'program_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'program_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'program_id');
    }

    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'admin_programs', 'program_id', 'admin_id');
    }
}

