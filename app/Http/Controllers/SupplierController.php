<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller {
    public function list() {
        return view('supplier.index', [
            'suppliers' => Supplier::all()
        ]);
    }
}
