<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Integration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class creteOrUpdateClientsCommand extends Command {
    protected $signature = 'clients:crete-or-update';

    protected $description = 'Command description';

    public int $smartProcess;
    public int $smartProcessCities;
    public int|null $start;
    public int|null $startCity;
    public string $domain;
    public string $endpointItems;
    public string $endpointFields;

    public function handle(): void {

        $this->smartProcess = 180;
        $this->smartProcessCities = 154;
        $this->domain = 'ip-eko.bitrix24.ru';
        $this->endpointItems = 'crm.item.list';
        $this->endpointFields = 'crm.item.fields';
        $this->start = 0;
        $this->startCity = 0;

        $integration = Integration::find(1);

        $cities = [];
        do {

            $responseCity = Http::get('https://'.$this->domain.'/rest/'.$this->endpointItems, [
                'entityTypeId' => $this->smartProcessCities,
                'auth' => $integration->accessToken,
                'start' => $this->startCity
            ]);

            if ($responseCity->failed()) {
                $responseCityObject = $responseCity->object();
                $errorShort = ! empty($responseCityObject->error) ? $responseCityObject->error : '';
                $errorDescription = ! empty($responseCityObject->error_description) ? $responseCityObject->error_description : '';
                $message = 'Ошибка соединения с порталом. Получен статус код: '.$responseCity->status().". Причина: $errorShort - $errorDescription";
                $this->errorLog($message);
                return;
            }

            $responseCityObject = $responseCity->object();

            foreach ($responseCityObject->result->items as $cityObject) {
                $cities[$cityObject->id] = $cityObject->title;
            }

            $this->startCity = isset($responseCityObject->next) ? $responseCityObject->next : null;

        } while (isset($responseCityObject->next));

        do {

            $response = Http::get('https://'.$this->domain.'/rest/'.$this->endpointItems, [
                'entityTypeId' => $this->smartProcess,
                'auth' => $integration->accessToken,
                'start' => $this->start,
            ]);


            if ($response->failed()) {
                $responseObject = $response->object();
                $errorShort = ! empty($responseObject->error) ? $responseObject->error : '';
                $errorDescription = ! empty($responseObject->error_description) ? $responseObject->error_description : '';
                $message = 'Ошибка соединения с порталом. Получен статус код: '.$response->status().". Причина: $errorShort - $errorDescription";
                $this->errorLog($message);
                return;
            }

            $responseObject = $response->object();

            foreach ($responseObject->result->items as $client) {

                Client::updateOrCreate([
                    'bitrix_id' => $client->id,
                ], [
                    'name' => $client->title,
                    'phone' => $client->ufCrm5_1708327292587 ?? null,
                    'city' => !empty($client->ufCrm5_1708324513) ? $cities[$client->ufCrm5_1708324513] : null,
                ]);

            }


            $this->start = isset($responseObject->next) ? $responseObject->next : null;

        } while (isset($responseObject->next));

    }

    private function log(string $message): void {
        $this->info($message);
    }

    private function errorLog(string $error): void {
        $this->error($error);
    }
}
