<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Entry;
use App\Models\Integration;
use App\Models\Supplier;
use App\Models\WriteOff;
use Config;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Log;

class IndexController extends Controller {

    public function index(Request $request) {

        try {

            return view('index', [
                'iframe' => $request->server()['HTTP_SEC_FETCH_DEST'] == 'iframe',
                'contracts' => Contract::count(),
                'writeoffs' => WriteOff::count(),
                'entries' => Entry::count(),
                'clients' => Client::count(),
                'suppliers' => Supplier::count()
            ]);

        } catch (Exception $exception) {
            report($exception);
            abort(500, $exception->getMessage());
        }
    }
}
