<?php

namespace App\Console\Commands;

use App\Models\SberIntegration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Str;

class updateClientIDSberBankIntegrationCommand extends Command {
    protected $signature = 'integration:update-sber-bank-client-id';

    protected $description = 'Команда для обновления ClientID для работы со СберБанк ID';

    public function handle(): void {

        $integration = SberIntegration::find(1);

        if(empty($integration->client_id)) {
            $this->errorLog('client_id - не определен');
            return;
        }

        if(empty($integration->client_secret)) {
            $this->errorLog('client_secret - не определен');
            return;
        }

        $newClientSecret = Str::random(11);

        $response = Http::withOptions([
            'cert' => '/var/www/ip-eko.bitrix.expert/html/storage/crt/SBBAPI_9672_7953ec3e-1851-4411-b953-5fd5d168cdc5.pem', '0328Dima',
            'verify' => false,
        ])->asForm()->post('https://fintech.sberbank.ru:9443/ic/sso/api/v1/change-client-secret', [
            'access_token' => $integration->access_token,
            'client_id' => $integration->client_id,
            'client_secret' => $integration->client_secret,
            'new_client_secret' => $newClientSecret,
        ]);

        if(!$response->successful()) {
            $this->errorLog('Ошибка при соединении с https://fintech.sberbank.ru:9443/ic/sso/api/v1/change-client-secret. Status: ' . $response->status());
            return;
        }

        $integration->client_secret = $newClientSecret;
        $integration->save();

        $this->log('client_secret успешно получен и обновлен');

    }

    private function log(string $message): void {
        $this->info($message);
    }

    private function errorLog(string $message): void {
        $this->error($message);
    }
}
