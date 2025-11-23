<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class book_issue extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * Get the student that owns the book_issue
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(student::class, 'student_id', 'id');
    }

    /**
     * Get the book that owns the book_issue
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(book::class, 'book_id', 'id');
    }

    public function fine()
    {
        return $this->hasOne(Fine::class);
    }

    /**
     * Get the user who approved the request
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    protected $fillable = [
        'student_id',
        'book_id',
        'issue_date',
        'return_date',
        'issue_status',
        'request_status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'return_day',
        'fine_amount',
        'book_condition',
        'damage_notes',
        'issue_receipt_number',
        'return_receipt_number',
        'is_overdue',
        'fine_notified',
    ];

    protected $casts = [
        'issue_date' => 'datetime:Y-m-d',
        'return_date' => 'datetime:Y-m-d',
        'return_day' => 'datetime:Y-m-d',
        'is_overdue' => 'boolean',
        'fine_notified' => 'boolean',
        'fine_amount' => 'decimal:2',
    ];

}
