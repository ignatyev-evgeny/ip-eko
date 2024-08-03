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
                return '<button data-write-off-id="'.$row->id.'" type="button" class="btn btn-primary m-0 text-start changeRow"><i class="fa-solid fa-pen-to-square"></i></button>';
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
