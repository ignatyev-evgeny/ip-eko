<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Exception;
use Illuminate\Http\Request;

class invoicesController extends Controller {
    public function list(Request $request) {
        try {
            return view('invoices.list', [
                'iframe' => $request->server()['HTTP_SEC_FETCH_DEST'] == 'iframe',
                'invoices' => Invoice::all()
            ]);
        } catch (Exception $exception) {
            report($exception);
            dd($exception);
        }
    }
}
