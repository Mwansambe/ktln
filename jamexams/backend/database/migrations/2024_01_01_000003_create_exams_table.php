<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 10)->unique(); // e.g. 001, 123
            $table->string('title');
            $table->text('description')->nullable();
            $table->uuid('subject_id');
            $table->enum('exam_type', ['past_paper', 'mock', 'exercise', 'revision'])->default('past_paper');
            $table->string('class_level')->nullable(); // e.g. Form 1, Form 4
            $table->year('year');
            $table->string('file_path');          // exam PDF path
            $table->bigInteger('file_size')->default(0); // bytes
            $table->string('marking_scheme_path')->nullable();
            $table->bigInteger('marking_scheme_size')->default(0);
            $table->boolean('has_marking_scheme')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('download_count')->default(0);
            $table->uuid('uploaded_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('subject_id')->references('id')->on('subjects')->cascadeOnDelete();
            $table->foreign('uploaded_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['subject_id', 'is_published']);
            $table->index(['year', 'class_level']);
            $table->index('exam_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
