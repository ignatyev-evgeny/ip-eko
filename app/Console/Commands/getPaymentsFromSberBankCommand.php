<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\Entry;
use App\Models\EntryIgnore;
use App\Models\SberIntegration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Str;

class getPaymentsFromSberBankCommand extends Command {
    protected $signature = 'integration:get-payments-from-sber-bank {date?}';

    protected $description = 'Команда для получения платежей со стороны СберБанк';

    public function handle(): void {

        $date = empty($this->argument('date')) ? date('Y-m-d') : $this->argument('date');

        $integration = SberIntegration::find(1);

        if(empty($integration->access_token)) {
            $this->errorLog('access_token - не определен');
            return;
        }

        $allTransactions = [];
        $page = 1;

        do {

            $response = Http::withToken($integration->access_token)
                ->withOptions([
                    'cert' => '/var/www/ip-eko.bitrix.expert/html/storage/crt/SBBAPI_9672_7953ec3e-1851-4411-b953-5fd5d168cdc5.pem', '0328Dima',
                    'verify' => false,
                ])
                ->get('https://fintech.sberbank.ru:9443/fintech/api/v2/statement/transactions', [
                    'accountNumber' => '40802810828000050817',
                    'statementDate' => $date,
                    'page' => $page
                ]);

            if ($response->status() === 400) {
                break;
            }

            if (!$response->successful()) {
                abort(500, 'Ошибка при соединении с https://fintech.sberbank.ru:9443/fintech/api/v2/statement/transactions. Status: ' . $response->status()." | ".$response->body());
            }

            $data = $response->object();

            if (empty($data->transactions)) {
                break;
            }

            $allTransactions = array_merge($allTransactions, $data->transactions);

            $page++;

        } while (!empty($data->transactions));

        if (empty($allTransactions)) {
            abort(500, 'Транзакции отсутствуют за указанную дату');
        }

        $entries = [];

        foreach ($allTransactions as $transaction) {

            if (!empty($transaction->direction) && $transaction->direction == "CREDIT") {

                if(empty(EntryIgnore::where('counteragent', $transaction->rurTransfer->payerName)->count()) && empty(EntryIgnore::where('counteragent_bank_account', $transaction->rurTransfer->payerAccount)->count())) {

                    $entries[] = [
                        'uuid' => $transaction->uuid,
                        'status' => 'new',
                        'datetime' => $transaction->documentDate,
                        'number' => $transaction->number,
                        'amount' => $transaction->amountRub->amount,
                        'counteragent' => $transaction->rurTransfer->payerName ?? null,
                        'counteragent_bank_account' => $transaction->rurTransfer->payerAccount ?? null,
                        'contract_id' => null,
                        'contract' => null,
                        'payment_purpose' => $transaction->paymentPurpose,
                        'operation_type' => null,
                    ];

                }

            }
        }

        if (!empty($entries)) {

            $uuids = array_column($entries, 'uuid');
            $existingEntries = Entry::whereIn('uuid', $uuids)->get()->keyBy('uuid');
            $filteredEntries = array_filter($entries, function($entry) use ($existingEntries) {
                if ((isset($existingEntries[$entry['uuid']]) && $existingEntries[$entry['uuid']]->status === 'passed') || (isset($existingEntries[$entry['uuid']]) && !empty($existingEntries[$entry['uuid']]->contract))) {
                    return false;
                }
                return true;
            });

            $filteredEntries = array_map(function($entry) {

                if (Str::startsWith($entry['payment_purpose'], 'ЗА ')) {
                    $paymentPurpose = explode(';', $entry['payment_purpose'])[3];
                } else {
                    $paymentPurpose = $entry['payment_purpose'];
                }

                //preg_match('/(?<=№)\d+(?=\/)/', $entry['payment_purpose'], $matches);
                //preg_match('/\d+(?=\/)/', $entry['payment_purpose'], $matches);
                preg_match('/(?<=\/)\d+(?=\/)/', $paymentPurpose, $matches);

                if (!empty($matches)) {
                    //$contract = Contract::where('number', $matches[0])->where('status', 'Активный')->first();
                    $contract = Contract::where('number', $matches[0])->first();
                    $entry['contract_id'] = !empty($contract) ? $contract->id : null;
                    $entry['contract'] = !empty($contract) ? $contract->title : null;
                    $entry['status'] = !empty($contract) ? 'contract' : 'new';
                }
                return $entry;
            }, $filteredEntries);

            if (!empty($filteredEntries)) {
                Entry::upsert(
                    $filteredEntries,
                    [
                        'uuid'
                    ],
                    [
                        'status',
                        'datetime',
                        'number',
                        'amount',
                        'counteragent',
                        'counteragent_bank_account',
                        'contract_id',
                        'contract',
                        'payment_purpose',
                        'operation_type'
                    ]
                );
            }
        }

        $this->log('Данные по оплатам успешно выгружены');

    }

    private function log(string $message): void {
        $this->info($message);
    }

    private function errorLog(string $message): void {
        $this->error($message);
    }
}
