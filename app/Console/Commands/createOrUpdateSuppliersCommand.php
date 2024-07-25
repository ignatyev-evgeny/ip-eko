<?php

namespace App\Console\Commands;

use App\Models\Integration;
use App\Models\Supplier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Log;

class createOrUpdateSuppliersCommand extends Command {
    protected $signature = 'suppliers:create-or-update';

    protected $description = 'Команда для получения и создания \ обновления списка поставщиков';
    public int $smartProcess;
    public int $smartProcessCities;
    public int $category;
    public int|null $start;
    public int|null $startCity;
    public string $domain;
    public string $endpointItems;
    public string $endpointFields;

    public function handle(): void {

        $this->smartProcess = 164;
        $this->smartProcessCities = 154;
        $this->category = 13;
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

            foreach ($responseObject->result->items as $supplier) {

                if(!empty($supplier->ufCrm7_1707460523720) && isset($field['ufCrm7_1707460523720'])) {
                    $export_days = [];
                    foreach ($supplier->ufCrm7_1707460523720 as $day) {
                        $export_days[] = $field['ufCrm7_1707460523720'][$day];
                    }
                }

                if(!empty($supplier->ufCrm7_1707460611940) && isset($field['ufCrm7_1707460611940'])) {
                    $supplier_free_days = [];
                    foreach ($supplier->ufCrm7_1707460611940 as $day) {
                        $supplier_free_days[] = $field['ufCrm7_1707460611940'][$day];
                    }
                }


                Supplier::updateOrCreate([
                    'bitrix_id' => $supplier->id,
                ], [
                    'title' => $supplier->title,
                    'email' => $supplier->ufCrm7_1721805960788,
                    'retailer' =>             !empty($supplier->ufCrm7_1707205123136) ? $field['ufCrm7_1707205123136'][$supplier->ufCrm7_1707205123136] : null,
                    'address' => $supplier->ufCrm7_1707203499066,
                    'internal_number' => $supplier->ufCrm7_1707203527385,
                    'external_number' => $supplier->ufCrm7_1707203536241,
                    'region' =>               !empty($supplier->ufCrm7_1707203867159) && isset($field['ufCrm7_1707203867159']) && !empty($field['ufCrm7_1707203867159'][$supplier->ufCrm7_1707203867159]) ? $field['ufCrm7_1707203867159'][$supplier->ufCrm7_1707203867159] : null,
                    'cities' =>               $cities[$supplier->ufCrm7_1707460296] ?? null,
                    'direction' =>            !empty($supplier->ufCrm7_1708323749806) && isset($field['ufCrm7_1708323749806']) && !empty($field['ufCrm7_1708323749806'][$supplier->ufCrm7_1708323749806]) ? $field['ufCrm7_1708323749806'][$supplier->ufCrm7_1708323749806] : null,
                    'type_point' =>           !empty($supplier->ufCrm7_1711617863029) && isset($field['ufCrm7_1711617863029']) && !empty($field['ufCrm7_1711617863029'][$supplier->ufCrm7_1711617863029]) ? $field['ufCrm7_1711617863029'][$supplier->ufCrm7_1711617863029] : null,
                    'type_payment' =>         !empty($supplier->ufCrm7_1707203964164) && isset($field['ufCrm7_1707203964164']) && !empty($field['ufCrm7_1707203964164'][$supplier->ufCrm7_1707203964164]) ? $field['ufCrm7_1707203964164'][$supplier->ufCrm7_1707203964164] : null,
                    'price' => $supplier->ufCrm7_1707203989785,
                    'contract_with' =>        !empty($supplier->ufCrm7_1707204357386) && isset($field['ufCrm7_1707204357386']) && !empty($field['ufCrm7_1707204357386'][$supplier->ufCrm7_1707204357386]) ? $field['ufCrm7_1707204357386'][$supplier->ufCrm7_1707204357386] : null,
                    'legal_name' => $supplier->ufCrm7_1707204613556,
                    'problem' =>              !empty($supplier->ufCrm7_1713429175630) && isset($field['ufCrm7_1713429175630']) && !empty($field['ufCrm7_1713429175630'][$supplier->ufCrm7_1713429175630]) ? $field['ufCrm7_1713429175630'][$supplier->ufCrm7_1713429175630] : null,
                    'export_frequency' =>     !empty($supplier->ufCrm7_1707460432819) && isset($field['ufCrm7_1707460432819']) && !empty($field['ufCrm7_1707460432819'][$supplier->ufCrm7_1707460432819]) ? $field['ufCrm7_1707460432819'][$supplier->ufCrm7_1707460432819] : null,
                    'export_days' =>          $export_days ?? [],
                    'supplier_status' =>      !empty($supplier->ufCrm7_1707204265452) && isset($field['ufCrm7_1707204265452']) && !empty($field['ufCrm7_1707204265452'][$supplier->ufCrm7_1707204265452]) ? $field['ufCrm7_1707204265452'][$supplier->ufCrm7_1707204265452] : null,
                    'supplier_tech_status' => !empty($supplier->ufCrm7_1707898964674) && isset($field['ufCrm7_1707898964674']) && !empty($field['ufCrm7_1707898964674'][$supplier->ufCrm7_1707898964674]) ? $field['ufCrm7_1707898964674'][$supplier->ufCrm7_1707898964674] : null,
                    'supplier_free_days' =>   $supplier_free_days ?? [],
                    'graph_id' => is_array($supplier->ufCrm7_1707460696413) ? implode(', ', $supplier->ufCrm7_1707460696413) : $supplier->ufCrm7_1707460696413,
                    'price_filter' => $supplier->ufCrm7_1713348665513,
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
