<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntryStoreRequest;
use App\Models\Contract;
use App\Models\ContractsBalanceHistory;
use App\Models\Entry;
use App\Models\EntryIgnore;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
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

    public function store(EntryStoreRequest $request) {
        $data = $request->validated();
        $data['status'] = 'new';
        $data['uuid'] = Str::uuid()->toString();
        $entry = Entry::create($data);

        return response()->json([
            'message' => 'Данные успешно сохранены!',
            'entry' => $entry,
        ], 201);
    }

    public function update(Entry $entry, EntryStoreRequest $request) {
        $data = $request->validated();

        if($entry->status == 'passed') {
            return response()->json([
                'message' => 'Запрещено редактировать уже проведенное поступление',
            ], 403);
        }

        $entry->update($data);

        return response()->json([
            'message' => 'Пополнение успешно обновлено',
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
            Entry::where('id', $entry)->delete();
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
                $entry = Entry::where('id', $entry)->first();

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
                    'contract_id' => $contract->id,
                    'start_balance' => $balanceSnapshot,
                    'amount' => floatval($entry->amount),
                    'end_balance' => $balanceSnapshot + floatval($entry->amount),
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
                    $entryDetail = Entry::where('id', $entry)->first();
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
