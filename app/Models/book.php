<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    /**
     * Get all authors for this book (many-to-many relationship)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function authors(): BelongsToMany
    {
        // Check which column exists in the table
        $columns = \Schema::getColumnListing('book_authors');
        $foreignKey = in_array('auther_id', $columns) ? 'auther_id' : 'author_id';
        
        $relation = $this->belongsToMany(auther::class, 'book_authors', 'book_id', $foreignKey)
            ->withTimestamps();
        
        // Add pivot columns if they exist
        if (in_array('is_main_author', $columns) && in_array('is_corresponding_author', $columns)) {
            $relation->withPivot('is_main_author', 'is_corresponding_author');
        } elseif (in_array('author_type', $columns)) {
            $relation->withPivot('author_type', 'order');
        }
        
        return $relation;
    }

    /**
     * Get the main author for this book
     *
     * @return \App\Models\auther|null
     */
    public function getMainAuthor()
    {
        return $this->authors()->wherePivot('is_main_author', true)->first();
    }

    /**
     * Get the corresponding author for this book
     *
     * @return \App\Models\auther|null
     */
    public function getCorrespondingAuthor()
    {
        return $this->authors()->wherePivot('is_corresponding_author', true)->first();
    }
}
