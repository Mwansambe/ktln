<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10); // e.g., 001, 123
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->enum('exam_type', ['PAST_PAPER', 'MOCK', 'MIDTERM', 'FINAL', 'REVISION'])
                  ->default('PAST_PAPER');
            $table->string('class_level', 20)->nullable(); // e.g., Form 1, Form 2
            $table->year('year')->nullable();
            $table->string('exam_file_path'); // Path to PDF
            $table->unsignedBigInteger('exam_file_size')->default(0); // In bytes
            $table->string('marking_scheme_path')->nullable();
            $table->unsignedBigInteger('marking_scheme_size')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('download_count')->default(0);
            $table->foreignUuid('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['subject_id', 'is_published']);
            $table->index(['class_level', 'exam_type']);
            $table->index('year');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
