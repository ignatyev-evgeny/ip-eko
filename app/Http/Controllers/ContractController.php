<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller {
    public function list() {
        return view('contract.index', [
            'contracts' => Contract::all()
        ]);
    }

    public function getNames(Request $request) {
        $term = $request->get('term', '');
        $contracts = Contract::where('title', 'LIKE', '%' . $term . '%')
            ->pluck('title');
        return response()->json($contracts);
    }
}
