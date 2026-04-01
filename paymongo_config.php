<?php
/**
 * PayMongo Configuration
 * 
 * Get your API keys from: https://dashboard.paymongo.com/
 * 
 * Test Mode Keys (use for testing):
 * - Secret Key: sk_test_...
 * - Public Key: pk_test_...
 * 
 * Live Mode Keys (use for production):
 * - Secret Key: sk_live_...
 * - Public Key: pk_live_...
 */

return [
    // API Keys - REPLACE WITH YOUR ACTUAL KEYS
    'secret_key' => 'sk_test_YOUR_SECRET_KEY_HERE',
    'public_key' => 'pk_test_YOUR_PUBLIC_KEY_HERE',
    
    // Webhook secret (from PayMongo dashboard)
    'webhook_secret' => 'whsec_YOUR_WEBHOOK_SECRET_HERE',
    
    // Payment settings
    'currency' => 'PHP',
    'service_fee' => 50, // Service fee in PHP
    
    // URLs
    'success_url' => 'http://localhost/php_kapoy/customer/payment_success.php',
    'cancel_url' => 'http://localhost/php_kapoy/customer/payment_failed.php',
    'webhook_url' => 'http://localhost/php_kapoy/customer/paymongo_webhook.php',
];