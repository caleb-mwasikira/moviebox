<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class MpesaService
{
    private string $consumerKey;
    private string $consumerSecret;
    private string $shortcode;
    private string $passkey;
    private string $callbackUrl;
    private string $baseUrl;

    public function __construct()
    {
        $this->consumerKey    = config('services.mpesa.consumer_key');
        $this->consumerSecret = config('services.mpesa.consumer_secret');
        $this->shortcode      = config('services.mpesa.shortcode');
        $this->passkey        = config('services.mpesa.passkey');
        $this->callbackUrl    = config('services.mpesa.callback_url');
        $this->baseUrl        = config('services.mpesa.sandbox')
            ? 'https://sandbox.safaricom.co.ke'
            : 'https://api.safaricom.co.ke';
    }

    public function getAccessToken(): string
    {
        $cacheKey = 'mpesa_access_token_' . md5($this->consumerKey);

        return Cache::remember($cacheKey, 3300, function () {
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->get("{$this->baseUrl}/oauth/v1/generate", ['grant_type' => 'client_credentials']);

            if ($response->failed()) {
                throw new \RuntimeException('Failed to get M-Pesa access token: ' . $response->body());
            }

            return $response->json('access_token');
        });
    }

    public function stkPush(string $phone, int $amount, string $accountRef, string $description): array
    {
        $token     = $this->getAccessToken();
        $timestamp = now()->format('YmdHis');
        $password  = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/mpesa/stkpush/v1/processrequest", [
                'BusinessShortCode' => $this->shortcode,
                'Password'          => $password,
                'Timestamp'         => $timestamp,
                'TransactionType'   => 'CustomerPayBillOnline',
                'Amount'            => $amount,
                'PartyA'            => $phone,
                'PartyB'            => $this->shortcode,
                'PhoneNumber'       => $phone,
                'CallBackURL'       => $this->callbackUrl,
                'AccountReference'  => substr($accountRef, 0, 12),
                'TransactionDesc'   => substr($description, 0, 13),
            ]);

        return $response->json() ?? [];
    }
}
