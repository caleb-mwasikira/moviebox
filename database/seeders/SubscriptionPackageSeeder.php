<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPackage;

class SubscriptionPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $packages = [
            [
                'name'              => 'Basic (With Ads)',
                'description'       => 'Great for individuals. Includes ads, 720p streaming on 1 screen.',
                'currency'          => 'KSH',
                'price'             => 20,
                'interval'          => 'week',
                'trial_period_days' => 0,
                'max_screens' => 1,
                'max_resolution' => '720p',
                'has_ads'           => true,
                'download_enabled'  => false,
                'is_active'         => true,
                'sort_order'        => 1,
            ],
            [
                'name'              => 'Standard',
                'description'       => 'Ad-free experience. 1080p (Full HD) streaming on 2 screens simultaneously.',
                'currency'          => 'KSH',
                'price'             => 200,
                'interval'          => 'month',
                'trial_period_days' => 7,
                'max_screens' => 3,
                'max_resolution' => '720p',
                'has_ads'           => false,
                'download_enabled'  => true,
                'is_active'         => true,
                'sort_order'        => 2,
            ],
            [
                'name'              => 'Premium (Ultra HD)',
                'description'       => 'The best experience. 4K + HDR streaming on 4 screens. Offline downloads included.',
                'currency'          => 'KSH',
                'price'             => 1800,
                'interval'          => 'year',
                'trial_period_days' => 30,
                'max_screens' => 10,
                'max_resolution' => '1080p',
                'has_ads'           => false,
                'download_enabled'  => true,
                'is_active'         => true,
                'sort_order'        => 3,
            ],
        ];

        foreach ($packages as $package) {
            SubscriptionPackage::updateOrCreate(
                ['name' => $package['name']],
                $package,
            );
        }
    }
}
