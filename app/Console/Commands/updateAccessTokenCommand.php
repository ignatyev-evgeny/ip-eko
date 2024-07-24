<?php

namespace App\Console\Commands;

use App\Models\Integration;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Log;

class updateAccessTokenCommand extends Command {
    protected $signature = 'bitrix:update-access-token';

    protected $description = 'Команда для обновления токенов приложения';
    private string $clientId;
    private string $clientSecret;

    public function handle(): void {

        $this->clientId = Config::get('services.bitrix24.client_id') ?? 'default_client_id';
        $this->clientSecret = Config::get('services.bitrix24.client_secret') ?? 'default_client_secret';

        if ($this->clientId === 'default_client_id' || $this->clientSecret === 'default_client_secret') {
            Log::critical('Конфигурация client_id или client_secret не установлена.');
            return;
        }

        $integration = Integration::find(1);

        $response = Http::get('https://oauth.bitrix.info/oauth/token', [
            'grant_type' => 'refresh_token',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $integration->refreshToken,
        ]);

        if ($response->failed()) {
            $responseObject = $response->object();
            $errorShort = ! empty($responseObject->error) ? $responseObject->error : '';
            $errorDescription = ! empty($responseObject->error_description) ? $responseObject->error_description : '';
            $message = 'Ошибка соединения с порталом. Получен статус код: '.$response->status().". Причина: $errorShort - $errorDescription";
            Log::critical($message);
            return;
        }

        $tokens = $response->object();

        if (empty($tokens->access_token) || empty($tokens->refresh_token)) {
            $message = 'Ошибка при получении access_token и/или refresh_token';
            Log::critical($message);
            return;
        }

        $integration->accessToken = $tokens->access_token;
        $integration->refreshToken = $tokens->refresh_token;
        $integration->last_update = Carbon::now()->timestamp;
        $integration->save();

    }
}
