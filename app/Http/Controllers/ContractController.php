<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractsBalanceHistory;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ContractController extends Controller {
    public function list(Request $request) {
        return view('contract.index', [
            'contracts' => Contract::all(),
            'iframe' => $request->server()['HTTP_SEC_FETCH_DEST'] == 'iframe',
        ]);
    }

    public function getRetailers()
    {
        $retailers = Contract::select('retailer')->distinct()->pluck('retailer');
        return response()->json($retailers);
    }

    public function data()
    {
        $entries = Contract::select(['id', 'shop', 'bitrix_id', 'local_balance', 'title', 'balance', 'recommended_payment', 'previous_period_amount', 'type', 'number', 'date', 'phone','create_deals', 'export_start_date', 'export_week_days', 'export_frequency', 'payment_total', 'payment_type', 'export_total_count', 'attorney_date', 'price', 'price_fruits_vegetables', 'price_bakery', 'price_dairy', 'price_used_oil', 'price_grocery', 'price_waste', 'other', 'city', 'status', 'shipment', 'process', 'supplier_registered', 'retailer', 'region', 'balance_status', 'source']);
        return DataTables::of($entries)

            ->addColumn('title', function($row) {
                return '<a target="_blank" href="https://ip-eko.bitrix24.ru/page/kontragenty/dogovory_s_klientom/type/172/details/'.$row->bitrix_id.'/">'.$row->title.'</a>';
            })

            ->addColumn('export_week_days', function($row) {
                return is_array($row->export_week_days) ? implode('<br>', $row->export_week_days) : '';
            })

            ->addColumn('local_balance', function($row) {
                $amount = $row->local_balance ? number_format($row->local_balance, 2, '.', ' ') : 0.00;
                return $amount.' ₽';
            })

            ->addColumn('balance_history', function($row) {
                return '<a href="' . route('contract.history', ['contract' => $row->id]) . '"><button type="button" class="btn btn-sm btn-warning">История</button></a>';
            })

            ->addColumn('price', function($row) {
                return $row->price.' ₽';
            })

            ->addColumn('price_fruits_vegetables', function($row) {
                return $row->price_fruits_vegetables.' ₽';
            })

            ->addColumn('price_bakery', function($row) {
                return $row->price_bakery.' ₽';
            })

            ->addColumn('price_dairy', function($row) {
                return $row->price_dairy.' ₽';
            })

            ->addColumn('price_used_oil', function($row) {
                return $row->price_used_oil.' ₽';
            })

            ->addColumn('price_grocery', function($row) {
                return $row->price_grocery.' ₽';
            })

            ->addColumn('price_waste', function($row) {
                return $row->price_waste.' ₽';
            })

            ->addColumn('other', function($row) {
                return $row->other.' ₽';
            })


            ->rawColumns([
                'title',
                'balance_history',
                'export_week_days'
            ])
            ->make(true);
    }

    public function getNames(Request $request) {
        $term = $request->get('term', '');
        $contracts = Contract::where('title', 'LIKE', '%' . $term . '%')
            ->orderBy('status', 'ASC')
            ->get();
        $formattedContracts = $contracts->map(function ($contract) {
            return [
                'title' => $contract->status . ' - ' . $contract->title,
                'bitrix_id' => $contract->bitrix_id,
                'retailer' => $contract->retailer,
                'date' => Carbon::parse($contract->date)->format('Y-m-d'),
                'client' => $contract->client,
                'shop' => $contract->shop,
                'shop_address' => $contract->shop_address,
                'price' => [
                    'price' => $contract->price,
                    'price_fruits_vegetables' => $contract->price_fruits_vegetables,
                    'price_bakery' => $contract->price_bakery,
                    'price_dairy' => $contract->price_dairy,
                    'price_used_oil' => $contract->price_used_oil,
                    'price_grocery' => $contract->price_grocery,
                    'price_waste' => $contract->price_waste,
                    'other' => $contract->other,
                ]
            ];
        });
        return response()->json($formattedContracts);
    }

    public function changeBalance(Request $request) {

        DB::beginTransaction();

        try {

            $rowId = $request->input('row');
            $columnId = $request->input('column');
            $value = $request->input('value');

            $contract = Contract::find($rowId);

            if(empty($contract)) {
                return response()->json([
                    'message' => 'Договор не существует'
                ], 404);
            }

            $balanceSnapshot = $contract->local_balance;

            switch ($columnId) {
                case 4:
                    $contract->local_balance = $value;
                    break;
            }

            $contract->save();

            if($balanceSnapshot != $value) {
                ContractsBalanceHistory::create([
                    'contract_id' => $contract->id,
                    'start_balance' => $balanceSnapshot,
                    'amount' => 0,
                    'end_balance' => $value,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Баланс успешно обновлен'
            ]);

        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
            dd($exception);
        }

    }

    public function history(Contract $contract, Request $request) {
        try {
            $contract->load('transactions');
            return view('contract.history', [
                'iframe' => $request->server()['HTTP_SEC_FETCH_DEST'] == 'iframe',
                'contract' => $contract,
                'transactions' => $contract->transactions
            ]);
        } catch (Exception $exception) {
            report($exception);
            dd($exception);
        }
    }
}
