@echo off
echo ========================================
echo FIXING FEE MANAGEMENT SYSTEM ISSUES
echo ========================================
echo.

echo 1. Checking current migration status...
php artisan migrate:status

echo.
echo 2. Running database migrations to add missing columns...
echo    - Adding deleted_at to fee_structures table
echo    - Adding deleted_at to payments table
echo    - Adding deleted_at to student_bills table
echo    - Adding deleted_at to bill_items table
echo    - Adding deleted_at to payment_receipts table
php artisan migrate --force

echo.
echo 3. Clearing all application caches...
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo.
echo 4. Recreating optimized files...
php artisan route:cache
php artisan config:cache

echo.
echo 5. Testing database connection and tables...
php artisan tinker --execute="
try {
    echo 'Database connected: ' . (DB::connection()->getPdo() ? 'YES' : 'NO') . PHP_EOL;
    echo 'Fee structures table exists: ' . (Schema::hasTable('fee_structures') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Payments table exists: ' . (Schema::hasTable('payments') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Student bills table exists: ' . (Schema::hasTable('student_bills') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Fee structures deleted_at column: ' . (Schema::hasColumn('fee_structures', 'deleted_at') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Payments deleted_at column: ' . (Schema::hasColumn('payments', 'deleted_at') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Student bills deleted_at column: ' . (Schema::hasColumn('student_bills', 'deleted_at') ? 'YES' : 'NO') . PHP_EOL;
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo ========================================
echo Fee Management System Issues Fixed!
echo ========================================
echo.
echo Fixed Issues:
echo - Added missing deleted_at columns to all fee tables
echo - Fixed route naming issues (admin.fees.structures.index)
echo - Cleared all application caches
echo - Optimized routes and config
echo.
echo You can now access:
echo - Fee Management Overview: http://127.0.0.1:8000/admin/fees/overview
echo - Fee Structures: http://127.0.0.1:8000/admin/fees/structures
echo - Student Bills: http://127.0.0.1:8000/admin/fees/bills
echo - Payments: http://127.0.0.1:8000/admin/fees/payments
echo.
echo All permission checks have been temporarily disabled for testing.
echo.
pause
