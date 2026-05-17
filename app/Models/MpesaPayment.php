<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MpesaPayment extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_package_id',
        'phone',
        'merchant_request_id',
        'checkout_request_id',
        'amount',
        'status',
        'result_code',
        'result_desc',
        'mpesa_receipt_number',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPackage::class, 'subscription_package_id');
    }
}
