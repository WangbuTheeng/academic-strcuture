<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToSchool;

class GradingScale extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'name',
        'description',
        'level_id',
        'program_id',
        'is_default',
        'pass_mark',
        'max_marks',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'pass_mark' => 'decimal:2',
        'max_marks' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the level that owns the grading scale.
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    /**
     * Get the program that owns the grading scale.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the user who created the grading scale.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the grade ranges for this grading scale.
     */
    public function gradeRanges(): HasMany
    {
        return $this->hasMany(GradeRange::class)->orderBy('min_percentage', 'desc');
    }

    /**
     * Get the exams using this grading scale.
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    /**
     * Calculate grade for a given percentage.
     */
    public function calculateGrade(float $percentage): array
    {
        $gradeRange = $this->gradeRanges()
                          ->where('min_percentage', '<=', $percentage)
                          ->where('max_percentage', '>=', $percentage)
                          ->first();

        if (!$gradeRange) {
            return [
                'grade' => 'F',
                'gpa' => 0.0,
                'result' => 'Fail'
            ];
        }

        return [
            'grade' => $gradeRange->grade,
            'gpa' => $gradeRange->gpa,
            'result' => $percentage >= $this->pass_mark ? 'Pass' : 'Fail'
        ];
    }

    /**
     * Get the default grading scale for a level/program.
     */
    public static function getDefault($levelId = null, $programId = null): ?self
    {
        $query = static::where('is_default', true)->where('is_active', true);

        if ($programId) {
            $query->where('program_id', $programId);
        } elseif ($levelId) {
            $query->where('level_id', $levelId)->whereNull('program_id');
        } else {
            $query->whereNull('level_id')->whereNull('program_id');
        }

        return $query->first();
    }

    /**
     * Scope to get active grading scales.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get default grading scales.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Get grade point for a given percentage.
     * This method provides compatibility with marksheet templates.
     */
    public function getGradePoint(float $percentage): float
    {
        // Use the existing calculateGrade method to get GPA
        $gradeData = $this->calculateGrade($percentage);
        return $gradeData['gpa'] ?? 0.0;
    }

    /**
     * Get grade letter for a given percentage.
     * This method provides compatibility with marksheet templates.
     */
    public function getGrade(float $percentage): string
    {
        // Use the existing calculateGrade method to get grade
        $gradeData = $this->calculateGrade($percentage);
        return $gradeData['grade'] ?? 'F';
    }

    /**
     * Get result status for a given percentage.
     * This method provides compatibility with marksheet templates.
     */
    public function getResult(float $percentage): string
    {
        // Use the existing calculateGrade method to get result
        $gradeData = $this->calculateGrade($percentage);
        return $gradeData['result'] ?? 'Fail';
    }

    /**
     * Get remarks for a given grade.
     * This method provides compatibility with marksheet templates.
     */
    public function getRemarks(float $percentage): string
    {
        $gradeRange = $this->gradeRanges()
                          ->where('min_percentage', '<=', $percentage)
                          ->where('max_percentage', '>=', $percentage)
                          ->first();

        if ($gradeRange && isset($gradeRange->remarks)) {
            return $gradeRange->remarks;
        }

        // Default remarks based on percentage
        if ($percentage >= 90) return 'Outstanding performance';
        if ($percentage >= 80) return 'Excellent work';
        if ($percentage >= 70) return 'Very good performance';
        if ($percentage >= 60) return 'Good work';
        if ($percentage >= 50) return 'Satisfactory performance';
        if ($percentage >= 40) return 'Acceptable performance';

        return 'Needs improvement';
    }

    /**
     * Get grading scale statistics.
     */
    public function getStatistics(): array
    {
        $totalExams = $this->exams()->count();
        $totalMarks = Mark::whereHas('exam', function($q) {
            $q->where('grading_scale_id', $this->id);
        })->count();

        $gradeDistribution = Mark::whereHas('exam', function($q) {
            $q->where('grading_scale_id', $this->id);
        })
        ->where('status', 'approved')
        ->selectRaw('grade, COUNT(*) as count')
        ->groupBy('grade')
        ->pluck('count', 'grade')
        ->toArray();

        return [
            'total_exams' => $totalExams,
            'total_marks' => $totalMarks,
            'grade_distribution' => $gradeDistribution,
            'usage_rate' => $totalExams > 0 ? 'Active' : 'Unused'
        ];
    }
}
