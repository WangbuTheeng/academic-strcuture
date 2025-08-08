<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MarkEntrySeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('marks')->truncate();
        Schema::enableForeignKeyConstraints();

        $exams = DB::table('exams')->where('result_status', '!=', 'draft')->get();
        $students = DB::table('student_enrollments')->select('id as enrollment_id', 'student_id', 'class_id')->get();
        $teacherId = DB::table('users')->where('role', 'teacher')->first()->id;

        foreach ($exams as $exam) {
            $subjectIds = DB::table('program_subjects')
                ->join('subjects', 'program_subjects.subject_id', '=', 'subjects.id')
                ->where('program_subjects.program_id', DB::table('classes')->where('id', $exam->class_id)->value('program_id'))
                ->pluck('subjects.id');

            foreach ($subjectIds as $subjectId) {
                foreach ($students as $student) {
                    if ($student->class_id != $exam->class_id) continue;

                    $theoryMax = $exam->theory_max;
                    $practicalMax = $exam->practical_max;
                    $assessMax = $exam->assess_max;

                    $assess = $assessMax > 0 ? rand($assessMax * 0.6, $assessMax) : null;
                    $theory = rand($theoryMax * 0.5, $theoryMax);
                    $practical = $practicalMax > 0 ? rand($practicalMax * 0.5, $practicalMax) : null;

                    $total = ($assess ?? 0) + $theory + ($practical ?? 0);
                    $max = $exam->max_marks;
                    $percentage = ($total / $max) * 100;

                    $grade = $percentage >= 80 ? 'A+' : 
                             $percentage >= 70 ? 'A' :
                             $percentage >= 60 ? 'B+' :
                             $percentage >= 50 ? 'B' :
                             $percentage >= 40 ? 'C' : 'F';

                    $gpa = match(true) {
                        $percentage >= 80 => 4.0,
                        $percentage >= 70 => 3.6,
                        $percentage >= 60 => 3.2,
                        $percentage >= 50 => 2.8,
                        $percentage >= 40 => 2.4,
                        default => 0.0
                    };

                    $result = $percentage >= 40 ? 'Pass' : 'Fail';

                    DB::table('marks')->insert([
                        'student_id' => $student->student_id,
                        'subject_id' => $subjectId,
                        'exam_id' => $exam->id,
                        'assess_marks' => $assess,
                        'theory_marks' => $theory,
                        'practical_marks' => $practical,
                        'total' => $total,
                        'percentage' => $percentage,
                        'grade' => $grade,
                        'gpa' => $gpa,
                        'result' => $result,
                        'status' => 'final',
                        'created_by' => $teacherId,
                        'updated_by' => $teacherId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('✅ Marks seeded for all exams and students.');
    }
}