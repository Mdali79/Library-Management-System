<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove record_status (draft concept) from tables.
     */
    public function up(): void
    {
        foreach (['books', 'authers', 'publishers', 'categories'] as $name) {
            if (Schema::hasTable($name) && Schema::hasColumn($name, 'record_status')) {
                Schema::table($name, function (Blueprint $blueprint) {
                    $blueprint->dropColumn('record_status');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['books', 'authers', 'publishers', 'categories'] as $name) {
            if (Schema::hasTable($name) && !Schema::hasColumn($name, 'record_status')) {
                Schema::table($name, function (Blueprint $blueprint) {
                    $blueprint->string('record_status', 20)->default('published')->after('id');
                });
            }
        }
    }
};
