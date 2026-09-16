<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily blog automation: import AI-prepared posts dropped in database/pending-posts/.
// Server must run `php artisan schedule:run` every minute via cron.
Schedule::command('blog:import-pending')->dailyAt('02:00');
