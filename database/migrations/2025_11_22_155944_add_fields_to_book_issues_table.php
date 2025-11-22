<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToBookIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('book_issues', function (Blueprint $table) {
            $table->decimal('fine_amount', 10, 2)->default(0)->after('return_day');
            $table->enum('book_condition', ['good', 'damaged', 'lost'])->default('good')->after('fine_amount');
            $table->text('damage_notes')->nullable()->after('book_condition');
            $table->string('issue_receipt_number')->nullable()->unique()->after('damage_notes');
            $table->string('return_receipt_number')->nullable()->unique()->after('issue_receipt_number');
            $table->boolean('is_overdue')->default(false)->after('return_receipt_number');
            $table->boolean('fine_notified')->default(false)->after('is_overdue');
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
            $table->dropColumn(['fine_amount', 'book_condition', 'damage_notes', 'issue_receipt_number', 'return_receipt_number', 'is_overdue', 'fine_notified']);
        });
    }
}
