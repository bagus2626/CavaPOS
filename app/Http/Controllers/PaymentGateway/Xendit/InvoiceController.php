<?php

namespace App\Http\Controllers\PaymentGateway\Xendit;

use App\Http\Controllers\Controller;
use App\Models\Xendit\XenditInvoice;
use App\Services\XenditService;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    protected $xendit;

    public function __construct(XenditService $xendit)
    {
        $this->xendit = $xendit;
    }

    public function createInvoice(string $bookingOrderId, string $xenditSubAccount = null, string $xenditSplitRule = null, array $payload)
    {
        try {
            $response = $this->xendit->createInvoice($xenditSubAccount, $xenditSplitRule, $payload);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create invoice',
                    'errors' => $response->json(),
                ], $response->status());
            }

            $invoiceData = $response->json();

            XenditInvoice::create([
                'order_id' => $bookingOrderId,
                'xendit_invoice_id' => $invoiceData['id'],
                'external_id' => $invoiceData['external_id'],
                'amount' => $invoiceData['amount'],
                'status' => $invoiceData['status'],
                'invoice_url' => $invoiceData['invoice_url'],
                'raw_response' => $invoiceData,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully',
                'data' => $invoiceData,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to create invoice with split rules',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAllInvoices(string $subAccountId = null, array $params = [])
    {
        try {
            $response = $this->xendit->getAllInvoices($subAccountId, $params);

            Log::info('DEBUG XENDIT INVOICES REQUEST', [
                'account_id' => $subAccountId,
                'params' => $params,
                'response' => $response,
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch invoices from Xendit',
                    'errors' => $response->json(),
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoices fetched successfully',
                'data' => $response->json(),
            ]);
        } catch (\Throwable $e) {
            Log::error('XENDIT INVOICE ERROR', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getInvoiceById(string $subAccountId = null, string $invoiceId = null)
    {
        try {
            $response = $this->xendit->getInvoiceById($subAccountId, $invoiceId);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch invoice from Xendit',
                    'errors' => $response->json(),
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoice fetched successfully',
                'data' => $response->json(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}