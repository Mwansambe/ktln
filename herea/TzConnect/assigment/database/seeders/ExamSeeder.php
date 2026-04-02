<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;   // ← ADD THIS LINE (import the Exam model)

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        Exam::create([
            'title' => 'CSEE Mathematics Paper 1 2023',
            'code' => 'MATH2023P1',
            'year' => 2023,
            'downloads' => 45,
            'is_published' => true,
        ]);

        Exam::create([
            'title' => 'CSEE English Language 2023',
            'code' => 'ENG2023',
            'year' => 2023,
            'downloads' => 32,
            'is_published' => true,
        ]);

        Exam::create([
            'title' => 'CSEE Biology 2024',
            'code' => 'BIO2024',
            'year' => 2024,
            'downloads' => 12,
            'is_published' => true,
        ]);
    }
}
