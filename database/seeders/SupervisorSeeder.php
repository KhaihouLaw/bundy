<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class SupervisorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departments = [
            'MANCOM' => 'luzvimindacruz@laverdad.edu.ph',
            'Department Head' => 'norielmangasil@laverdad.edu.ph',
            'Accounting and Finance' => 'garylucin@laverdad.edu.ph',
            'Human Resource' => 'ivymae.garcia@laverdad.edu.ph',
            'Registration and Admission' => 'albertsoriano@laverdad.edu.ph',
            'Library' => 'gilbertfruel@laverdad.edu.ph',
            'Office of Student Affair' => 'kesiahcruz@laverdad.edu.ph',
            'Guidance' => 'jennysantos@laverdad.edu.ph',
            'Maintenance and Security' => 'ericbolano@laverdad.edu.ph',
            'Sports' => 'roldanvillanueva@laverdad.edu.ph',
            'Nurse' => 'ivymae.garcia@laverdad.edu.ph',
            'Kindergarten' => 'irishdamiao@laverdad.edu.ph',
            'Elementary' => 'titamatocinos@laverdad.edu.ph',
            'Junior Highschool' => 'rommelalba@laverdad.edu.ph',
            'Senior Highschool' => 'roldanvillanueva@laverdad.edu.ph',
            'College' => 'albertsoriano@laverdad.edu.ph'
        ];
        foreach ($departments as $department => $approver) {
            Department::where('department', $department)->first()->update([
                'supervisor' => $approver,
            ]);
        }
    }
}
