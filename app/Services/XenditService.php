<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class XenditService
{
    protected $secretKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.xendit.base_url'), '/');
        $this->secretKey = config('services.xendit.secret_key');
    }

    public function request(string $method, string $endpoint, array $payload = [], array $headers = [])
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');

        $client = Http::withBasicAuth($this->secretKey, '')
            ->withHeaders($headers);

        if (strtolower($method) === 'get') {
            return $client->get($url, array_filter($payload));
        }

        return $client->{$method}($url, array_filter($payload));
    }

    public function createAccount(array $payload)
    {
        return $this->request('post', 'v2/accounts', $payload);
    }

    public function getSubAccounts(array $filters = [])
    {

        return $this->request('get', 'v2/accounts', $filters);
    }

    public function getSubAccountById(string $id)
    {
        return $this->request('get', "v2/accounts/{$id}");
    }

    public function createInvoice(?string $forUserId = null, ?string $withSplitRule = null, array $payload = [])
    {
        $headers = [];

        if ($forUserId) {
            $headers['for-user-id'] = $forUserId;
        }

        if ($withSplitRule) {
            $headers['with-split-rule'] = $withSplitRule;
        }

        return $this->request('post', 'v2/invoices', $payload, $headers);
    }

    public function getAllInvoices(?string $forUserId = null, array $params = [])
    {
        $headers = [];

        if ($forUserId) {
            $headers['for-user-id'] = $forUserId;
        }

        $query = array_filter($params);

        return $this->request('get', 'v2/invoices', $query, $headers);
    }

    public function getBalance(string $forUserId = null, array $params)
    {
        $headers = [];
        if ($forUserId) {
            $headers['for-user-id'] = $forUserId;
        }

        $query = array_filter([
            'account_type' => $params['account_type'] ?? 'CASH',
            'currency' => $params['currency'] ?? 'IDR',
            'at_timestamp' => $params['at_timestamp'] ?? null,
        ]);

        return $this->request('get', '/balance', $query, $headers);
    }

    public function getTransactions(?string $forUserId = null, array $params = [])
    {
        $headers = [];

        if ($forUserId) {
            $headers['for-user-id'] = $forUserId;
        }

        $query = array_filter($params);

        return $this->request('get', 'transactions', $query, $headers);
    }

    public function getTransactionById(?string $forUserId = null, ?string $id = null)
    {
        $headers = [];

        if ($forUserId) {
            $headers['for-user-id'] = $forUserId;
        }

        return $this->request('get', "transactions/{$id}", [], $headers);
    }

    public function createSplitRule(array $payload)
    {
        return $this->request('post', 'split_rules', $payload);
    }

    public function getPayoutChannels()
    {
        return $this->request('get', "payouts_channels");
    }

    public function createPayout(?string $forUserId = null, ?string $idempotencyKey = null, array $payload = [])
    {
        $headers = [];

        if ($forUserId) {
            $headers['for-user-id'] = $forUserId;
        }

        if ($idempotencyKey) {
            $headers['Idempotency-key'] = $idempotencyKey;
        }

        return $this->request('post', 'v2/payouts', $payload, $headers);
    }

    public function getPayoutById(?string $forUserId = null, ?string $payoutId = null)
    {
        $headers = [];

        if ($forUserId) {
            $headers['for-user-id'] = $forUserId;
        }

        return $this->request('get', "v2/payouts/{$payoutId}", [], $headers);
    }

    public function getInvoiceById(?string $forUserId = null, ?string $id = null)
    {
        $headers = [];

        if ($forUserId) {
            $headers['for-user-id'] = $forUserId;
        }

        return $this->request('get', "v2/invoices/{$id}", [], $headers);
    }
}
