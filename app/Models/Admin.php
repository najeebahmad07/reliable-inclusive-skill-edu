<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'admins';

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'department_it_computer' => 'boolean',
        'department_fire_safety' => 'boolean',
        'office' => 'boolean',
        'toilet' => 'boolean',
        'wallet_balance' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'admin_id');
    }

    public function programs()
    {
        return $this->belongsToMany(Program::class, 'admin_programs', 'admin_id', 'program_id');
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class, 'admin_id');
    }

    public function failedTransactions()
    {
        return $this->hasMany(FailedTransaction::class, 'admin_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'to_admin');
    }

    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->where('is_read', 0)->count();
    }

    public function certificates()
    {
        return $this->hasMany(AdminCertificate::class, 'admin_id');
    }
}

