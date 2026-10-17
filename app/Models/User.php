<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // admin, pm, developer, finance, client
        'phone_number',
        'avatar',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function clientProfile()
    {
        return $this->hasOne(Client::class, 'user_id');
    }

    public function assignedPmOrders()
    {
        return $this->hasMany(Order::class, 'assigned_pm_id');
    }

    public function assignedDevOrders()
    {
        return $this->hasMany(Order::class, 'assigned_dev_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPm(): bool
    {
        return $this->role === 'pm';
    }

    public function isDeveloper(): bool
    {
        return $this->role === 'developer';
    }

    public function isFinance(): bool
    {
        return $this->role === 'finance';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }
}
