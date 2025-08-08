<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SampleStudentsSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('student_enrollments')->truncate();
        DB::table('students')->truncate();
        Schema::enableForeignKeyConstraints();

        $academicYear = DB::table('academic_years')->where('is_current', true)->first();
        if (!$academicYear) {
            $this->command->warn('❌ No current academic year found. Run AcademicStructureSeeder first.');
            return;
        }

        $classes = DB::table('classes')->pluck('id', 'code');
        $programs = DB::table('programs')->pluck('id', 'name');

        $names = [
            ['Ram', 'Shrestha'], ['Sita', 'Karki'], ['Hari', 'Thapa'], ['Gita', 'Rai'], ['Bikash', 'Adhikari'],
            ['Anjali', 'Poudel'], ['Suman', 'Basnet'], ['Nisha', 'Maharjan'], ['Raj', 'KC'], ['Pooja', 'Ghimire'],
            ['Amit', 'Sharma'], ['Sarita', 'Bhandari'], ['Manoj', 'Dahal'], ['Rita', 'Joshi'], ['Deepak', 'Gurung'],
        ];

        $globalCounter = 0; // Global counter for unique admission numbers
        foreach ($classes as $code => $classId) {
            $programId = null;
            if (str_contains($code, 'S') || str_contains($code, 'M')) {
                $programName = str_contains($code, 'S') ? 'Science' : 'Management';
                $programId = $programs[$programName] ?? null;
            } elseif (str_starts_with($code, 'BBS')) {
                $programId = $programs['BBS'] ?? null;
            } elseif (str_starts_with($code, 'BCA')) {
                $programId = $programs['BCA'] ?? null;
            } else {
                $programId = $programs['General School'] ?? null;
            }

            if (!$programId) continue;

            $classCounter = 0; // Local counter for roll numbers in this class
            foreach ($names as $name) {
                $globalCounter++;
                $classCounter++;
                $firstName = $name[0];
                $lastName = $name[1];
                $admissionNo = 'ADM-' . $academicYear->name . '-' . str_pad($globalCounter, 3, '0', STR_PAD_LEFT);

                $studentId = DB::table('students')->insertGetId([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'date_of_birth' => now()->subYears(16)->subMonths(rand(0, 48))->format('Y-m-d'),
                    'gender' => in_array($firstName, ['Sita', 'Gita', 'Anjali', 'Nisha', 'Pooja', 'Sarita', 'Rita']) ? 'Female' : 'Male',
                    'religion' => 'Hindu',
                    'caste' => 'Brahmin',
                    'nationality' => 'Nepali',
                    'phone' => '98' . rand(40000000, 99999999),
                    'address' => 'Kathmandu, Nepal',
                    'guardian_name' => 'Mr. ' . $lastName,
                    'guardian_relation' => 'Father',
                    'guardian_phone' => '98' . rand(40000000, 99999999),
                    'admission_number' => $admissionNo,
                    'admission_date' => now()->subYears(2)->addMonths(rand(0, 24)),
                    'photo_url' => 'student-photos/' . strtolower(Str::slug($firstName . '-' . $lastName)) . '.jpg',
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $rollNo = $code . '-' . str_pad($classCounter, 2, '0', STR_PAD_LEFT);

                DB::table('student_enrollments')->insert([
                    'student_id' => $studentId,
                    'academic_year_id' => $academicYear->id,
                    'class_id' => $classId,
                    'program_id' => $programId,
                    'roll_no' => $rollNo,
                    'section' => 'A',
                    'enrollment_date' => $academicYear->start_date,
                    'status' => 'active',
                    'academic_standing' => 'good',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Sample students seeded across all classes.');
    }
}