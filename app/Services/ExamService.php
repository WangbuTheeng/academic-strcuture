<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\Level;
use App\Models\ClassModel;
use Illuminate\Support\Facades\DB;

class ExamService
{
    /**
     * Create exam(s) based on scope
     */
    public function createExam(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $createdExams = [];
            
            if ($data['exam_scope'] === 'level' && isset($data['level_id'])) {
                // Create exams for all classes in the level
                $createdExams = $this->createLevelExams($data);
            } elseif ($data['exam_scope'] === 'school') {
                // Create exams for all classes in the school
                $createdExams = $this->createSchoolWideExams($data);
            } else {
                // Create single class exam
                $createdExams = [$this->createSingleExam($data)];
            }
            
            return $createdExams;
        });
    }

    /**
     * Create exams for all classes within a level
     */
    private function createLevelExams(array $data): array
    {
        $level = Level::findOrFail($data['level_id']);
        $classes = ClassModel::where('level_id', $level->id)->get();
        
        if ($classes->isEmpty()) {
            throw new \Exception("No classes found for level: {$level->name}");
        }
        
        $createdExams = [];
        
        foreach ($classes as $class) {
            $examData = $data;
            $examData['class_id'] = $class->id;
            $examData['name'] = $data['name'] . ' - ' . $class->name;
            
            $createdExams[] = $this->createSingleExam($examData);
        }
        
        return $createdExams;
    }

    /**
     * Create exams for all classes in the school
     */
    private function createSchoolWideExams(array $data): array
    {
        $classes = ClassModel::all();
        
        if ($classes->isEmpty()) {
            throw new \Exception("No classes found in the school");
        }
        
        $createdExams = [];
        
        foreach ($classes as $class) {
            $examData = $data;
            $examData['class_id'] = $class->id;
            $examData['level_id'] = $class->level_id;
            $examData['name'] = $data['name'] . ' - ' . $class->name;
            
            $createdExams[] = $this->createSingleExam($examData);
        }
        
        return $createdExams;
    }

    /**
     * Create a single exam
     */
    private function createSingleExam(array $data): Exam
    {
        // Remove custom_exam_type from data as it's not a database field
        if (isset($data['custom_exam_type'])) {
            if ($data['exam_type'] === 'custom') {
                $data['exam_type'] = $data['custom_exam_type'];
            }
            unset($data['custom_exam_type']);
        }
        
        $data['created_by'] = auth()->id();
        $data['result_status'] = 'draft';
        
        return Exam::create($data);
    }

    /**
     * Get exam summary for level-based exams
     */
    public function getExamSummary(Exam $exam): array
    {
        if ($exam->exam_scope === 'level') {
            $relatedExams = Exam::where('level_id', $exam->level_id)
                               ->where('name', 'like', '%' . explode(' - ', $exam->name)[0] . '%')
                               ->where('start_date', $exam->start_date)
                               ->get();
            
            return [
                'total_classes' => $relatedExams->count(),
                'total_students' => $relatedExams->sum(function($e) { return $e->students()->count(); }),
                'affected_classes' => $relatedExams->pluck('class.name')->toArray()
            ];
        }
        
        return [
            'total_classes' => 1,
            'total_students' => $exam->students()->count(),
            'affected_classes' => [$exam->class->name ?? 'All Classes']
        ];
    }

    /**
     * Update all related exams in a level
     */
    public function updateLevelExams(Exam $baseExam, array $data): array
    {
        return DB::transaction(function () use ($baseExam, $data) {
            $updatedExams = [];
            
            if ($baseExam->exam_scope === 'level') {
                // Find all related exams in the same level
                $relatedExams = Exam::where('level_id', $baseExam->level_id)
                                   ->where('name', 'like', '%' . explode(' - ', $baseExam->name)[0] . '%')
                                   ->where('start_date', $baseExam->start_date)
                                   ->get();
                
                foreach ($relatedExams as $exam) {
                    $examData = $data;
                    // Preserve class-specific name
                    $examData['name'] = $data['name'] . ' - ' . $exam->class->name;
                    $examData['class_id'] = $exam->class_id;
                    
                    $exam->update($examData);
                    $updatedExams[] = $exam;
                }
            } else {
                $baseExam->update($data);
                $updatedExams[] = $baseExam;
            }
            
            return $updatedExams;
        });
    }
}
