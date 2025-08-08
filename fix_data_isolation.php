<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 Starting comprehensive data isolation fix...\n\n";

// Get all schools
$schools = DB::table('schools')->get();
echo "Found " . count($schools) . " schools:\n";
foreach ($schools as $school) {
    echo "  - {$school->name} ({$school->code}) - ID: {$school->id}\n";
}
echo "\n";

// Step 1: Fix null school_id records by assigning them to the first school
$firstSchool = $schools->first();
if (!$firstSchool) {
    echo "❌ No schools found! Please create at least one school first.\n";
    exit(1);
}

echo "🔧 Step 1: Fixing null school_id records (assigning to {$firstSchool->name})...\n";

$tablesToFix = [
    'students', 'teachers', 'classes', 'subjects', 'exams', 'marks', 
    'levels', 'faculties', 'grading_scales', 'institute_settings',
    'academic_years', 'sessions', 'fees', 'attendances', 'assignments'
];

foreach ($tablesToFix as $table) {
    if (Schema::hasTable($table) && Schema::hasColumn($table, 'school_id')) {
        $nullCount = DB::table($table)->whereNull('school_id')->count();
        if ($nullCount > 0) {
            DB::table($table)->whereNull('school_id')->update(['school_id' => $firstSchool->id]);
            echo "  ✅ Fixed {$nullCount} null school_id records in {$table}\n";
        }
    }
}

echo "\n🔧 Step 2: Creating isolated data for each school...\n";

