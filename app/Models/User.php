<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'contact',
        'role',
        'department',
        'batch',
        'roll',
        'reg_no',
        'password',
        'is_verified',
        'verification_code',
        'registration_status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    public function student()
    {
        return $this->hasOne(student::class);
    }

    public function fines()
    {
        return $this->hasMany(Fine::class);
    }

    public function reservations()
    {
        return $this->hasMany(BookReservation::class);
    }

    /**
     * Get the user who approved this registration
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }
}
