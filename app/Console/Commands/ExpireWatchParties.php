<?php

namespace App\Console\Commands;

use App\Models\WatchParty;
use Illuminate\Console\Command;

class ExpireWatchParties extends Command
{
    protected $signature   = 'watch-parties:expire';
    protected $description = 'Mark watch parties that have passed their 7-day expiry as ended';

    public function handle(): void
    {
        $count = WatchParty::whereNotIn('status', ['ended'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['status' => 'ended', 'ended_at' => now()]);

        $this->info("Expired {$count} watch " . ($count === 1 ? 'party' : 'parties') . '.');
    }
}