// Step 2: For each school, ensure they have their own isolated data
foreach ($schools as $school) {
    echo "\n📋 Processing school: {$school->name} (ID: {$school->id})\n";
    
    // Create default levels for this school
    $defaultLevels = [
        ['name' => 'Nursery', 'description' => 'Pre-primary education'],
        ['name' => 'LKG', 'description' => 'Lower Kindergarten'],
        ['name' => 'UKG', 'description' => 'Upper Kindergarten'],
        ['name' => 'Class 1', 'description' => 'Primary Level - Grade 1'],
        ['name' => 'Class 2', 'description' => 'Primary Level - Grade 2'],
        ['name' => 'Class 3', 'description' => 'Primary Level - Grade 3'],
        ['name' => 'Class 4', 'description' => 'Primary Level - Grade 4'],
        ['name' => 'Class 5', 'description' => 'Primary Level - Grade 5'],
        ['name' => 'Class 6', 'description' => 'Lower Secondary - Grade 6'],
        ['name' => 'Class 7', 'description' => 'Lower Secondary - Grade 7'],
        ['name' => 'Class 8', 'description' => 'Lower Secondary - Grade 8'],
        ['name' => 'Class 9', 'description' => 'Secondary Level - Grade 9'],
        ['name' => 'Class 10', 'description' => 'Secondary Level - Grade 10'],
        ['name' => 'Class 11', 'description' => 'Higher Secondary - Grade 11'],
        ['name' => 'Class 12', 'description' => 'Higher Secondary - Grade 12'],
    ];

    foreach ($defaultLevels as $levelData) {
        $exists = DB::table('levels')
            ->where('name', $levelData['name'])
            ->where('school_id', $school->id)
            ->exists();
            
        if (!$exists) {
            DB::table('levels')->insert([
                'name' => $levelData['name'],
                'description' => $levelData['description'],
                'school_id' => $school->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
    echo "  ✅ Ensured levels exist for {$school->name}\n";

    // Create default faculties for this school
    $defaultFaculties = [
        ['name' => 'Science', 'description' => 'Science and Mathematics'],
        ['name' => 'Management', 'description' => 'Business and Management Studies'],
        ['name' => 'Humanities', 'description' => 'Arts and Humanities'],
        ['name' => 'Education', 'description' => 'Education Studies'],
    ];

    foreach ($defaultFaculties as $facultyData) {
        $exists = DB::table('faculties')
            ->where('name', $facultyData['name'])
            ->where('school_id', $school->id)
            ->exists();
            
        if (!$exists) {
            DB::table('faculties')->insert([
                'name' => $facultyData['name'],
                'description' => $facultyData['description'],
                'school_id' => $school->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
    echo "  ✅ Ensured faculties exist for {$school->name}\n";

    // Create default grading scales for this school
    $defaultGrades = [
        ['grade' => 'A+', 'min_marks' => 90, 'max_marks' => 100, 'gpa' => 4.0, 'description' => 'Outstanding'],
        ['grade' => 'A', 'min_marks' => 80, 'max_marks' => 89, 'gpa' => 3.6, 'description' => 'Excellent'],
        ['grade' => 'B+', 'min_marks' => 70, 'max_marks' => 79, 'gpa' => 3.2, 'description' => 'Very Good'],
        ['grade' => 'B', 'min_marks' => 60, 'max_marks' => 69, 'gpa' => 2.8, 'description' => 'Good'],
        ['grade' => 'C+', 'min_marks' => 50, 'max_marks' => 59, 'gpa' => 2.4, 'description' => 'Satisfactory'],
        ['grade' => 'C', 'min_marks' => 40, 'max_marks' => 49, 'gpa' => 2.0, 'description' => 'Acceptable'],
        ['grade' => 'D', 'min_marks' => 32, 'max_marks' => 39, 'gpa' => 1.6, 'description' => 'Partially Acceptable'],
        ['grade' => 'E', 'min_marks' => 0, 'max_marks' => 31, 'gpa' => 0.8, 'description' => 'Insufficient'],
    ];

    foreach ($defaultGrades as $gradeData) {
        $exists = DB::table('grading_scales')
            ->where('grade', $gradeData['grade'])
            ->where('school_id', $school->id)
            ->exists();
            
        if (!$exists) {
            DB::table('grading_scales')->insert(array_merge($gradeData, [
                'school_id' => $school->id,
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
    echo "  ✅ Ensured grading scales exist for {$school->name}\n";

    // Create default institute settings for this school
    $exists = DB::table('institute_settings')->where('school_id', $school->id)->exists();
    if (!$exists) {
        DB::table('institute_settings')->insert([
            'school_id' => $school->id,
            'academic_year_start_month' => 4,
            'academic_year_end_month' => 3,
            'default_language' => 'en',
            'timezone' => 'Asia/Kathmandu',
            'currency' => 'NPR',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            'week_start' => 0,
            'enable_sms' => false,
            'enable_email' => true,
            'enable_notifications' => true,
            'max_students_per_class' => 40,
            'min_attendance_percentage' => 75,
            'passing_marks_percentage' => 40,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "  ✅ Created institute settings for {$school->name}\n";
    }

    // Create default academic year for this school
    $currentYear = date('Y');
    $exists = DB::table('academic_years')->where('school_id', $school->id)->exists();
    if (!$exists) {
        DB::table('academic_years')->insert([
            'school_id' => $school->id,
            'name' => $currentYear . '-' . ($currentYear + 1),
            'start_date' => $currentYear . '-04-01',
            'end_date' => ($currentYear + 1) . '-03-31',
            'is_current' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "  ✅ Created academic year for {$school->name}\n";
    }
}

echo "\n🔧 Step 3: Removing cross-contaminated data...\n";

// Step 3: Remove data that belongs to other schools (cross-contamination)
foreach ($schools as $school) {
    echo "\n🧹 Cleaning cross-contaminated data for {$school->name}...\n";
    
    foreach ($tablesToFix as $table) {
        if (Schema::hasTable($table) && Schema::hasColumn($table, 'school_id')) {
            // Count records that don't belong to this school
            $crossContaminated = DB::table($table)
                ->where('school_id', '!=', $school->id)
                ->count();
                
            if ($crossContaminated > 0) {
                echo "  ⚠️  Found {$crossContaminated} cross-contaminated records in {$table}\n";
                
                // For now, we'll just report them. In production, you might want to:
                // 1. Move them to the correct school
                // 2. Delete them if they're duplicates
                // 3. Create separate copies for each school
            }
        }
    }
}

echo "\n✅ Data isolation fix completed!\n";
echo "\n📊 Running final verification...\n";

// Final verification
exec('php artisan data:fix-isolation --dry-run', $output);
echo implode("\n", $output);

echo "\n🎉 Data isolation process completed!\n";
echo "\n💡 Next steps:\n";
echo "1. Review the remaining violations above\n";
echo "2. Run 'php artisan data:fix-isolation' to fix remaining null school_id issues\n";
echo "3. Manually review cross-contaminated data if needed\n";
echo "4. Test the application to ensure data isolation is working\n";
