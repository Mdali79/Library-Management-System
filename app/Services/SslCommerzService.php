<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SslCommerzService
{
    protected string $sessionUrl;
    protected string $validationUrl;
    protected string $storeId;
    protected string $storePassword;

    public function __construct()
    {
        $this->sessionUrl = config('sslcommerz.session_url');
        $this->validationUrl = config('sslcommerz.validation_url');
        $this->storeId = config('sslcommerz.store_id');
        $this->storePassword = config('sslcommerz.store_password');
    }

    /**
     * Create payment session and get GatewayPageURL for redirect.
     *
     * @param array $params [tran_id, total_amount, currency, success_url, fail_url, cancel_url, cus_name, cus_email, cus_phone, product_name, product_category, ...]
     * @return array{success: bool, gateway_url?: string, session_key?: string, error?: string}
     */
    public function createSession(array $params): array
    {
        if (empty($this->storeId) || empty($this->storePassword)) {
            Log::warning('SSLCommerz: store_id or store_password not configured');
            return ['success' => false, 'error' => 'Payment gateway is not configured.'];
        }

        $payload = array_merge([
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'total_amount' => $params['total_amount'],
            'currency' => $params['currency'] ?? 'BDT',
            'tran_id' => $params['tran_id'],
            'success_url' => $params['success_url'],
            'fail_url' => $params['fail_url'],
            'cancel_url' => $params['cancel_url'],
            'cus_name' => $params['cus_name'] ?? 'Customer',
            'cus_email' => $params['cus_email'] ?? 'customer@example.com',
            'cus_phone' => $params['cus_phone'] ?? '00000000000',
            'cus_add1' => $params['cus_add1'] ?? 'N/A',
            'cus_city' => $params['cus_city'] ?? 'N/A',
            'cus_country' => $params['cus_country'] ?? 'Bangladesh',
            'product_name' => $params['product_name'] ?? 'Payment',
            'product_category' => $params['product_category'] ?? 'General',
            'product_profile' => 'general',
            'shipping_method' => 'NO',
            'num_of_item' => 1,
        ], $params);

        try {
            $response = Http::asForm()->timeout(30)->post($this->sessionUrl, $payload);

            if (!$response->successful()) {
                Log::error('SSLCommerz session request failed', ['status' => $response->status(), 'body' => $response->body()]);
                return ['success' => false, 'error' => 'Could not connect to payment gateway.'];
            }

            $data = $response->json();
            $gatewayUrl = $data['GatewayPageURL'] ?? null;
            $sessionKey = $data['sessionkey'] ?? null;

            if (empty($gatewayUrl)) {
                $msg = $data['failedreason'] ?? $data['error'] ?? 'Invalid response from gateway';
                Log::warning('SSLCommerz session no GatewayPageURL', ['response' => $data]);
                return ['success' => false, 'error' => $msg];
            }

            return [
                'success' => true,
                'gateway_url' => $gatewayUrl,
                'session_key' => $sessionKey,
            ];
        } catch (\Throwable $e) {
            Log::error('SSLCommerz createSession exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Payment gateway error. Please try again.'];
        }
    }

    /**
     * Validate transaction with SSLCommerz using val_id from callback.
     *
     * @param string $valId Validation ID from success/fail/cancel redirect
     * @return array{status: string, valid: bool, amount?: float, tran_id?: string, error?: string}
     */
    public function validateTransaction(string $valId): array
    {
        if (empty($this->storeId) || empty($this->storePassword)) {
            return ['valid' => false, 'status' => 'ERROR', 'error' => 'Gateway not configured'];
        }

        try {
            $response = Http::timeout(15)->get($this->validationUrl, [
                'val_id' => $valId,
                'store_id' => $this->storeId,
                'store_passwd' => $this->storePassword,
            ]);

            if (!$response->successful()) {
                Log::error('SSLCommerz validation request failed', ['status' => $response->status()]);
                return ['valid' => false, 'status' => 'ERROR', 'error' => 'Validation request failed'];
            }

            $data = $response->json();
            if (!is_array($data)) {
                return ['valid' => false, 'status' => 'ERROR', 'error' => 'Invalid validation response'];
            }

            $status = strtoupper((string) ($data['status'] ?? ''));
            $valid = $status === 'VALID';
            $amount = isset($data['amount']) ? (float) $data['amount'] : null;
            $tranId = $data['tran_id'] ?? null;

            return [
                'valid' => $valid,
                'status' => $status,
                'amount' => $amount,
                'tran_id' => $tranId,
                'data' => $data,
            ];
        } catch (\Throwable $e) {
            Log::error('SSLCommerz validateTransaction exception', ['message' => $e->getMessage()]);
            return ['valid' => false, 'status' => 'ERROR', 'error' => $e->getMessage()];
        }
    }
}
