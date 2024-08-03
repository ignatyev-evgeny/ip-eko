<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntryStoreRequest;
use App\Models\Entry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EntryController extends Controller {
    public function list(Request $request) {
        return view('entry.index', [
            'entries' => Entry::all(),
            'iframe' => $request->server()['HTTP_SEC_FETCH_DEST'] == 'iframe',
        ]);
    }

    public function data()
    {
        $entries = Entry::select(['id', 'status', 'datetime', 'number', 'amount', 'counteragent', 'counteragent_bank_account', 'contract', 'payment_purpose', 'operation_type']);
        return DataTables::of($entries)
            ->addColumn('datetime', function($row) {
                return Carbon::parse($row->datetime)->format('d/m/Y');
            })
            ->addColumn('amount', function($row) {
                return number_format($row->amount, 2, '.', ' ').' ₽';
            })
            ->addColumn('actions', function($row) {
                return '<button data-entry-id="'.$row->id.'" type="button" class="btn btn-primary m-0 text-start changeRow"><i class="fa-solid fa-pen-to-square"></i></button>';
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
        $entry = Entry::create($data);

        return response()->json([
            'message' => 'Данные успешно сохранены!',
            'entry' => $entry,
        ], 201);
    }

    public function update(Entry $entry, EntryStoreRequest $request) {
        $data = $request->validated();
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

}
