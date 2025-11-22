<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'user_id',
        'student_id',
        'status',
        'reserved_at',
        'notified_at',
        'expires_at',
    ];

    protected $casts = [
        'reserved_at' => 'datetime',
        'notified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(book::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(student::class);
    }
}
