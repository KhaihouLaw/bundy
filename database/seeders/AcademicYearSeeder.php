<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $current_year = date('Y');
        do {
            $succeeding_year = $current_year + 1;
            AcademicYear::create([
                'description' => "A.Y. {$current_year}-{$succeeding_year} 1st Semester",
                'semester' => 1,
                'start_year' => $current_year,
                'end_year' => $succeeding_year,
                'start_date' => $current_year . date('-m-d'),
                'end_date' => $current_year . '-12-24'
            ]);
            $current_year = $succeeding_year;
        } while ($current_year < 2025);
        
    }
}
