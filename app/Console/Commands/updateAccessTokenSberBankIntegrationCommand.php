<?php

namespace App\Console\Commands;

use App\Models\SberIntegration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class updateAccessTokenSberBankIntegrationCommand extends Command {
    protected $signature = 'integration:update-sber-bank';

    protected $description = 'Команда для обновления токенов для работы со СберБанк ID';

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

        $response = Http::withOptions([
            'cert' => '/var/www/ip-eko.bitrix.expert/html/storage/crt/SBBAPI_9672_7953ec3e-1851-4411-b953-5fd5d168cdc5.pem', '0328Dima',
            'verify' => false,
        ])->asForm()->post('https://fintech.sberbank.ru:9443/ic/sso/api/v2/oauth/token', [
            'grant_type' => 'refresh_token',
            'client_id' => $integration->client_id,
            'client_secret' => $integration->client_secret,
            'refresh_token' => $integration->refresh_token,
        ]);

        if(!$response->successful()) {
            $this->errorLog('Ошибка при соединении с https://fintech.sberbank.ru:9443/ic/sso/api/v2/oauth/token. Status: ' . $response->status());
            return;
        }

        $data = $response->object();

        if(empty($data->access_token)) {
            $this->errorLog('Ошибка при получении access_token');
            return;
        }

        if(empty($data->refresh_token)) {
            $this->errorLog('Ошибка при получении refresh_token');
            return;
        }

        $integration->access_token = $data->access_token;
        $integration->refresh_token = $data->refresh_token;
        $integration->save();

        $this->log('Новая пара access_token и refresh_token успешно получена и обновлена');

    }

    private function log(string $message): void {
        $this->info($message);
    }

    private function errorLog(string $message): void {
        $this->error($message);
    }
}
