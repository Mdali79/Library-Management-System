<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class auther extends Model
{
    use HasFactory;
    protected $guarded=[];

    /**
     * Get all books for this author (many-to-many relationship)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function books(): BelongsToMany
    {
        // Check which column exists in the table
        $columns = \Schema::getColumnListing('book_authors');
        $foreignKey = in_array('auther_id', $columns) ? 'auther_id' : 'author_id';
        
        $relation = $this->belongsToMany(book::class, 'book_authors', $foreignKey, 'book_id')
            ->withTimestamps();
        
        // Add pivot columns if they exist
        if (in_array('is_main_author', $columns) && in_array('is_corresponding_author', $columns)) {
            $relation->withPivot('is_main_author', 'is_corresponding_author');
        } elseif (in_array('author_type', $columns)) {
            $relation->withPivot('author_type', 'order');
        }
        
        return $relation;
    }
}
