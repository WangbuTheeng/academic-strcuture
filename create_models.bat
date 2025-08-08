@echo off
echo Creating Academic Management System Models...

php artisan make:model Faculty
php artisan make:model Department
php artisan make:model ClassModel
php artisan make:model Program
php artisan make:model AcademicYear
php artisan make:model Semester
php artisan make:model Subject
php artisan make:model ProgramSubject
php artisan make:model GradingScale
php artisan make:model Student
php artisan make:model StudentEnrollment
php artisan make:model TeacherSubject
php artisan make:model StudentSubject
php artisan make:model StudentDocument
php artisan make:model Exam
php artisan make:model Mark
php artisan make:model MarkLog
php artisan make:model InstituteSettings
php artisan make:model Backup

echo All models created successfully!
pause
