<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ContractController extends Controller {
    public function list(Request $request) {
        return view('contract.index', [
            'contracts' => Contract::all(),
            'iframe' => $request->server()['HTTP_SEC_FETCH_DEST'] == 'iframe',
        ]);
    }

    public function data()
    {
        $entries = Contract::select(['id', 'bitrix_id', 'local_balance', 'title', 'balance', 'recommended_payment', 'previous_period_amount', 'type', 'number', 'date', 'phone','create_deals', 'export_start_date', 'export_week_days', 'export_frequency', 'payment_total', 'payment_type', 'export_total_count', 'attorney_date', 'price', 'price_fruits_vegetables', 'price_bakery', 'price_dairy', 'price_used_oil', 'price_grocery', 'price_waste', 'other', 'city', 'status', 'shipment', 'process', 'supplier_registered', 'retailer', 'region', 'balance_status', 'source']);
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

            ->rawColumns(['title', 'export_week_days'])
            ->make(true);
    }

    public function getNames(Request $request) {
        $term = $request->get('term', '');
        $contracts = Contract::where('title', 'LIKE', '%' . $term . '%')
            ->select('status', 'title', 'bitrix_id')
            ->orderBy('status', 'ASC')
            ->get();
        $formattedContracts = $contracts->map(function ($contract) {
            return $contract->status . ' - ' . $contract->title;
        });
        return response()->json($formattedContracts);
    }

    public function changeBalance(Request $request) {

        $rowId = $request->input('row');  // Получаем ID строки
        $columnId = $request->input('column');  // Получаем индекс колонки
        $value = $request->input('value');  // Получаем новое числовое значение

        $row = Contract::find($rowId);

        switch ($columnId) {
            case 2: // Колонка "Local Balance"
                $row->local_balance = $value;
                break;
        }

        $row->save();

        return response()->json(['message' => 'Баланс успешно обновлен']);

    }

}
