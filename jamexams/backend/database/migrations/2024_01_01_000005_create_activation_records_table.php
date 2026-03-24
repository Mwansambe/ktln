<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activation_records', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('activated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('activated_at');
            $table->timestamp('expires_at');
            $table->unsignedSmallInteger('duration_days')->default(30);
            $table->text('notes')->nullable(); // Admin notes
            $table->boolean('is_current')->default(true); // Track latest activation
            $table->timestamps();

            $table->index(['user_id', 'is_current']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activation_records');
    }
};
