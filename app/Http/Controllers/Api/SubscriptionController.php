<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionPackageResource;
use App\Models\MpesaPayment;
use App\Models\SubscriptionPackage;
use App\Services\MpesaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SubscriptionController extends Controller
{
    public function __construct(private MpesaService $mpesa) {}

    public function packages(): JsonResponse
    {
        $packages = SubscriptionPackage::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => SubscriptionPackageResource::collection($packages),
        ]);
    }

    public function current(Request $request): JsonResponse
    {
        $sub = $request->user()->currentSubscription?->load('package');

        if (!$sub) {
            return response()->json(['data' => null]);
        }

        return response()->json([
            'data' => [
                'package'    => new SubscriptionPackageResource($sub->package),
                'status'     => $sub->status,
                'starts_at'  => $sub->starts_at?->toISOString(),
                'expires_at' => $sub->expires_at?->toISOString(),
            ],
        ]);
    }

    public function initiate(Request $request): JsonResponse
    {
        $request->validate([
            'phone'        => ['required', 'string'],
            'package_uuid' => ['required', 'string', 'exists:subscription_packages,uuid'],
        ]);

        $phone = $this->normalizePhone($request->input('phone'));

        if (!$this->isValidKenyanPhone($phone)) {
            throw ValidationException::withMessages([
                'phone' => 'Enter a valid Kenyan phone number (e.g. 0712 345 678).',
            ]);
        }

        $package = SubscriptionPackage::where('uuid', $request->input('package_uuid'))
            ->where('is_active', true)
            ->firstOrFail();

        $user = $request->user();

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

            return response()->json([
                'success' => false,
                'message' => 'Payment service unavailable. Please try again.',
            ], 503);
        }
    }

    public function status(Request $request): JsonResponse
    {
        $request->validate([
            'checkout_request_id' => ['required', 'string'],
        ]);

        $payment = MpesaPayment::where('checkout_request_id', $request->input('checkout_request_id'))
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json([
            'status'  => $payment->status,
            'receipt' => $payment->mpesa_receipt_number,
            'message' => $payment->result_desc,
        ]);
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        } elseif (str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }
        return $phone;
    }

    private function isValidKenyanPhone(string $phone): bool
    {
        return (bool) preg_match('/^2547\d{8}$/', $phone);
    }
}
