<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class settings extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $fillable = [
        'return_days',
        'fine_per_day',
        'fine_grace_period_days',
        'max_borrowing_limit_student',
        'max_borrowing_limit_teacher',
        'max_borrowing_limit_librarian',
    ];

    protected $casts = [
        'return_days' => 'integer',
        'fine_per_day' => 'decimal:2',
        'fine_grace_period_days' => 'integer',
        'max_borrowing_limit_student' => 'integer',
        'max_borrowing_limit_teacher' => 'integer',
        'max_borrowing_limit_librarian' => 'integer',
    ];
}
