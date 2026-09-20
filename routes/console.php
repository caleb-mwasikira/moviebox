<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('subscriptions:remind')->dailyAt('09:00');
Schedule::command('watch-parties:expire')->daily();
Schedule::call(function () {
    \App\Support\ChunkedUpload::prune();
    \App\Models\VideoUpload::where('updated_at', '<', now()->subHours(48))->delete();
})->daily()->name('chunked-uploads:prune');
