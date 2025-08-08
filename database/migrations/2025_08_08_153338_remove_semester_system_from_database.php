<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove all semester-related functionality from the database.
     */
    public function up(): void
    {
        // Remove semester_id columns from related tables
        $this->removeSemesterColumns();

        // Drop the semesters table
        $this->dropSemestersTable();
    }

    /**
     * Reverse the migrations.
     * Restore semester functionality (for rollback purposes).
     */
    public function down(): void
    {
        // Recreate semesters table
        $this->createSemestersTable();

        // Add back semester_id columns
        $this->addSemesterColumns();
    }

    /**
     * Remove semester_id columns from related tables.
     */
    private function removeSemesterColumns(): void
    {
        // Disable foreign key checks temporarily
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Remove semester_id from exams table
        if (Schema::hasTable('exams') && Schema::hasColumn('exams', 'semester_id')) {
            try {
                Schema::table('exams', function (Blueprint $table) {
                    $table->dropColumn('semester_id');
                });
                echo "✅ Removed semester_id from exams table\n";
            } catch (\Exception $e) {
                echo "⚠️  Error removing semester_id from exams table: " . $e->getMessage() . "\n";
            }
        }

        // Remove semester_id from program_subjects pivot table
        if (Schema::hasTable('program_subjects') && Schema::hasColumn('program_subjects', 'semester_id')) {
            try {
                Schema::table('program_subjects', function (Blueprint $table) {
                    $table->dropColumn('semester_id');
                });
                echo "✅ Removed semester_id from program_subjects table\n";
            } catch (\Exception $e) {
                echo "⚠️  Error removing semester_id from program_subjects table: " . $e->getMessage() . "\n";
            }
        } else {
            echo "ℹ️  semester_id column not found in program_subjects table\n";
        }

        // Remove semester_id from class_subjects pivot table
        if (Schema::hasTable('class_subjects') && Schema::hasColumn('class_subjects', 'semester_id')) {
            try {
                Schema::table('class_subjects', function (Blueprint $table) {
                    $table->dropColumn('semester_id');
                });
                echo "✅ Removed semester_id from class_subjects table\n";
            } catch (\Exception $e) {
                echo "⚠️  Error removing semester_id from class_subjects table: " . $e->getMessage() . "\n";
            }
        } else {
            echo "ℹ️  semester_id column not found in class_subjects table\n";
        }

        // Remove semester_id from program_classes pivot table
        if (Schema::hasTable('program_classes') && Schema::hasColumn('program_classes', 'semester_id')) {
            try {
                Schema::table('program_classes', function (Blueprint $table) {
                    $table->dropColumn('semester_id');
                });
                echo "✅ Removed semester_id from program_classes table\n";
            } catch (\Exception $e) {
                echo "⚠️  Error removing semester_id from program_classes table: " . $e->getMessage() . "\n";
            }
        } else {
            echo "ℹ️  semester_id column not found in program_classes table\n";
        }

        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Drop the semesters table.
     */
    private function dropSemestersTable(): void
    {
        if (Schema::hasTable('semesters')) {
            Schema::dropIfExists('semesters');
            echo "✅ Dropped semesters table\n";
        }
    }

    /**
     * Recreate semesters table (for rollback).
     */
    private function createSemestersTable(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('cascade');
            $table->bigInteger('academic_year_id')->unsigned();
            $table->string('name', 20);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('order')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
        });
    }

    /**
     * Add back semester_id columns (for rollback).
     */
    private function addSemesterColumns(): void
    {
        // Add semester_id back to exams table
        if (Schema::hasTable('exams') && !Schema::hasColumn('exams', 'semester_id')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->bigInteger('semester_id')->unsigned()->nullable()->after('academic_year_id');
                $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('set null');
            });
        }

        // Add semester_id back to program_subjects table
        if (Schema::hasTable('program_subjects') && !Schema::hasColumn('program_subjects', 'semester_id')) {
            Schema::table('program_subjects', function (Blueprint $table) {
                $table->bigInteger('semester_id')->unsigned()->nullable()->after('year_no');
                $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('set null');
            });
        }

        // Add semester_id back to class_subjects table
        if (Schema::hasTable('class_subjects') && !Schema::hasColumn('class_subjects', 'semester_id')) {
            Schema::table('class_subjects', function (Blueprint $table) {
                $table->bigInteger('semester_id')->unsigned()->nullable()->after('year_no');
                $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('set null');
            });
        }

        // Add semester_id back to program_classes table
        if (Schema::hasTable('program_classes') && !Schema::hasColumn('program_classes', 'semester_id')) {
            Schema::table('program_classes', function (Blueprint $table) {
                $table->bigInteger('semester_id')->unsigned()->nullable()->after('year_no');
                $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('set null');
            });
        }
    }
};
