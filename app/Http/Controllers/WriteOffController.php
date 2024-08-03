<?php

namespace App\Http\Controllers;

use App\Http\Requests\WriteOffRequest;
use App\Models\WriteOff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class WriteOffController extends Controller {
    public function list(Request $request) {
        return view('writeOff.index', [
            'writeOffs' => WriteOff::all(),
            'iframe' => $request->server()['HTTP_SEC_FETCH_DEST'] == 'iframe',
        ]);
    }

    public function data()
    {
        $writeOffs = WriteOff::select(['id', 'external', 'store', 'date', 'total_weight', 'total_amount', 'counteragent', 'contract', 'retailer']);

        return DataTables::of($writeOffs)
            ->addColumn('date', function($row) {
                return Carbon::parse($row->datetime)->format('d/m/Y');
            })
            ->addColumn('total_amount', function($row) {
                return number_format($row->total_amount, 2, '.', ' ').' ₽';
            })
            ->addColumn('total_weight', function($row) {
                return number_format($row->total_weight, 2, '.', ' ').' кг.';
            })
            ->addColumn('actions', function($row) {
                return '<button data-write-off-id="'.$row->id.'" type="button" class="btn btn-warning m-0 text-start changeRow"><i class="fa-solid fa-pen-to-square"></i></button>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function detail(WriteOff $writeoff)
    {
        return response()->json([
            'message' => 'Данные успешно получены!',
            'writeOff' => $writeoff,
        ]);
    }

    public function store(WriteOffRequest $request) {
        $data = $request->validated();

        $data['total_detail'] = [
            'fruits_amount' => $data['fruits_amount'] ?? 0,
            'fruits_weight' => $data['fruits_weight'] ?? 0,
            'bread_amount' => $data['bread_amount'] ?? 0,
            'bread_weight' => $data['bread_weight'] ?? 0,
            'milk_amount' => $data['milk_amount'] ?? 0,
            'milk_weight' => $data['milk_weight'] ?? 0,
            'food_waste_amount' => $data['food_waste_amount'] ?? 0,
            'food_waste_weight' => $data['food_waste_weight'] ?? 0,
            'used_vegetable_oil_amount' => $data['used_vegetable_oil_amount'] ?? 0,
            'used_vegetable_oil_weight' => $data['used_vegetable_oil_weight'] ?? 0,
            'groceries_amount' => $data['groceries_amount'] ?? 0,
            'groceries_weight' => $data['groceries_weight'] ?? 0,
            'other_amount' => $data['other_amount'] ?? 0,
            'other_weight' => $data['other_weight'] ?? 0,
        ];

        if(empty($data['total_weight'])) {
            $data['total_weight'] = $data['total_detail']['fruits_weight'] + $data['total_detail']['bread_weight'] + $data['total_detail']['milk_weight'] + $data['total_detail']['food_waste_weight'] + $data['total_detail']['used_vegetable_oil_weight'] + $data['total_detail']['groceries_weight'] + $data['total_detail']['other_weight'];
            $data['total_amount'] = $data['total_detail']['fruits_amount'] + $data['total_detail']['bread_amount'] + $data['total_detail']['milk_amount'] + $data['total_detail']['food_waste_amount'] + $data['total_detail']['used_vegetable_oil_amount'] + $data['total_detail']['groceries_amount'] + $data['total_detail']['other_amount'];
        }

        $writeoff = WriteOff::create($data);
        return response()->json([
            'message' => 'Данные успешно сохранены!',
            'writeoff' => $writeoff,
        ], 201);
    }

    public function update(WriteOff $writeoff, WriteOffRequest $request) {
        $data = $request->validated();
        $writeoff->update($data);

        return response()->json([
            'message' => 'Списание успешно обновлено',
            'writeoff' => $writeoff,
        ], 201);
    }

    public function delete(Request $request) {

        if(empty($request['writeoffs'])) {
            return response()->json([
                'message' => 'Не выбран ни один элемент для удаления',
            ], 400);
        }

        foreach ($request['writeoffs'] as $entry) {
            WriteOff::where('id', $entry)->delete();
        }

        return response()->json([
            'message' => 'Данные успешно удалены!',
        ]);
    }
}
