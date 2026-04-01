<?php
/**
 * PayMongo API Integration
 * Documentation: https://developers.paymongo.com/
 */

class PayMongo {
    private $secret_key;
    private $public_key;
    private $base_url = 'https://api.paymongo.com/v1';
    
    public function __construct($secret_key = '', $public_key = '') {
        $this->secret_key = $secret_key;
        $this->public_key = $public_key;
    }
    
    /**
     * Set API keys
     */
    public function setKeys($secret_key, $public_key) {
        $this->secret_key = $secret_key;
        $this->public_key = $public_key;
    }
    
    /**
     * Get public key for frontend
     */
    public function getPublicKey() {
        return $this->public_key;
    }
    
    /**
     * Make API request
     */
    private function request($method, $endpoint, $data = []) {
        $url = $this->base_url . $endpoint;
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($this->secret_key . ':')
        ];
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            return ['error' => true, 'message' => $error];
        }
        
        return json_decode($response, true);
    }
    
    /**
     * Create a Payment Intent
     * This is used for one-time payments
     */
    public function createPaymentIntent($amount, $currency = 'PHP', $description = '', $metadata = []) {
        $data = [
            'data' => [
                'attributes' => [
                    'amount' => $amount * 100, // PayMongo uses cents
                    'currency' => $currency,
                    'description' => $description,
                    'metadata' => $metadata
                ]
            ]
        ];
        
        return $this->request('POST', '/payment_intents', $data);
    }
    
    /**
     * Retrieve a Payment Intent
     */
    public function retrievePaymentIntent($payment_intent_id) {
        return $this->request('GET', '/payment_intents/' . $payment_intent_id);
    }
    
    /**
     * Create a Payment Method (for saved cards)
     */
    public function createPaymentMethod($type, $details = []) {
        $data = [
            'data' => [
                'attributes' => [
                    'type' => $type,
                    'details' => $details
                ]
            ]
        ];
        
        return $this->request('POST', '/payment_methods', $data);
    }
    
    /**
     * Attach Payment Method to Payment Intent
     */
    public function attachPaymentIntent($payment_intent_id, $payment_method_id, $return_url = '') {
        $data = [
            'data' => [
                'attributes' => [
                    'payment_method' => $payment_method_id,
                    'return_url' => $return_url
                ]
            ]
        ];
        
        return $this->request('POST', '/payment_intents/' . $payment_intent_id . '/attach', $data);
    }
    
    /**
     * Create a Checkout Session (for GCash, Card, etc.)
     */
    public function createCheckoutSession($amount, $currency = 'PHP', $description = '', $success_url = '', $cancel_url = '', $metadata = []) {
        $data = [
            'data' => [
                'attributes' => [
                    'amount' => $amount * 100,
                    'currency' => $currency,
                    'description' => $description,
                    'success_url' => $success_url,
                    'cancel_url' => $cancel_url,
                    'metadata' => $metadata
                ]
            ]
        ];
        
        return $this->request('POST', '/checkout_sessions', $data);
    }
    
    /**
     * Retrieve Checkout Session
     */
    public function retrieveCheckoutSession($checkout_session_id) {
        return $this->request('GET', '/checkout_sessions/' . $checkout_session_id);
    }
    
    /**
     * Create a Refund
     */
    public function createRefund($payment_intent_id, $amount = null, $reason = '') {
        $attributes = ['reason' => $reason];
        
        if ($amount) {
            $attributes['amount'] = $amount * 100;
        }
        
        $data = [
            'data' => [
                'attributes' => $attributes
            ]
        ];
        
        return $this->request('POST', '/payment_intents/' . $payment_intent_id . '/refunds', $data);
    }
    
    /**
     * List all payments
     */
    public function listPayments($limit = 10) {
        return $this->request('GET', '/payments?limit=' . $limit);
    }
    
    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature($payload, $signature, $webhook_secret) {
        $expected_signature = hash_hmac('sha256', $payload, $webhook_secret);
        return hash_equals($expected_signature, $signature);
    }
}

/**
 * Helper function to format amount for PayMongo (converts to cents)
 */
function formatAmountForPayMongo($amount) {
    return (int)round($amount * 100);
}

/**
 * Helper function to format amount from PayMongo (converts from cents)
 */
function formatAmountFromPayMongo($amount) {
    return $amount / 100;
}