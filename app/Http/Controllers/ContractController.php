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
        $entries = Contract::select(['bitrix_id', 'title', 'balance', 'recommended_payment', 'previous_period_amount', 'type', 'number', 'date', 'phone','create_deals', 'export_start_date', 'export_week_days', 'export_frequency', 'payment_total', 'payment_type', 'export_total_count', 'attorney_date', 'price', 'price_fruits_vegetables', 'price_bakery', 'price_dairy', 'price_used_oil', 'price_grocery', 'price_waste', 'other', 'city', 'status', 'shipment', 'process', 'supplier_registered', 'retailer', 'region', 'balance_status', 'source']);
        return DataTables::of($entries)
            ->addColumn('title', function($row) {
                return '<a target="_blank" href="https://ip-eko.bitrix24.ru/page/kontragenty/dogovory_s_klientom/type/172/details/'.$row->bitrix_id.'/">'.$row->title.'</a>';
            })
            ->addColumn('export_week_days', function($row) {
                return is_array($row->export_week_days) ? implode(', ', $row->export_week_days) : '';
            })
            ->rawColumns(['title'])
            ->make(true);
    }

    public function getNames(Request $request) {
        $term = $request->get('term', '');
        $contracts = Contract::where('title', 'LIKE', '%' . $term . '%')
            ->pluck('title');
        return response()->json($contracts);
    }
}
