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
        $table->id();
        $table->string('title');
        $table->string('code')->unique();
        $table->integer('year');                // ← ADD year column
        $table->integer('downloads')->default(0);  // ← PLURAL 'downloads'
        $table->boolean('is_published')->default(true);
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
