<?php

namespace App\Http\Controllers;

use App\Mail\PaymentConfirmed;
use App\Mail\SubscriptionUpdated;
use App\Models\MpesaPayment;
use App\Models\SubscriptionPackage;
use App\Services\MpesaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class MpesaController extends Controller
{
    public function __construct(private MpesaService $mpesa) {}

    public function initiate(Request $request): JsonResponse
    {
        $request->validate([
            'phone'        => ['required', 'string'],
            'package_uuid' => ['required', 'string', 'exists:subscription_packages,uuid'],
        ]);

        $phone = $this->normalizePhone($request->input('phone'));

        if (! $this->isValidKenyanPhone($phone)) {
            throw ValidationException::withMessages([
                'phone' => 'Enter a valid Kenyan phone number (e.g. 0712 345 678).',
            ]);
        }

        $package = SubscriptionPackage::where('uuid', $request->input('package_uuid'))
            ->where('is_active', true)
            ->firstOrFail();

        $user = $request->user();

        // Cancel any previous pending attempt for this package
        $user->mpesaPayments()
            ->where('status', 'pending')
            ->where('subscription_package_id', $package->id)
            ->update(['status' => 'cancelled']);

        $payment = $user->mpesaPayments()->create([
            'subscription_package_id' => $package->id,
            'phone'  => $phone,
            'amount' => $package->price,
            'status' => 'pending',
        ]);

        try {
            $result = $this->mpesa->stkPush(
                $phone,
                $package->price,
                'Kibandasky',
                "Sub: {$package->name}"
            );

            if (($result['ResponseCode'] ?? null) === '0') {
                $payment->update([
                    'merchant_request_id' => $result['MerchantRequestID'],
                    'checkout_request_id' => $result['CheckoutRequestID'],
                ]);

                return response()->json([
                    'success'             => true,
                    'checkout_request_id' => $result['CheckoutRequestID'],
                    'message'             => 'STK push sent. Check your phone and enter your M-Pesa PIN.',
                ]);
            }

            $errMsg = $result['errorMessage'] ?? ($result['ResponseDescription'] ?? 'STK push failed');
            $payment->update(['status' => 'failed', 'result_desc' => $errMsg]);

            return response()->json(['success' => false, 'message' => $errMsg], 422);

        } catch (\Exception $e) {
            $payment->update(['status' => 'failed', 'result_desc' => $e->getMessage()]);
            Log::error('M-Pesa STK push error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Payment service unavailable. Please try again.'], 503);
        }
    }

    public function callback(Request $request): JsonResponse
    {
        $body = $request->all();
        Log::info('M-Pesa callback received', $body);

        $stk = $body['Body']['stkCallback'] ?? null;
        if (! $stk) {
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        $checkoutRequestId = $stk['CheckoutRequestID'] ?? null;
        $resultCode        = $stk['ResultCode'] ?? -1;

        $payment = MpesaPayment::where('checkout_request_id', $checkoutRequestId)->first();
        if (! $payment) {
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        if ($resultCode === 0) {
            $meta = collect($stk['CallbackMetadata']['Item'] ?? [])->pluck('Value', 'Name');

            $payment->update([
                'status'               => 'paid',
                'result_code'          => 0,
                'result_desc'          => 'Success',
                'mpesa_receipt_number' => $meta->get('MpesaReceiptNumber'),
            ]);

            $this->activateSubscription($payment);
        } else {
            $payment->update([
                'status'      => 'failed',
                'result_code' => $resultCode,
                'result_desc' => $stk['ResultDesc'] ?? 'Payment failed',
            ]);
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    public function status(Request $request, string $checkoutRequestId): JsonResponse
    {
        $payment = MpesaPayment::where('checkout_request_id', $checkoutRequestId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $payment) {
            return response()->json(['status' => 'not_found'], 404);
        }

        return response()->json([
            'status'  => $payment->status,
            'message' => $payment->result_desc,
        ]);
    }

    private function activateSubscription(MpesaPayment $payment): void
    {
        $user    = $payment->user;
        $package = $payment->package;

        try {
            DB::transaction(function () use ($user, $package) {
                $user->userSubscriptions()
                    ->where('status', 'active')
                    ->update(['status' => 'expired', 'expires_at' => now()]);

                $expiresAt = match (strtolower($package->interval)) {
                    'week'  => now()->addWeek(),
                    'month' => now()->addMonth(),
                    'year'  => now()->addYear(),
                    default => now()->addMonth(),
                };

                $sub = $user->userSubscriptions()->create([
                    'subscription_package_id' => $package->id,
                    'starts_at'  => now(),
                    'expires_at' => $expiresAt,
                    'status'     => 'active',
                ]);

                $sub->load('package');
                Mail::to($user->email, $user->name)
                    ->queue(new PaymentConfirmed($user, $payment));
                Mail::to($user->email, $user->name)
                    ->queue(new SubscriptionUpdated($user, $sub, 'renewed'));
            });
        } catch (\Exception $e) {
            Log::error("Failed to activate subscription for user {$user->id} after payment {$payment->id}: " . $e->getMessage());
        }
    }

    /**
     * Normalise a Kenyan phone number to 254XXXXXXXXX format.
     * Strips +, leading spaces. Handles 07XX, +2547XX, 2547XX, 7XX.
     */
    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', $phone);
        $phone = ltrim($phone, '+');

        if (str_starts_with($phone, '0') && strlen($phone) === 10) {
            return '254' . substr($phone, 1);
        }

        if (strlen($phone) === 9 && (str_starts_with($phone, '7') || str_starts_with($phone, '1'))) {
            return '254' . $phone;
        }

        return $phone;
    }

    private function isValidKenyanPhone(string $phone): bool
    {
        // Must be 12 digits starting with 2547XX or 2541XX
        return (bool) preg_match('/^254[71]\d{8}$/', $phone);
    }
}
