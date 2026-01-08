<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPdfFileToBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'pdf_file')) {
                $table->string('pdf_file')->nullable()->after('cover_image');
            }
            if (!Schema::hasColumn('books', 'preview_pages')) {
                $table->integer('preview_pages')->nullable()->default(50)->after('pdf_file');
            }
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
            $table->dropColumn(['pdf_file', 'preview_pages']);
        });
    }
}
