#!/bin/bash
# Stripe Payment System - Quick Health Check
# Run this to verify your Stripe payment system is working

echo "=================================="
echo "STRIPE PAYMENT SYSTEM HEALTH CHECK"
echo "=================================="
echo ""

# Check if .env file exists
if [ -f .env ]; then
    echo "✓ .env file found"
else
    echo "✗ .env file NOT found"
    exit 1
fi

# Check for duplicate Stripe keys
STRIPE_KEY_COUNT=$(grep -c "^STRIPE_KEY=" .env)
STRIPE_SECRET_COUNT=$(grep -c "^STRIPE_SECRET=" .env)

echo ""
echo "STRIPE KEY CHECK:"
if [ "$STRIPE_KEY_COUNT" -eq 1 ]; then
    echo "✓ Single STRIPE_KEY found"
    grep "STRIPE_KEY=" .env
else
    echo "✗ Multiple STRIPE_KEY entries found ($STRIPE_KEY_COUNT)"
fi

if [ "$STRIPE_SECRET_COUNT" -eq 1 ]; then
    echo "✓ Single STRIPE_SECRET found"
    grep "STRIPE_SECRET=" .env | head -1
else
    echo "✗ Multiple STRIPE_SECRET entries found ($STRIPE_SECRET_COUNT)"
fi

# Check FRONTEND_URL
echo ""
echo "FRONTEND URL CHECK:"
if grep -q "^FRONTEND_URL=" .env; then
    echo "✓ FRONTEND_URL configured"
    grep "FRONTEND_URL=" .env
else
    echo "✗ FRONTEND_URL NOT configured (payment redirect may fail)"
fi

# Check if controller has been fixed
echo ""
echo "CONTROLLER VALIDATION:"
if grep -q "FRONTEND_URL" app/Http/Controllers/StripePaymentController.php; then
    echo "✓ StripePaymentController uses FRONTEND_URL"
else
    echo "✗ StripePaymentController not updated"
fi

# Check blade template
echo ""
echo "BLADE TEMPLATE VALIDATION:"
if grep -q "isset(\$paymentIntent)" resources/views/stripe.blade.php; then
    echo "✓ stripe.blade.php has error handling"
else
    echo "✗ stripe.blade.php missing error handling"
fi

# Final status
echo ""
echo "=================================="
if [ "$STRIPE_KEY_COUNT" -eq 1 ] && [ "$STRIPE_SECRET_COUNT" -eq 1 ] && grep -q "FRONTEND_URL" .env; then
    echo "✓ STRIPE PAYMENT SYSTEM READY"
    echo "You can now test payments with card: 4242 4242 4242 4242"
else
    echo "⚠ STRIPE PAYMENT SYSTEM NEEDS ATTENTION"
    echo "Check the results above and fix any issues"
fi
echo "=================================="
