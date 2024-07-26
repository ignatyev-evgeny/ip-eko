<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\Integration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use function Laravel\Prompts\select;

class createOrUpdateContractsCommand extends Command {
    protected $signature = 'contracts:create-or-update';

    protected $description = 'Command description';
    public int $smartProcess;
    public int $smartProcessCities;
    public int|null $start;
    public int|null $startCity;
    public string $domain;
    public string $endpointItems;
    public string $endpointFields;
    public function handle(): void {
        $this->smartProcess = 172;
        $this->smartProcessCities = 154;
        $this->domain = 'ip-eko.bitrix24.ru';
        $this->endpointItems = 'crm.item.list';
        $this->endpointFields = 'crm.item.fields';
        $this->start = 0;
        $this->startCity = 0;

        $integration = Integration::find(1);

        $responseFields = Http::get('https://'.$this->domain.'/rest/'.$this->endpointFields, [
            'entityTypeId' => $this->smartProcess,
            'auth' => $integration->accessToken,
        ]);

        if ($responseFields->failed()) {
            $responseFieldsObject = $responseFields->object();
            $errorShort = ! empty($responseFieldsObject->error) ? $responseFieldsObject->error : '';
            $errorDescription = ! empty($responseFieldsObject->error_description) ? $responseFieldsObject->error_description : '';
            $message = 'Ошибка соединения с порталом. Получен статус код: '.$responseFields->status().". Причина: $errorShort - $errorDescription";
            $this->errorLog($message);
            return;
        }

        $responseFieldsObject = $responseFields->object();

        foreach ($responseFieldsObject->result->fields as $key => $responseField) {
            $field[$key] = [];
            if($responseField->type == 'enumeration') {
                foreach ($responseField->items as $item) {
                    $field[$key][$item->ID] = $item->VALUE;
                }
            }
        }

        foreach ($field as $key => $element) {
            if(empty($element)) {
                unset($field[$key]);
            }
        }

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

            foreach ($responseObject->result->items as $contract) {

                if(!empty($supplier->ufCrm9_1707197490488) && isset($field['ufCrm9_1707197490488'])) {
                    $export_week_days = [];
                    foreach ($supplier->ufCrm7_1707460611940 as $day) {
                        $export_week_days[] = $field['ufCrm9_1707197490488'][$day];
                    }
                }

                Contract::updateOrCreate([
                    'bitrix_id' => $contract->id,
                ], [
                    'client_id' => $contract->parentId164,
                    'supplier_id' => $contract->parentId180,
                    'title' => $contract->title,
                    'balance' => $contract->ufCrm9_1706097377335,
                    'recommended_payment' => $contract->ufCrm9_1720768282430,
                    'previous_period_amount' => $contract->ufCrm9_1720768304379,
                    'type' => !empty($contract->ufCrm9_1707227274078) && isset($field['ufCrm9_1707227274078']) ? $field['ufCrm9_1707227274078'][$contract->ufCrm9_1707227274078] : null,
                    'number' => $contract->ufCrm9_1707202290201,
                    'date' => $contract->ufCrm9_1707202457837,
                    'phone' => $contract->ufCrm9_1711361474209,
                    'create_deals' => !empty($contract->ufCrm9_1716544426829) && isset($field['ufCrm9_1716544426829']) ? $field['ufCrm9_1716544426829'][$contract->ufCrm9_1716544426829] : null,
                    'export_start_date' => $contract->ufCrm9_1707197653085,
                    'export_week_days' =>  $export_week_days ?? [],
                    'export_frequency' => !empty($contract->ufCrm9_1707197599915) && isset($field['ufCrm9_1707197599915']) ? $field['ufCrm9_1707197599915'][$contract->ufCrm9_1707197599915] : null,
                    'payment_total' => $contract->ufCrm9_1711618438030,
                    'payment_type' => !empty($contract->ufCrm9_1716544379933) && isset($field['ufCrm9_1716544379933']) ? $field['ufCrm9_1716544379933'][$contract->ufCrm9_1716544379933] : null,
                    'export_total_count' => $contract->ufCrm9_1716544825532,
                    'attorney_date' => $contract->ufCrm9_1711619910858,
                    'price' => $contract->ufCrm9_1707227321644,
                    'price_fruits_vegetables' => $contract->ufCrm9_1720433999899,
                    'price_bakery' => $contract->ufCrm9_1720434029205,
                    'price_dairy' => $contract->ufCrm9_1720434055942,
                    'price_used_oil' => $contract->ufCrm9_1720434142725,
                    'price_grocery' => $contract->ufCrm9_1720434294604,
                    'price_waste' => $contract->ufCrm9_1720433920073,
                    'other' => $contract->ufCrm9_1720433948843,
                    'city' => null,
                    'status' => !empty($contract->ufCrm9_1707197191281) && isset($field['ufCrm9_1707197191281']) ? $field['ufCrm9_1707197191281'][$contract->ufCrm9_1707197191281] : null,
                    'shipment' => $contract->ufCrm9_1708331491083,
                    'process' => $contract->ufCrm9_1707200007012,
                    'supplier_registered' => $contract->ufCrm9_1707468645357,
                    'retailer' => !empty($contract->ufCrm9_1720766998022) && isset($field['ufCrm9_1720766998022']) ? $field['ufCrm9_1720766998022'][$contract->ufCrm9_1720766998022] : null,
                    'region' => $contract->ufCrm9_1719220972,
                    'balance_status' => !empty($contract->ufCrm9_1721899462464) && isset($field['ufCrm9_1721899462464']) ? $field['ufCrm9_1721899462464'][$contract->ufCrm9_1721899462464] : null,
                    'source' => $contract->sourceId,
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
