<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Activation records - track user access periods
        Schema::create('activation_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('activated_by');
            $table->timestamp('activated_at');
            $table->timestamp('expires_at');
            $table->integer('duration_days')->default(30);
            $table->enum('status', ['active', 'expired', 'revoked'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('activated_by')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['user_id', 'status']);
            $table->index('expires_at');
        });

        // Push notification log
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('body');
            $table->uuid('exam_id')->nullable();
            $table->uuid('sent_by');
            $table->integer('recipients_count')->default(0);
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->json('data')->nullable(); // extra payload
            $table->timestamps();

            $table->foreign('exam_id')->references('id')->on('exams')->nullOnDelete();
            $table->foreign('sent_by')->references('id')->on('users')->cascadeOnDelete();
        });

        // Download records
        Schema::create('download_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('exam_id');
            $table->uuid('user_id');
            $table->enum('file_type', ['exam', 'marking_scheme'])->default('exam');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->foreign('exam_id')->references('id')->on('exams')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['exam_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('download_records');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('activation_records');
    }
};
