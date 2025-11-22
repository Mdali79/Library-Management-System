<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->enum('role', ['Student', 'Teacher', 'Librarian'])->default('Student')->after('id');
            $table->string('department')->nullable()->after('class');
            $table->string('batch')->nullable()->after('department');
            $table->string('roll')->nullable()->after('batch');
            $table->string('reg_no')->unique()->nullable()->after('roll');
            $table->integer('borrowing_limit')->default(5)->after('reg_no');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->after('borrowing_limit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['role', 'department', 'batch', 'roll', 'reg_no', 'borrowing_limit', 'user_id']);
        });
    }
}
