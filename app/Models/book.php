<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class book extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    protected $fillable = [
        'name',
        'category_id',
        'auther_id',
        'publisher_id',
        'status',
        'isbn',
        'edition',
        'publication_year',
        'description',
        'cover_image',
        'total_quantity',
        'available_quantity',
        'issued_quantity',
    ];

    /**
     * Get the auther that owns the book
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function auther(): BelongsTo
    {
        return $this->belongsTo(auther::class,'auther_id','id');
    }

    /**
     * Get the category that owns the book
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(category::class);
    }

    /**
     * Get the publisher that owns the book
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(publisher::class);
    }

    public function issues()
    {
        return $this->hasMany(book_issue::class);
    }

    public function reservations()
    {
        return $this->hasMany(BookReservation::class);
    }
}
