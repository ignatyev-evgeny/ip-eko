<?php

namespace App\Http\Controllers;

use App\Http\Requests\WriteOffRequest;
use App\Models\WriteOff;
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
        $writeOffs = WriteOff::select(['external', 'store', 'date', 'total_weight', 'total_amount', 'counteragent', 'contract', 'retailer']);

        return DataTables::of($writeOffs)->make(true);
    }

    public function store(WriteOffRequest $request) {
        $data = $request->validated();
        $entry = WriteOff::create($data);
        return response()->json([
            'message' => 'Данные успешно сохранены!',
            'entry' => $entry,
        ], 201);
    }
}
