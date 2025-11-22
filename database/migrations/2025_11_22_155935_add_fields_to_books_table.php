<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('isbn')->nullable()->unique();
            $table->string('edition')->nullable();
            $table->year('publication_year')->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->integer('total_quantity')->default(1);
            $table->integer('available_quantity')->default(1);
            $table->integer('issued_quantity')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['isbn', 'edition', 'publication_year', 'description', 'cover_image', 'total_quantity', 'available_quantity', 'issued_quantity']);
        });
    }
}
