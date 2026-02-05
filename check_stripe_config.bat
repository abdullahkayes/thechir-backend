@echo off
REM Stripe Payment System - Quick Health Check for Windows
REM Run this to verify your Stripe payment system is working

echo.
echo ==================================
echo STRIPE PAYMENT SYSTEM HEALTH CHECK
echo ==================================
echo.

REM Check if .env file exists
if exist .env (
    echo [OK] .env file found
) else (
    echo [ERROR] .env file NOT found
    exit /b 1
)

REM Check Laravel cache
echo.
echo CLEARING LARAVEL CACHE...
php artisan config:clear >nul 2>&1
if %errorlevel% equ 0 (
    echo [OK] Cache cleared successfully
) else (
    echo [WARNING] Could not clear cache
)

REM Check for STRIPE_KEY
echo.
echo CHECKING STRIPE CONFIGURATION...
echo.
findstr /M "STRIPE_KEY=pk_test" .env >nul
if %errorlevel% equ 0 (
    echo [OK] STRIPE_KEY (publishable key) found
    findstr "STRIPE_KEY=pk_test" .env | findstr /v "REM" | for /f "tokens=2 delims==" %%a in ('findstr "STRIPE_KEY"') do echo     Key: %%a
) else (
    echo [ERROR] STRIPE_KEY NOT found or invalid format
)

REM Check for STRIPE_SECRET
findstr /M "STRIPE_SECRET=sk_test" .env >nul
if %errorlevel% equ 0 (
    echo [OK] STRIPE_SECRET (secret key) found
) else (
    echo [ERROR] STRIPE_SECRET NOT found or invalid format
)

REM Check for FRONTEND_URL
echo.
findstr /M "FRONTEND_URL=" .env >nul
if %errorlevel% equ 0 (
    echo [OK] FRONTEND_URL configured
    findstr "FRONTEND_URL=" .env | findstr /v "REM"
) else (
    echo [WARNING] FRONTEND_URL NOT configured (payment redirect may fail)
    echo     Add to .env: FRONTEND_URL=http://localhost:5173
)

REM Check if StripePaymentController has been fixed
echo.
echo CHECKING CODE UPDATES...
echo.
findstr /M "FRONTEND_URL" app\Http\Controllers\StripePaymentController.php >nul
if %errorlevel% equ 0 (
    echo [OK] StripePaymentController updated
) else (
    echo [ERROR] StripePaymentController not updated
)

REM Check blade template
findstr /M "isset.*paymentIntent" resources\views\stripe.blade.php >nul
if %errorlevel% equ 0 (
    echo [OK] stripe.blade.php has error handling
) else (
    echo [ERROR] stripe.blade.php missing error handling
)

REM Final status
echo.
echo ==================================
echo TEST INSTRUCTIONS:
echo ==================================
echo.
echo 1. Use Stripe Test Card: 4242 4242 4242 4242
echo 2. Expiry: Any future date (e.g., 12/25)
echo 3. CVC: Any 3 digits (e.g., 123)
echo 4. Name: Any name
echo.
echo Database verification:
echo   - Check: stripe_orders table
echo   - Check: orders table
echo   - Check: order_products table
echo.
echo Log file: storage/logs/laravel.log
echo.
echo ==================================
echo.
echo Hit any key to continue...
pause >nul
