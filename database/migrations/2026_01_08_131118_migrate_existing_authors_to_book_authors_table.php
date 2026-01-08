<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrateExistingAuthorsToBookAuthorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if book_authors table has data already
        $existingCount = \DB::table('book_authors')->count();
        
        if ($existingCount == 0) {
            // Get column structure
            $columns = Schema::getColumnListing('book_authors');
            $hasAutherId = in_array('auther_id', $columns);
            $hasAuthorId = in_array('author_id', $columns);
            
            // Migrate existing auther_id data to book_authors pivot table
            $books = \DB::table('books')->whereNotNull('auther_id')->get();
            
            foreach ($books as $book) {
                // Check if entry already exists using raw SQL to avoid column name issues
                $exists = false;
                if ($hasAutherId) {
                    $exists = \DB::table('book_authors')
                        ->where('book_id', $book->id)
                        ->where('auther_id', $book->auther_id)
                        ->exists();
                } elseif ($hasAuthorId) {
                    $exists = \DB::table('book_authors')
                        ->where('book_id', $book->id)
                        ->where('author_id', $book->auther_id)
                        ->exists();
                }
                
                if (!$exists) {
                    $data = [
                        'book_id' => $book->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    
                    if ($hasAutherId) {
                        $data['auther_id'] = $book->auther_id;
                        if (in_array('is_main_author', $columns)) {
                            $data['is_main_author'] = true;
                        }
                        if (in_array('is_corresponding_author', $columns)) {
                            $data['is_corresponding_author'] = false;
                        }
                    } elseif ($hasAuthorId) {
                        $data['author_id'] = $book->auther_id;
                        if (in_array('author_type', $columns)) {
                            $data['author_type'] = 'main';
                        }
                        if (in_array('order', $columns)) {
                            $data['order'] = 0;
                        }
                    }
                    
                    \DB::table('book_authors')->insert($data);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove all migrated data
        \DB::table('book_authors')->truncate();
    }
}
