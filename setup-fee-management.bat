@echo off
echo Setting up Fee Management System...
echo.

echo Running database migrations...
php artisan migrate --force

echo.
echo Clearing application cache...
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo.
echo Optimizing application...
php artisan route:cache
php artisan config:cache

echo.
echo Fee Management System setup complete!
echo.
echo You can now access:
echo - Fee Management Overview: http://127.0.0.1:8000/admin/fees/overview
echo - Fee Structures: http://127.0.0.1:8000/admin/fees/structures
echo - Student Bills: http://127.0.0.1:8000/admin/fees/bills
echo - Payments: http://127.0.0.1:8000/admin/fees/payments
echo.
pause
