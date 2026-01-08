<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookAuthorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if table exists and what structure it has
        if (!Schema::hasTable('book_authors')) {
            Schema::create('book_authors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('book_id')->constrained()->onDelete('cascade');
                $table->foreignId('auther_id')->constrained()->onDelete('cascade');
                $table->boolean('is_main_author')->default(false);
                $table->boolean('is_corresponding_author')->default(false);
                $table->timestamps();
                
                // Prevent duplicate author entries for the same book
                $table->unique(['book_id', 'auther_id']);
            });
        } else {
            // Table exists - check if it has the old structure (author_id) or new structure (auther_id)
            $columns = Schema::getColumnListing('book_authors');
            
            if (in_array('author_id', $columns) && !in_array('auther_id', $columns)) {
                // Old structure exists - add new columns
                Schema::table('book_authors', function (Blueprint $table) use ($columns) {
                    if (!in_array('auther_id', $columns)) {
                        $table->foreignId('auther_id')->nullable()->after('book_id');
                    }
                    if (!in_array('is_main_author', $columns)) {
                        $table->boolean('is_main_author')->default(false);
                    }
                    if (!in_array('is_corresponding_author', $columns)) {
                        $table->boolean('is_corresponding_author')->default(false);
                    }
                });
                
                // Migrate data from author_id to auther_id
                \DB::statement('UPDATE book_authors SET auther_id = author_id WHERE auther_id IS NULL');
            } else {
                // New structure or both exist - just add missing boolean columns
                Schema::table('book_authors', function (Blueprint $table) use ($columns) {
                    if (!in_array('is_main_author', $columns)) {
                        $table->boolean('is_main_author')->default(false);
                    }
                    if (!in_array('is_corresponding_author', $columns)) {
                        $table->boolean('is_corresponding_author')->default(false);
                    }
                });
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
        Schema::dropIfExists('book_authors');
    }
}
