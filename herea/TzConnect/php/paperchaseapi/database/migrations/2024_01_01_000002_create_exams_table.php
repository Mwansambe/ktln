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
        Schema::create('exams', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('title');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->string('year');
            $table->enum('type', [
                'PRACTICE_PAPER',
                'MOCK_PAPER',
                'PAST_PAPER',
                'NECTA_PAPER',
                'REVISION_PAPER',
                'JOINT_PAPER',
                'PRE_NECTA'
            ]);
            $table->text('description')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('pdf_name')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('marking_scheme_path')->nullable();
            $table->string('marking_scheme_name')->nullable();
            $table->bigInteger('marking_scheme_size')->nullable();
            $table->boolean('has_marking_scheme')->default(false);
            $table->string('preview_image')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->string('bg_color')->nullable();
            $table->string('border_color')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_new')->default(true);
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->integer('bookmark_count')->default(0);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
