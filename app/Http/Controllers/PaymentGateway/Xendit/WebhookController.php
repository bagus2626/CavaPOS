<?php

namespace App\Http\Controllers\PaymentGateway\Xendit;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Transaction\OrderPayment;
use App\Models\Xendit\SplitRule;
use App\Models\Xendit\XenditInvoice;
use App\Models\Xendit\XenditPayout;
use App\Models\Xendit\XenditSplitTransaction;
use App\Models\Xendit\XenditSubAccount;
use App\Models\Xendit\XenditWebhookLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Events\OrderCreated;
use App\Models\Transaction\BookingOrder;

class WebhookController extends Controller
{
    public function invoice(Request $request)
    {
        DB::beginTransaction();
        try {
            $payload = $request->all();

            $callbackToken = $request->header('x-callback-token');
            $expectedToken = config('services.xendit.callback_token');

            if ($expectedToken && $callbackToken !== $expectedToken) {
                return response()->json(['success' => false, 'message' => 'Invalid token'], 403);
            }

            XenditWebhookLog::create([
                'xendit_id' => data_get($payload, 'id'),
                'status'    => data_get($payload, 'status'),
                'event'     => data_get($payload, 'event', 'invoice.status'),
                'payload'   => $payload,
            ]);

            $xenditInvoice = XenditInvoice::with('order')->where('xendit_invoice_id', data_get($payload, 'id'))->first();
            $xenditInvoice->update([
                'status'         => data_get($payload, 'status'),
                'payment_method' => data_get($payload, 'payment_method'),
            ]);

            OrderPayment::where('booking_order_id', $xenditInvoice->order_id)
                ->update(['payment_status' => data_get($payload, 'status')]);

            if (data_get($payload, 'status') === 'PAID') {
                BookingOrder::where('id', $xenditInvoice->order_id)
                ->update([
                    'order_status' => data_get($payload, 'status'),
                    'payment_flag'  => true,
                ]);
            }

            $bookingOrder = $xenditInvoice->order;

            DB::commit();
            DB::afterCommit(function () use ($bookingOrder) {
                event(new OrderCreated($bookingOrder));
            });

            return response()->json([
                'success' => true,
                'message' => "Webhook invoice processed",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Xendit Webhook error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function splitPaymentStatus(Request $request)
    {
        try {
            $payload = $request->all();

            $callbackToken = $request->header('x-callback-token');
            $expectedToken = config('services.xendit.callback_token');

            if ($expectedToken && $callbackToken !== $expectedToken) {
                return response()->json(['success' => false, 'message' => 'Invalid token'], 403);
            }

            $splitData = data_get($payload, 'data');

            XenditWebhookLog::create([
                'xendit_id' => data_get($splitData, 'id'),
                'status'    => data_get($splitData, 'status'),
                'event'     => data_get($payload, 'event'),
                'payload'   => $payload,
            ]);

            $xenditInvoice = XenditInvoice::where('external_id', data_get($splitData, 'payment_reference_id'))->first();
            $subAccount = XenditSubAccount::where('xendit_user_id', data_get($splitData, 'destination_account_id'))->first();
            $splitRule = SplitRule::where('id', data_get($splitData, 'split_rule_id'))->first();

            $percentAmount = null;

            if ($splitRule && $splitRule->routes) {
                $routes = json_decode($splitRule->routes, true);
                foreach ($routes as $route) {
                    if (
                        isset($route['destination_account_id']) &&
                        $route['destination_account_id'] === data_get($splitData, 'destination_account_id')
                    ) {
                        $percentAmount = $route['percent_amount'] ?? null;
                        break;
                    }
                }
            }

            XenditSplitTransaction::create([
                'xendit_invoice_id'         => $xenditInvoice->xendit_invoice_id,
                'split_rule_id'             => data_get($splitData, 'split_rule_id'),
                'xendit_split_payment_id'   => data_get($splitData, 'id'),
                'reference_id'              => data_get($splitData, 'reference_id'),
                'payment_id'                => data_get($splitData, 'payment_id'),
                'payment_reference_id'      => data_get($splitData, 'payment_reference_id'),
                'source_account_id'         => data_get($splitData, 'source_account_id'),
                'destination_account_id'    => data_get($splitData, 'destination_account_id'),
                'account_type'              => $subAccount ? 'SUB_ACCOUNT' : 'MASTER',
                'amount'                    => data_get($splitData, 'amount'),
                'percentage'                => $percentAmount,
                'status'                    => data_get($splitData, 'status'),
                'currency'                  => data_get($splitData, 'currency'),
                'raw_response'              => $payload,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Webhook invoice processed",
            ]);
        } catch (\Throwable $e) {
            Log::error('Xendit Webhook error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function payout(Request $request)
    {
        try {
            $payload = $request->all();

            $callbackToken = $request->header('x-callback-token');
            $expectedToken = config('services.xendit.callback_token');

            if ($expectedToken && $callbackToken !== $expectedToken) {
                return response()->json(['success' => false, 'message' => 'Invalid token'], 403);
            }

            $payoutData = data_get($payload, 'data');

            XenditWebhookLog::create([
                'xendit_id' => data_get($payoutData, 'id'),
                'status'    => data_get($payoutData, 'status'),
                'event'     => data_get($payload, 'event'),
                'payload'   => $payload,
            ]);

            $payoutId = data_get($payoutData, 'id');
            $existing = XenditPayout::where('payout_id', $payoutId)->first();

            $dataToSave = [
                'payout_id'           => $payoutId,
                'reference_id'        => data_get($payoutData, 'reference_id'),
                'idempotency_key'     => data_get($payoutData, 'idempotency_key'),
                'business_id'         => data_get($payoutData, 'business_id'),
                'amount'              => data_get($payoutData, 'amount', 0),
                'currency'            => data_get($payoutData, 'currency', 'IDR'),
                'channel_code'        => data_get($payoutData, 'channel_code'),
                'channel_category'    => data_get($payoutData, 'channel_category'),
                'connector_reference' => data_get($payoutData, 'connector_reference'),
                'status'              => data_get($payoutData, 'status', 'PENDING'),
                'description'         => data_get($payoutData, 'description'),
                'failure_code'        => data_get($payoutData, 'failure_code'),
                'account_holder_name' => data_get($payoutData, 'channel_properties.account_holder_name'),
                'account_number'      => data_get($payoutData, 'channel_properties.account_number'),
                'account_type'        => data_get($payoutData, 'channel_properties.account_type'),
                'email_to'            => data_get($payoutData, 'receipt_notification.email_to'),
                'email_cc'            => data_get($payoutData, 'receipt_notification.email_cc'),
                'email_bcc'           => data_get($payoutData, 'receipt_notification.email_bcc'),
                'metadata'            => data_get($payoutData, 'metadata'),
                'estimated_arrival_time' => data_get($payoutData, 'estimated_arrival_time'),
                'created_xendit'         => data_get($payoutData, 'created'),
                'updated_xendit'         => data_get($payoutData, 'updated'),
                'raw_response'        => $payload,
            ];

            if ($existing) {
                $existing->update([
                    'status'              => data_get($payoutData, 'status', 'PENDING'),
                    'description'         => data_get($payoutData, 'description'),
                    'failure_code'        => data_get($payoutData, 'failure_code'),
                    'idempotency_key'     => data_get($payoutData, 'idempotency_key'),
                    'channel_category'    => data_get($payoutData, 'channel_category'),
                    'connector_reference' => data_get($payoutData, 'connector_reference'),
                    'updated_xendit'      => data_get($payoutData, 'updated'),
                    'estimated_arrival_time' => data_get($payoutData, 'estimated_arrival_time'),
                ]);
                $message = 'Payout status updated successfully';
            } else {
                XenditPayout::create($dataToSave);
                $message = 'Payout created successfully';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (\Throwable $e) {
            Log::error('Xendit Payout Webhook error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function ownedAccountCreated(Request $request)
    {
        try {
            $payload = $request->all();

            $callbackToken = $request->header('x-callback-token');
            $expectedToken = config('services.xendit.callback_token');

            if ($expectedToken && $callbackToken !== $expectedToken) {
                return response()->json(['success' => false, 'message' => 'Invalid token'], 403);
            }

            $payoutData = data_get($payload, 'data');

            XenditWebhookLog::create([
                'xendit_id' => data_get($payoutData, 'id'),
                'status'    => data_get($payoutData, 'status'),
                'event'     => data_get($payload, 'event'),
                'payload'   => $payload,
            ]);

            $xenditSubAccount = XenditSubAccount::where('xendit_user_id', data_get($payoutData, 'id'))->first();
            $xenditSubAccount->update([
                'status'            => data_get($payoutData, 'status'),
                'payments_enabled'  => true,
                'updated_xendit'    => Carbon::parse(data_get($payoutData, 'created')),
            ]);

            $owner = Owner::findOrFail($xenditSubAccount->partner_id);
            if ($owner) {
                $owner->update([
                    'xendit_registration_status' => data_get($payoutData, 'status'),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Webhook sub account processed",
            ]);
        } catch (\Throwable $e) {
            Log::error('Xendit Webhook error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function managedAccountUpdated(Request $request)
    {
        try {
            $payload = $request->all();

            $callbackToken = $request->header('x-callback-token');
            $expectedToken = config('services.xendit.callback_token');

            if ($expectedToken && $callbackToken !== $expectedToken) {
                return response()->json(['success' => false, 'message' => 'Invalid token'], 403);
            }

            $payoutData = data_get($payload, 'data');
            $accounInfoData = data_get($payoutData, 'account_info');
            $event = data_get($payload, 'event');

            XenditWebhookLog::create([
                'xendit_id' => data_get($payoutData, 'user_id'),
                'status'    => in_array($event, ['account.registered', 'account.activated']) ? 'REGISTERED' : null,
                'event'     => data_get($payload, 'event'),
                'payload'   => $payload,
            ]);

            $xenditInvoice = XenditSubAccount::where('xendit_user_id', data_get($payoutData, 'user_id'))->first();
            $xenditInvoice->update([
                'master_acc_business_id'   => data_get($payload, 'master_acc_business_id'),
                'payments_enabled'         => data_get($accounInfoData, 'payments_enabled'),
                'updated_xendit'           => Carbon::parse(data_get($payoutData, 'created')),

            ]);

            $owner = Owner::findOrFail($xenditInvoice->partner_id);
            if ($owner && in_array($event, ['account.registered', 'account.activated'])) {
                $owner->update([
                    'xendit_registration_status' => 'REGISTERED',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Webhook sub account processed",
            ]);
        } catch (\Throwable $e) {
            Log::error('Xendit Webhook error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

}
