<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedTransaction extends Model
{
    protected $table = 'failed_transactions';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }
}

