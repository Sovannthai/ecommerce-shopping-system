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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
            $table->string('gender')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->enum('status',[1,0])->default(0);
            $table->longText('fcm_token')->nullable();
            $table->longText('token')->nullable();
            $table->string('address')->nullable();
            $table->timestamp('dob')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
