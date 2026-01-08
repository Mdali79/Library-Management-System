<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateUsersRoleEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, update any existing Teacher or Librarian users to Admin
        DB::table('users')
            ->whereIn('role', ['Teacher', 'Librarian'])
            ->update(['role' => 'Admin']);

        // Modify the enum column
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Student', 'Admin') DEFAULT 'Student'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to original enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Student', 'Teacher', 'Librarian', 'Admin') DEFAULT 'Student'");
    }
}

