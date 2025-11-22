<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->integer('return_days')->default(14);
            $table->decimal('fine_per_day', 10, 2)->default(0);
            $table->integer('fine_grace_period_days')->default(14);
            $table->integer('max_borrowing_limit_student')->default(5);
            $table->integer('max_borrowing_limit_teacher')->default(10);
            $table->integer('max_borrowing_limit_librarian')->default(15);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
