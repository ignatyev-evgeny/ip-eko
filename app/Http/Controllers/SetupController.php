<?php

namespace App\Http\Controllers;

use App\Models\Integration;
use Config;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Log;

class SetupController extends Controller {

    protected string $clientId;

    protected string $clientSecret;
    public const PLACEMENT_SMART_PROCESS = 'CRM_DYNAMIC_172_DETAIL_TAB';

    public function __construct()
    {
        $this->clientId = Config::get('services.bitrix24.client_id') ?? 'default_client_id';
        $this->clientSecret = Config::get('services.bitrix24.client_secret') ?? 'default_client_secret';

        if ($this->clientId === 'default_client_id' || $this->clientSecret === 'default_client_secret') {
            Log::critical('Конфигурация client_id или client_secret не установлена.');
        }
    }

    public function setup(Request $request) {

        try {

            if ($this->clientId === 'default_client_id' || $this->clientSecret === 'default_client_secret') {
                $message = 'Невозможно выполнить команду без правильной конфигурации client_id и client_secret.';
                Log::critical($message);
                abort(400, $message);
            }

            if(empty($request['REFRESH_ID'])) {
                $message = 'REFRESH_ID не передан со стороны Bitrix24';
                abort(400, $message);
            }

            $response = Http::get('https://oauth.bitrix.info/oauth/token', [
                'grant_type' => 'refresh_token',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $request['REFRESH_ID'],
            ]);

            if ($response->failed()) {
                $responseObject = $response->object();
                $errorShort = ! empty($responseObject->error) ? $responseObject->error : '';
                $errorDescription = ! empty($responseObject->error_description) ? $responseObject->error_description : '';
                $message = 'Ошибка соединения с порталом '.$request['DOMAIN'].'. Получен статус код: '.$response->status().". Причина: $errorShort - $errorDescription";
                Log::critical($message);
                abort(403, $message);
            }

            $tokens = $response->object();

            if (empty($tokens->access_token) || empty($tokens->refresh_token)) {
                $message = 'Ошибка при получении access_token и/или refresh_token';
                Log::critical($message);
                abort(403, $message);
            }

            Integration::updateOrCreate([
                'id' => 1
            ], [
                'accessToken' => $tokens->access_token,
                'refreshToken' => $tokens->refresh_token,
                'last_update' => Carbon::now()->timestamp,
            ]);

            $availablePlacements = $this->getAvailablePlacements($tokens->access_token, $request['DOMAIN']);

            if(!in_array(self::PLACEMENT_SMART_PROCESS, $availablePlacements)) {
                $message = "Смарт процесс " . self::PLACEMENT_SMART_PROCESS . " не найден на стороне портала - " . $request['DOMAIN'];
                Log::critical($message);
                abort(404, $message);
            }

            $placementBindResponse = $this->bindPlacement(self::PLACEMENT_SMART_PROCESS, $tokens->access_token, $request['DOMAIN']);

            if (!empty($placementBindResponse['error']) && $placementBindResponse['error_description'] == 'Unable to set placement handler: Handler already binded') {
                $this->unbindPlacement(self::PLACEMENT_SMART_PROCESS, $tokens->access_token, $request['DOMAIN']);
                $this->bindPlacement(self::PLACEMENT_SMART_PROCESS, $tokens->access_token, $request['DOMAIN']);
            }

            return view('setup', [
                'iframe' => $request->server()['HTTP_SEC_FETCH_DEST'] == 'iframe',
            ]);

        } catch (Exception $exception) {
            report($exception);
            abort(500, $exception->getMessage());
        }
    }
}
