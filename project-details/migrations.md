Below is the complete, detailed, and production-ready set of Laravel migrations for all 21 core tables in your Academic Management System (v3.0).

These migrations include:

✅ Proper foreign key constraints
✅ Indexes for performance
✅ Unique constraints (e.g., roll no, student/year)
✅ Soft deletes where needed
✅ Enum fields with Laravel compatibility
✅ Descriptive comments
✅ Nullability & defaults as per spec
📁 Save these in database/migrations/ in your Laravel project.

1. create_levels_table.php
php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
⌄
⌄
⌄
⌄
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50); // School, College, Bachelor
            $table->integer('order')->default(0); // 1=School, 2=College
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('levels');
    }
};
2. create_faculties_table.php
php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
⌄
⌄
⌄
⌄
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100); // Faculty of Science
            $table->string('code', 10)->nullable(); // FOS
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('faculties');
    }
};
3. create_departments_table.php
php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
⌄
⌄
⌄
⌄
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('faculty_id')->unsigned();
            $table->string('name', 100); // Computer Dept
            $table->string('code', 10)->nullable();
            $table->timestamps();

            $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('departments');
    }
};
4. create_classes_table.php
php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
⌄
⌄
⌄
⌄
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('level_id')->unsigned();
            $table->bigInteger('department_id')->unsigned()->nullable();
            $table->string('name', 50); // Class 9, BCA1
            $table->string('code', 10); // 9, BCA1
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('classes');
    }
};
5. create_programs_table.php
php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
⌄
⌄
⌄
⌄
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('department_id')->unsigned();
            $table->string('name', 100); // Science, BCA, BBS
            $table->integer('duration_years');
            $table->enum('degree_type', ['school', 'college', 'bachelor']);
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('programs');
    }
};
6. create_academic_years_table.php
php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
⌄
⌄
⌄
⌄
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 10); // 2081
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->timestamps();

            $table->unique('name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('academic_years');
    }
};
7. create_semesters_table.php
php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
⌄
⌄
⌄
⌄
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('academic_year_id')->unsigned();
            $table->string('name', 20); // Semester 1, Yearly
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('order')->nullable(); // 1, 2
            $table->timestamps();

            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('semesters');
    }
};
8. create_subjects_table.php
php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
⌄
⌄
⌄
⌄
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('code', 10);
            $table->integer('max_assess')->default(0);
            $table->integer('max_theory')->default(0);
            $table->integer('max_practical')->default(0);
            $table->boolean('is_practical')->default(false);
            $table->boolean('has_internal')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('subjects');
    }
};
9. create_program_subjects_table.php
php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
⌄
⌄
⌄
⌄
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('program_subjects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('program_id')->unsigned();
            $table->bigInteger('subject_id')->unsigned();
            $table->boolean('is_compulsory')->default(true);
            $table->integer('credit_hours')->nullable();
            $table->bigInteger('semester_id')->unsigned()->nullable();
            $table->integer('year_no')->nullable(); // for yearly programs
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('set null');

            $table->unique(['program_id', 'subject_id', 'semester_id', 'year_no']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('program_subjects');
    }
};
10. create_grading_scales_table.php
php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
35
36
⌄
⌄
⌄
⌄
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('grading_scales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100); // School (40% Pass)
            $table->text('description')->nullable();
            $table->integer('pass_mark')->default(40); // min % to pass
            $table->enum('scale_type', ['percentage', 'gpa', 'division'])->default('percentage');
            $table->string('grade', 5); // A+, B
            $table->integer('min_percentage');
            $table->integer('max_percentage');
            $table->decimal('gpa', 3, 2);

            $table->bigInteger('applies_to_program_id')->unsigned()->nullable();
            $table->bigInteger('applies_to_level_id')->unsigned()->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('applies_to_program_id')->references('id')->on('programs')->onDelete('set null');
            $table->foreign('applies_to_level_id')->references('id')->on('levels')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('grading_scales');
    }
};
11. create_students_table.php
php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
35
36
37
38
39
40
41
42
43
44
45
46
47
48
49
50
51
52
53
54
55
56
57
58
59
60
61
62
63
⌄
⌄
⌄
⌄
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->date('date_of_birth');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('blood_group', 5)->nullable();
            $table->string('religion', 50)->nullable();
            $table->string('caste', 50)->nullable();
            $table->string('nationality', 50)->default('Nepali');
            $table->string('mother_tongue', 50)->nullable();

            $table->string('phone', 15);
            $table->string('email', 100)->nullable();
            $table->text('address');
            $table->text('temporary_address')->nullable();

            $table->string('emergency_contact_name', 100)->nullable();
            $table->string('emergency_contact_phone', 15)->nullable();
            $table->string('emergency_contact_relation', 20)->nullable();

            $table->string('guardian_name', 100);
            $table->string('guardian_relation', 20);
            $table->string('guardian_phone', 15);
            $table->string('guardian_email', 100)->nullable();

            $table->string('citizenship_number', 20)->nullable();
            $table->date('citizenship_issue_date')->nullable();
            $table->string('citizenship_issue_district', 50)->nullable();
            $table->string('citizenship_document', 255)->nullable();

            $table->string('previous_school_name', 150)->nullable();
            $table->string('transfer_certificate_no', 50)->nullable();
            $table->date('transfer_certificate_date')->nullable();
            $table->string('migration_certificate_no', 50)->nullable();

            $table->enum('disability_status', ['none', 'visual', 'hearing', 'mobility', 'learning', 'other'])->default('none');
            $table->text('special_needs')->nullable();

            $table->string('admission_number', 20)->unique(); // ADM-2078-001
            $table->date('admission_date');
            $table->string('photo_url', 255)->nullable();
            $table->enum('status', ['active', 'inactive', 'graduated', 'transferred', 'dropped'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
};
12. create_student_enrollments_table.php
php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
35
36
37
38
39
⌄
⌄
⌄
⌄
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('student_id')->unsigned();
            $table->bigInteger('academic_year_id')->unsigned();
            $table->bigInteger('class_id')->unsigned();
            $table->bigInteger('program_id')->unsigned();
            $table->string('roll_no', 10);
            $table->string('section', 5)->nullable();
            $table->date('enrollment_date');
            $table->enum('status', ['active', 'dropped', 'transferred'])->default('active');
            $table->enum('academic_standing', ['good', 'probation', 'repeat', 'dismissed'])->nullable();
            $table->integer('backlog_count')->default(0);
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');

            $table->unique(['student_id', 'academic_year_id']);
            $table->unique(['class_id', 'academic_year_id', 'roll_no']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_enrollments');
    }
};
13. create_teacher_subjects_table.php
php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
⌄
⌄
⌄
⌄
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('teacher_subjects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned(); // teacher
            $table->bigInteger('class_id')->unsigned();
            $table->bigInteger('subject_id')->unsigned();
            $table->bigInteger('academic_year_id')->unsigned();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');

            $table->unique(['user_id', 'class_id', 'subject_id', 'academic_year_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('teacher_subjects');
    }
};
14. create_exams_table.php
php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
35
36
37
38
39
40
41
42
43
44
45
46
47
48
49
50
51
52
53
54
55
56
⌄
⌄
⌄
⌄
⌄
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->enum('exam_type', ['assessment', 'terminal', 'quiz', 'project', 'practical', 'final']);
            $table->bigInteger('academic_year_id')->unsigned();
            $table->bigInteger('semester_id')->unsigned()->nullable();
            $table->bigInteger('class_id')->unsigned()->nullable();
            $table->bigInteger('program_id')->unsigned()->nullable();
            $table->bigInteger('subject_id')->unsigned()->nullable();
            $table->bigInteger('grading_scale_id')->unsigned()->nullable();

            $table->integer('max_marks');
            $table->integer('theory_max');
            $table->integer('practical_max')->default(0);
            $table->integer('assess_max')->default(0);
            $table->boolean('has_practical')->default(false);

            $table->dateTime('submission_deadline')->nullable();
            $table->enum('result_status', [
                'draft', 'scheduled', 'ongoing', 'submitted', 'approved', 'published', 'locked'
            ])->default('scheduled');
            $table->boolean('is_locked')->default(false);
            $table->timestamp('approval_date')->nullable();

            $table->date('start_date');
            $table->date('end_date');
            $table->text('remarks')->nullable();

            $table->bigInteger('created_by')->unsigned();
            $table->timestamps();

            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('set null');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('set null');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('set null');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('set null');
            $table->foreign('grading_scale_id')->references('id')->on('grading_scales')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('exams');
    }
};
15. create_student_subjects_table.php
php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
⌄
⌄
⌄
⌄
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_subjects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('student_enrollment_id')->unsigned();
            $table->bigInteger('subject_id')->unsigned();
            $table->date('date_added');
            $table->enum('status', ['active', 'dropped'])->default('active');
            $table->timestamps();

            $table->foreign('student_enrollment_id')->references('id')->on('student_enrollments')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_subjects');
    }
};
16. create_marks_table.php
php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
35
36
37
38
39
40
41
42
43
44
45
46
47
48
49
50
51
52
53
⌄
⌄
⌄
⌄
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('marks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('student_id')->unsigned();
            $table->bigInteger('subject_id')->unsigned();
            $table->bigInteger('exam_id')->unsigned();

            $table->decimal('assess_marks', 5, 2)->nullable();
            $table->decimal('theory_marks', 5, 2)->nullable();
            $table->decimal('practical_marks', 5, 2)->nullable();

            $table->decimal('total', 5, 2)->storedAs('COALESCE(assess_marks, 0) + COALESCE(theory_marks, 0) + COALESCE(practical_marks, 0)');
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('grade', 5)->nullable();
            $table->decimal('gpa', 3, 2)->nullable();
            $table->enum('result', ['Pass', 'Fail', 'Incomplete'])->nullable();

            $table->boolean('is_reexam')->default(false);
            $table->bigInteger('original_exam_id')->unsigned()->nullable();
            $table->decimal('grace_marks', 3, 2)->default(0.00);
            $table->text('carry_forward_reason')->nullable();

            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->bigInteger('created_by')->unsigned();
            $table->bigInteger('updated_by')->unsigned();

            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('original_exam_id')->references('id')->on('exams')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            $table->unique(['student_id', 'subject_id', 'exam_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('marks');
    }
};
17. create_mark_logs_table.php
php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
⌄
⌄
⌄
⌄
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mark_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('mark_id')->unsigned();
            $table->string('field_name', 20); // theory_marks
            $table->decimal('old_value', 5, 2)->nullable();
            $table->decimal('new_value', 5, 2);
            $table->bigInteger('changed_by')->unsigned();
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('mark_id')->references('id')->on('marks')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mark_logs');
    }
};
18. create_activity_log_table.php
php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
⌄
⌄
⌄
⌄
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('subject_type', 200);
            $table->bigInteger('subject_id')->unsigned();
            $table->string('action', 50); // mark_updated, exam_published
            $table->text('description')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_log');
    }
};
19. create_institute_settings_table.php
php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
35
36
⌄
⌄
⌄
⌄
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('institute_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('school_name', 200);
            $table->string('school_name_np', 200)->nullable();
            $table->text('address');
            $table->string('phone', 15);
            $table->string('email', 100);
            $table->string('logo_path', 255)->nullable();
            $table->string('seal_path', 255)->nullable();
            $table->string('principal_name', 100);
            $table->string('principal_signature', 255)->nullable();

            $table->boolean('show_attendance')->default(false);
            $table->boolean('show_remarks')->default(true);
            $table->enum('template_style', ['modern', 'classic', 'minimal'])->default('modern');
            $table->boolean('enable_grace_marks')->default(true);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('institute_settings');
    }
};
20. create_backups_table.php
php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
⌄
⌄
⌄
⌄
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('path', 500); // backups/2081-04-05.sql.gz
            $table->integer('size'); // in KB
            $table->enum('type', ['manual', 'auto']);
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('backups');
    }
};
21. create_student_documents_table.php
php


1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
⌄
⌄
⌄
⌄
⌄
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('student_id')->unsigned();
            $table->enum('doc_type', [
                'citizenship', 'birth_cert', 'transfer_cert', 'migration', 'photo', 'other'
            ]);
            $table->string('file_path', 500);
            $table->bigInteger('uploaded_by')->unsigned();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_documents');
    }
};
✅ Next Steps
Copy all 21 migrations into database/migrations/
Run:
bash


1
php artisan migrate
Then run seeders (I can generate those next)
Start building Livewire components