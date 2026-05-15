<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSubscriptions extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_package_id',
        'starts_at',
        'expires_at',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function package()
    {
        return $this->belongsTo(SubscriptionPackage::class, 'subscription_package_id');
    }
}

