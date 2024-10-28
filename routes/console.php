<?php

use App\Console\Commands\createOrUpdateContractsCommand;
use App\Console\Commands\EntriesFindToPassedCommand;
use App\Console\Commands\getPaymentsFromSberBankCommand;
use App\Console\Commands\updateAccessTokenCommand;
use App\Console\Commands\updateAccessTokenSberBankIntegrationCommand;
use App\Console\Commands\updateClientIDSberBankIntegrationCommand;
use App\Console\Commands\WriteOffsFindToPassedCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command(updateAccessTokenCommand::class)->everyMinute();
Schedule::command(WriteOffsFindToPassedCommand::class)->everyMinute();
Schedule::command(EntriesFindToPassedCommand::class)->everyMinute();
Schedule::command(updateAccessTokenSberBankIntegrationCommand::class)->everyFifteenMinutes();
Schedule::command(getPaymentsFromSberBankCommand::class)->everyTenMinutes();
Schedule::command(createOrUpdateContractsCommand::class)->everyThirtyMinutes();
Schedule::command(updateClientIDSberBankIntegrationCommand::class)->cron('0 0 */15 * *');