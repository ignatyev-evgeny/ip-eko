<?php

use App\Console\Commands\updateAccessTokenCommand;
use App\Console\Commands\updateAccessTokenSberBankIntegrationCommand;
use App\Console\Commands\updateClientIDSberBankIntegrationCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command(updateAccessTokenCommand::class)->everyMinute();
Schedule::command(updateAccessTokenSberBankIntegrationCommand::class)->everyFifteenMinutes();
Schedule::command(updateClientIDSberBankIntegrationCommand::class)->cron('0 0 */15 * *');