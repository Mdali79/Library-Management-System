<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class student extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $fillable = [
        'name',
        'age',
        'gender',
        'email',
        'phone',
        'address',
        'class',
        'role',
        'department',
        'batch',
        'roll',
        'reg_no',
        'borrowing_limit',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookIssues()
    {
        return $this->hasMany(book_issue::class);
    }

    public function fines()
    {
        return $this->hasMany(Fine::class);
    }

    public function reservations()
    {
        return $this->hasMany(BookReservation::class);
    }
}
