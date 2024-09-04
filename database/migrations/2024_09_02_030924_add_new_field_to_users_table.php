<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram_id')->nullable()->unique();
            $table->string('username')->nullable();
            $table->string('avatar')->nullable();// Optional
            $table->string('access_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('telegram_id');
            $table->dropColumn('username');
            $table->dropColumn('avatar');
            $table->dropColumn('access_token');  // Optional
        });
    }
};
