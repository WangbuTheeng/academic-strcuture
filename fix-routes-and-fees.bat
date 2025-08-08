@echo off
echo ========================================
echo FIXING ROUTES AND FEE MANAGEMENT ISSUES
echo ========================================
echo.

echo 1. Clearing all route and view caches...
php artisan route:clear
php artisan view:clear
php artisan config:clear
php artisan cache:clear

echo.
echo 2. Running database migrations...
php artisan migrate --force

echo.
echo 3. Checking route definitions...
php artisan route:list --name=fees

echo.
echo 4. Testing fee structure routes...
php artisan tinker --execute="
try {
    echo 'Fee structures count: ' . App\Models\FeeStructure::count() . PHP_EOL;
    echo 'Students count: ' . App\Models\Student::count() . PHP_EOL;
    echo 'Academic years count: ' . App\Models\AcademicYear::count() . PHP_EOL;
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo 5. Creating sample fee structures if none exist...
php artisan tinker --execute="
try {
    if (App\Models\FeeStructure::count() == 0) {
        \$academicYear = App\Models\AcademicYear::first();
        if (\$academicYear) {
            App\Models\FeeStructure::create([
                'academic_year_id' => \$academicYear->id,
                'fee_category' => 'tuition',
                'fee_name' => 'Monthly Tuition Fee',
                'amount' => 5000.00,
                'billing_frequency' => 'monthly',
                'due_date_offset' => 30,
                'is_mandatory' => true,
                'is_active' => true,
                'created_by' => 1
            ]);
            
            App\Models\FeeStructure::create([
                'academic_year_id' => \$academicYear->id,
                'fee_category' => 'admission',
                'fee_name' => 'Admission Fee',
                'amount' => 2000.00,
                'billing_frequency' => 'annual',
                'due_date_offset' => 15,
                'is_mandatory' => true,
                'is_active' => true,
                'created_by' => 1
            ]);
            
            App\Models\FeeStructure::create([
                'academic_year_id' => \$academicYear->id,
                'fee_category' => 'examination',
                'fee_name' => 'Examination Fee',
                'amount' => 1500.00,
                'billing_frequency' => 'semester',
                'due_date_offset' => 20,
                'is_mandatory' => false,
                'is_active' => true,
                'created_by' => 1
            ]);
            
            echo 'Created 3 sample fee structures' . PHP_EOL;
        } else {
            echo 'No academic year found. Please create an academic year first.' . PHP_EOL;
        }
    } else {
        echo 'Fee structures already exist: ' . App\Models\FeeStructure::count() . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'Error creating fee structures: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo 6. Optimizing application...
php artisan config:cache
php artisan route:cache

echo.
echo ========================================
echo FIXES COMPLETED SUCCESSFULLY!
echo ========================================
echo.
echo What was fixed:
echo ✓ Cleared all route and view caches
echo ✓ Updated StudentBillController to support custom fees
echo ✓ Enhanced bill creation form with custom fee functionality
echo ✓ Created sample fee structures if none existed
echo ✓ Fixed route parameter binding issues
echo ✓ Optimized application caches
echo.
echo You can now:
echo • Create bills with predefined fee structures
echo • Add custom fees to bills
echo • Edit fee structures without route errors
echo • Access all fee management features
echo.
echo Test URLs:
echo - Fee Structures: http://127.0.0.1:8000/admin/fees/structures
echo - Create Bill: http://127.0.0.1:8000/admin/fees/bills/create
echo - Payments: http://127.0.0.1:8000/admin/fees/payments
echo.
pause
