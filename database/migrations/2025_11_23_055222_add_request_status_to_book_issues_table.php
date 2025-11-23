<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequestStatusToBookIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('book_issues', function (Blueprint $table) {
            $table->enum('request_status', ['pending', 'approved', 'rejected', 'issued'])->default('pending')->after('issue_status');
            $table->foreignId('approved_by')->nullable()->after('request_status')->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejection_reason')->nullable()->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('book_issues', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['request_status', 'approved_by', 'approved_at', 'rejection_reason']);
        });
    }
}
