<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminCertificate extends Model
{
    protected $table = 'admin_certificates';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'issue_date' => 'date',
        'created_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}

