<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MarksheetTemplate;

class TestCustomTemplateSeeder extends Seeder
{
    public function run()
    {
        MarksheetTemplate::create([
            'name' => 'Test Custom Template',
            'description' => 'A test custom template for verification',
            'template_type' => 'custom',
            'grading_scale_id' => null,
            'is_active' => true,
            'is_default' => false,
            'settings' => [
                'show_school_logo' => true,
                'show_school_name' => true,
                'show_school_address' => true,
                'show_contact_info' => true,
                'show_principal_name' => true,
                'show_principal_signature' => false,
                'logo_position' => 'left',
                'logo_size' => 'medium',
                'school_name_size' => '24',
                'school_name_bold' => true,
                'show_theory_practical' => true,
                'show_assessment_marks' => true,
                'show_remarks' => true,
                'show_grade_points' => true,
                'show_attendance' => false,
                'show_rank' => false,
            ]
        ]);

        echo "Test custom template created successfully!\n";
    }
}
