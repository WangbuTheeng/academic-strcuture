@echo off
echo Creating Academic Management System Migrations...

php artisan make:migration create_departments_table
php artisan make:migration create_classes_table
php artisan make:migration create_programs_table
php artisan make:migration create_academic_years_table
php artisan make:migration create_semesters_table
php artisan make:migration create_subjects_table
php artisan make:migration create_program_subjects_table
php artisan make:migration create_grading_scales_table
php artisan make:migration create_students_table
php artisan make:migration create_student_enrollments_table
php artisan make:migration create_teacher_subjects_table
php artisan make:migration create_student_subjects_table
php artisan make:migration create_student_documents_table
php artisan make:migration create_exams_table
php artisan make:migration create_marks_table
php artisan make:migration create_mark_logs_table
php artisan make:migration create_activity_log_table
php artisan make:migration create_institute_settings_table
php artisan make:migration create_backups_table

echo All migrations created successfully!
pause
