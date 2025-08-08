<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ExamsSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('marks')->truncate();
        DB::table('exams')->truncate();
        Schema::enableForeignKeyConstraints();

        $academicYear = DB::table('academic_years')->where('is_current', true)->first();
        if (!$academicYear) {
            $this->command->warn('❌ No current academic year found. Run AcademicStructureSeeder first.');
            return;
        }

        $classes = DB::table('classes')->select('id', 'name', 'code')->get();
        $programs = DB::table('programs')->pluck('id', 'name');
        $gradingScales = DB::table('grading_scales')
            ->select('id', 'applies_to_program_id', 'applies_to_level_id')
            ->get();

        $adminId = DB::table('users')->where('role', 'admin')->value('id') ??
                   DB::table('users')->first()->id;

        $exams = [];

        foreach ($classes as $class) {
            $programId = null;
            if (str_contains($class->code, 'S') || str_contains($class->code, 'M')) {
                $programName = str_contains($class->code, 'S') ? 'Science' : 'Management';
                $programId = $programs[$programName] ?? null;
            } elseif (str_starts_with($class->code, 'BBS')) {
                $programId = $programs['BBS'] ?? null;
            } elseif (str_starts_with($class->code, 'BCA')) {
                $programId = $programs['BCA'] ?? null;
            } else {
                $programId = $programs['General School'] ?? null;
            }

            if (!$programId) continue;

            // Find grading scale for this program or level
            $gradingScaleId = null;
            foreach ($gradingScales as $scale) {
                if (($scale->applies_to_program_id == $programId) ||
                    ($scale->applies_to_level_id && DB::table('classes')->where('id', $class->id)->value('level_id') == $scale->applies_to_level_id)) {
                    $gradingScaleId = $scale->id;
                    break;
                }
            }

            $year = now()->year;
            $month = now()->month;

            // Terminal Exam (75+25 or 80+20 or 100)
            $terminal = [
                'name' => 'Terminal Exam',
                'exam_type' => 'terminal',
                'academic_year_id' => $academicYear->id,
                'class_id' => $class->id,
                'program_id' => $programId,
                'grading_scale_id' => $gradingScaleId,
                'max_marks' => 100,
                'theory_max' => 75,
                'practical_max' => 25,
                'assess_max' => 0,
                'has_practical' => false,
                'start_date' => "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01",
                'end_date' => "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-07",
                'submission_deadline' => Carbon::now()->addDays(10),
                'result_status' => 'scheduled',
                'is_locked' => false,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Adjust based on class
            if (str_starts_with($class->code, 'BBS') || str_starts_with($class->code, '10')) {
                $terminal['theory_max'] = 100;
                $terminal['practical_max'] = 0;
                $terminal['max_marks'] = 100;
            } elseif (str_starts_with($class->code, 'BCA')) {
                $terminal['theory_max'] = 60;
                $terminal['practical_max'] = 40;
                $terminal['max_marks'] = 100;
            } elseif ($class->id <= DB::table('classes')->where('code', '8')->value('id')) {
                $terminal['theory_max'] = 80;
                $terminal['practical_max'] = 20;
                $terminal['max_marks'] = 100;
            }

            $terminal['has_practical'] = $terminal['practical_max'] > 0;

            $exams[] = $terminal;

            // Assessment (Internal)
            $assessment = $terminal;
            $assessment['name'] = 'Internal Assessment';
            $assessment['exam_type'] = 'assessment';
            $assessment['theory_max'] = 0;
            $assessment['practical_max'] = 0;
            $assessment['assess_max'] = 20;
            $assessment['max_marks'] = 20;
            $assessment['has_practical'] = false;
            $assessment['start_date'] = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-10";
            $assessment['end_date'] = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-15";

            $exams[] = $assessment;
        }

        foreach ($exams as $exam) {
            DB::table('exams')->insert($exam);
        }

        $this->command->info('✅ Exams seeded: Terminal & Assessment for all classes.');
    }
}