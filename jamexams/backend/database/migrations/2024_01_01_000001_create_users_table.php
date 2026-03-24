<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone', 20)->nullable();
            $table->string('avatar')->nullable();
            $table->string('fcm_token')->nullable(); // Firebase token for push notifications
            $table->boolean('is_active')->default(false); // Admin must manually activate
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // 30 days after activation
            $table->rememberToken();
            $table->timestamps();

            // Indexes for performance
            $table->index('email');
            $table->index('is_active');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
