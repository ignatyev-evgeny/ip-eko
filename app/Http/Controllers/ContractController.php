<?php

namespace App\Http\Controllers;

use App\Models\Contract;

class ContractController extends Controller {
    public function list() {
        return view('contract.index', [
            'contracts' => Contract::all()
        ]);
    }
}
