<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fines', function (Blueprint $table) {
            $table->string('transaction_id', 100)->nullable()->index()->after('notes');
            $table->string('gateway_status', 50)->nullable()->after('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('fines', function (Blueprint $table) {
            $table->dropColumn(['transaction_id', 'gateway_status']);
        });
    }
};
