<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntryStoreRequest;
use App\Models\Contract;
use App\Models\ContractsBalanceHistory;
use App\Models\Entry;
use App\Models\EntryIgnore;
use App\Models\SberIntegration;
use Carbon\Carbon;
use DB;
use Dflydev\DotAccessData\Data;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Str;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class EntryController extends Controller {
    public function list(Request $request, $type = null) {

        $entries = match ($type) {
            'ignored' => EntryIgnore::all(),
            default => Entry::all(),
        };

        return view('entry.index', [
            'entries' => $entries,
            'type' => $type,
            'iframe' => $request->server()['HTTP_SEC_FETCH_DEST'] == 'iframe',
        ]);
    }

    public function data()
    {
        $entries = Entry::select(['id', 'status', 'datetime', 'number', 'amount', 'counteragent', 'counteragent_bank_account', 'contract', 'payment_purpose']);
        return DataTables::of($entries)
            ->addColumn('status', function($row) {

                $status = match ($row->status) {
                    'new' => 'Новое',
                    'duplicate' => 'Дубликат',
                    'passed' => 'Проведено',
                    'contract' => 'Найден',
                    default => $row->status
                };

                return $status;
            })
            ->addColumn('datetime', function($row) {
                return Carbon::parse($row->datetime)->format('Y-m-d');
            })
            ->addColumn('amount', function($row) {
                return number_format($row->amount, 2, '.', ' ').' ₽';
            })
            ->addColumn('actions', function($row) {
                $buttons = '<button data-entry-id="'.$row->id.'" type="button" class="btn btn-success m-1 text-start changeRow"><i class="fas fa-folder-plus"></i></button>';
                $buttons .= '<button data-entry-id="'.$row->id.'" type="button" class="btn btn-primary m-1 text-start viewRow"><i class="fas fa-eye"></i></button>';
                return $buttons;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function detail(Entry $entry)
    {
        return response()->json([
            'message' => 'Данные успешно получены!',
            'entry' => $entry,
        ]);
    }

    public function bank(string $date)
    {
        try {

            $date = empty($date) ? date('Y-m-d') : $date;
            $integration = SberIntegration::find(1);

            if (empty($integration->access_token)) {
                abort(404, 'access_token - не определен');
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

            $transactions = array_filter($allTransactions, function ($transaction) {
                return isset($transaction->direction) && $transaction->direction === 'CREDIT';
            });

            $transactions = array_map(function ($transaction) {
                $transaction->is_found = Entry::where('uuid', $transaction->uuid)->exists();
                return $transaction;
            }, $transactions);

            return view('entry.bank', [
                'transactions' => $transactions,
                'total' => count($transactions)
            ]);

        } catch (Exception $exception) {
            dd($exception);
        }
    }

    public function update(Entry $entry, EntryStoreRequest $request)
    {

        $data = $request->validated();

        if(empty($data['contract'])) {
            return response()->json([
                'message' => 'Договор не указан',
            ], 404);
        }

        if($entry->status == 'passed') {
            return response()->json([
                'message' => 'Запрещено редактировать уже проведенное поступление',
            ], 403);
        }

        $contract = Contract::where('title', "like", "%" . getTextAfterFirstDashIfMatched($data['contract']) . "%")->first();

        if(empty($contract)) {
            return response()->json([
                'message' => 'Указанный договор - ' . $data['contract'] . ' - не найден',
            ], 404);
        }

        $data['contract_id'] = $contract->id;

        $entry->update($data);

        return response()->json([
            'message' => 'Пополнение успешно обновлено',
            'entry' => $entry,
        ], 201);
    }

    public function transfer(Entry $entry, Request $request)
    {

        if($entry->status != 'passed') {
            return response()->json([
                'message' => 'Изменять договор в поступлении можно только на проведенном поступлении',
            ], 404);
        }

        if(empty($request->new_contract)) {
            return response()->json([
                'message' => 'Невозможно перенести платеж, если новый договор не заполнен',
            ], 404);
        }

        if(empty($request->contract)) {
            return response()->json([
                'message' => 'Невозможно перенести платеж, если договор не заполнен',
            ], 404);
        }

        $contract = Contract::where('title', "like", "%" . getTextAfterFirstDashIfMatched($request->contract) . "%")->first();
        $newContract = Contract::where('title', "like", "%" . getTextAfterFirstDashIfMatched($request->new_contract) . "%")->first();

        if(empty($newContract)) {
            return response()->json([
                'message' => 'Невозможно перенести платеж, введенный договор не найден на стороне приложения',
            ], 404);
        }

        if(empty($contract)) {
            return response()->json([
                'message' => 'Невозможно перенести платеж, введенный указанный договор не найден на стороне приложения',
            ], 404);
        }

        $contractBalanceSnapshot = $contract->local_balance;
        $newContractBalanceSnapshot = $newContract->local_balance;

        $contract->local_balance = $contract->local_balance - floatval($entry->amount);
        $contract->save();

        $newContract->local_balance = $newContract->local_balance + floatval($entry->amount);
        $newContract->save();


        ContractsBalanceHistory::create([
            'contract_id' => $contract->id,
            'start_balance' => $contractBalanceSnapshot,
            'amount' => -floatval($entry->amount),
            'end_balance' => $contractBalanceSnapshot - floatval($entry->amount),
        ]);

        ContractsBalanceHistory::create([
            'contract_id' => $newContract->id,
            'start_balance' => $newContractBalanceSnapshot,
            'amount' => floatval($entry->amount),
            'end_balance' => $newContractBalanceSnapshot + floatval($entry->amount),
        ]);

        $entry->contract = $newContract->title;
        $entry->save();

        return response()->json([
            'message' => 'Пополнение успешно перенесено',
            'entry' => $entry,
        ], 201);
    }

    public function delete(Request $request) {

        if(empty($request['entries'])) {
            return response()->json([
                'message' => 'Не выбран ни один элемент для удаления',
            ], 400);
        }

        foreach ($request['entries'] as $entry) {

            if($entry->status == 'passed') {
                continue;
            }

            Entry::find($entry)->delete();
        }

        return response()->json([
            'message' => 'Данные успешно удалены!',
        ]);
    }

    public function passed(Request $request) {

        if(empty($request['entries'])) {
            return response()->json([
                'message' => 'Не выбран ни один элемент для проведения',
            ], 400);
        }

        DB::beginTransaction();

        try {

            foreach ($request['entries'] as $entry) {
                $entry = Entry::find($entry);

                if($entry->status == 'passed') {
                    continue;
                }

                if(empty($entry->contract)) {
                    continue;
                }

                $contract = Contract::where('title', "like", "%" . getTextAfterFirstDashIfMatched($entry->contract) . "%")->first();

                if(empty($contract)) {
                    continue;
                }

                $balanceSnapshot = $contract->local_balance;
                $contract->local_balance = $contract->local_balance + floatval($entry->amount);
                $contract->save();

                $entry->status = 'passed';
                $entry->save();

                ContractsBalanceHistory::create([
                    'type' => 'entry',
                    'type_relation' => $entry->id,
                    'contract_id' => $contract->id,
                    'start_balance' => $balanceSnapshot,
                    'amount' => floatval($entry->amount),
                    'end_balance' => $balanceSnapshot + floatval($entry->amount),
                    'comment' => $entry->payment_purpose ?? null,
                ]);

            }

            DB::commit();

            return response()->json([
                'message' => 'Данные успешно проведены!',
            ]);

        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'При проведении некоторых поступлений, произошла ошибка!',
            ], 500);
        }


    }

    public function ignore(Request $request, $type = 'false')
    {
        DB::beginTransaction();
        try {

            if($type == 'true') {

                if(empty($request['entries'])) {
                    return response()->json([
                        'message' => 'Не выбран ни один элемент для проведения',
                    ], 400);
                }

                foreach ($request['entries'] as $entry) {

                    if($entry->status == 'passed') {
                        continue;
                    }

                    $entryDetail = Entry::find($entry);
                    EntryIgnore::updateOrCreate([
                        'counteragent_bank_account' => $entryDetail->counteragent_bank_account,
                    ], [
                        'counteragent' => $entryDetail->counteragent
                    ]);
                    $entryDetail->delete();
                }

            } else if($type == 'false') {
                $counteragentBankAccount = $request->counteragentBankAccount;
                EntryIgnore::where('counteragent_bank_account', $counteragentBankAccount)->delete();
            }

            DB::commit();

            return response()->json([
                'message' => 'Данные успешно проведены!',
            ]);

        } catch (Throwable $throwable) {
            DB::rollBack();
            report($throwable);
            return response()->json([
                'message' => 'При добавлении контрагента в список игнорирования произошла ошибка!',
            ], 500);
        }
    }

}
