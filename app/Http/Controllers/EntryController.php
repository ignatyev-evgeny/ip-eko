<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntryStoreRequest;
use App\Models\Entry;
use Yajra\DataTables\Facades\DataTables;

class EntryController extends Controller {
    public function list() {
        return view('entry.index', [
            'entries' => Entry::all()
        ]);
    }

    public function data()
    {
        $entries = Entry::select(['status', 'datetime', 'number', 'amount', 'counteragent', 'counteragent_bank_account', 'contract', 'payment_purpose', 'operation_type']);

        return DataTables::of($entries)->make(true);
    }

    public function store(EntryStoreRequest $request) {
        $data = $request->validated();
        $entry = Entry::create($data);
        return response()->json([
            'message' => 'Данные успешно сохранены!',
            'entry' => $entry,
        ], 201);
    }

}
