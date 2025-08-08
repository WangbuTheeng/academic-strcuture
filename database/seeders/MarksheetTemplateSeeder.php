<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MarksheetTemplate;
use App\Models\GradingScale;

class MarksheetTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first grading scale or create a default one
        $gradingScale = GradingScale::first();
        
        if (!$gradingScale) {
            $this->command->warn('No grading scale found. Please create a grading scale first.');
            return;
        }

        // Modern Template
        MarksheetTemplate::create([
            'name' => 'Modern Design',
            'description' => 'A clean, modern marksheet design with blue header and professional layout',
            'template_type' => 'modern',
            'grading_scale_id' => $gradingScale->id,
            'settings' => [
                // School Information Display
                'show_school_logo' => true,
                'show_school_name' => true,
                'show_school_address' => true,
                'show_contact_info' => true,
                'show_principal_name' => true,
                'show_principal_signature' => false,

                // Logo & Positioning
                'logo_position' => 'left',
                'logo_size' => 'medium',

                // School Name Styling
                'school_name_size' => '24',
                'school_name_bold' => true,

                // Marks Display
                'show_theory_practical' => true,
                'show_assessment_marks' => true,
                'show_remarks' => true,
                'show_grade_points' => true,
                'show_attendance' => false,
                'show_rank' => false,

                // Design Settings
                'header_color' => '#2563eb',
                'text_color' => '#1f2937',
                'border_style' => 'solid',
                'font_family' => 'Arial',
                'font_size' => 12,
            ],
            'is_default' => true,
            'is_active' => true,
        ]);

        // Classic Template
        MarksheetTemplate::create([
            'name' => 'Classic Design',
            'description' => 'Traditional marksheet design with formal appearance and Times font',
            'template_type' => 'classic',
            'grading_scale_id' => $gradingScale->id,
            'settings' => [
                'show_school_logo' => true,
                'show_school_address' => true,
                'show_principal_name' => true,
                'show_theory_practical' => true,
                'show_assessment_marks' => true,
                'show_remarks' => true,
                'show_grade_points' => true,
                'header_color' => '#1f2937',
                'text_color' => '#1f2937',
                'border_style' => 'solid',
                'font_family' => 'Times',
                'font_size' => 12,
            ],
            'is_default' => false,
            'is_active' => true,
        ]);

        // Minimal Template
        MarksheetTemplate::create([
            'name' => 'Minimal Design',
            'description' => 'Clean and minimal marksheet design with reduced visual elements',
            'template_type' => 'minimal',
            'grading_scale_id' => $gradingScale->id,
            'settings' => [
                'show_school_logo' => true,
                'show_school_address' => false,
                'show_principal_name' => false,
                'show_theory_practical' => false,
                'show_assessment_marks' => false,
                'show_remarks' => false,
                'show_grade_points' => false,
                'header_color' => '#6b7280',
                'text_color' => '#1f2937',
                'border_style' => 'none',
                'font_family' => 'Helvetica',
                'font_size' => 11,
            ],
            'is_default' => false,
            'is_active' => true,
        ]);

        // Nepali Style Template
        MarksheetTemplate::create([
            'name' => 'Nepali Traditional',
            'description' => 'Traditional Nepali marksheet design with cultural elements',
            'template_type' => 'custom',
            'grading_scale_id' => $gradingScale->id,
            'settings' => [
                'show_school_logo' => true,
                'show_school_address' => true,
                'show_principal_name' => true,
                'show_theory_practical' => true,
                'show_assessment_marks' => true,
                'show_remarks' => true,
                'show_grade_points' => true,
                'header_color' => '#dc2626',
                'text_color' => '#1f2937',
                'border_style' => 'solid',
                'font_family' => 'Arial',
                'font_size' => 12,
            ],
            'custom_css' => '
.marksheet-header {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    border-bottom: 3px solid #fbbf24;
}

.marksheet-table th {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    border: 2px solid #fbbf24;
}

.grade-summary {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 2px solid #fbbf24;
}

.signature-section {
    border-top: 3px solid #dc2626;
}
            ',
            'is_default' => false,
            'is_active' => true,
        ]);

        // High School Template
        MarksheetTemplate::create([
            'name' => 'High School Format',
            'description' => 'Designed specifically for high school level marksheets',
            'template_type' => 'modern',
            'grading_scale_id' => $gradingScale->id,
            'settings' => [
                'show_school_logo' => true,
                'show_school_address' => true,
                'show_principal_name' => true,
                'show_theory_practical' => true,
                'show_assessment_marks' => true,
                'show_remarks' => true,
                'show_grade_points' => false,
                'header_color' => '#059669',
                'text_color' => '#1f2937',
                'border_style' => 'solid',
                'font_family' => 'Arial',
                'font_size' => 12,
            ],
            'is_default' => false,
            'is_active' => true,
        ]);

        $this->command->info('Marksheet templates created successfully!');
    }
}
