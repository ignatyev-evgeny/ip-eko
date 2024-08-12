<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Entry;
use App\Models\Supplier;
use App\Models\WriteOff;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class IndexController extends Controller {

    public function index(Request $request) {




        try {


            if(isset($request->code)) {
                $response = Http::withOptions([
                    'cert' => '/var/www/ip-eko.bitrix.expert/html/storage/crt/cert.pem', 'testtest',
                    'ssl_key' => '/var/www/ip-eko.bitrix.expert/html/storage/crt/key.pem', 'testtest',
                    'verify' => false,
                ])->post('https://iftfintech.testsbi.sberbank.ru:9443/ic/sso/api/v2/oauth/token', [
                    'grant_type' => 'authorization_code',
                    'code' => $request->code,
                    'client_id' => '13286',
                    'redirect_uri' => 'https://ip-eko.bitrix.expert/',
                    'client_secret' => 'SAND66430PILE',
                ]);
                dd($response->body());
            }

            return view('index', [
                'iframe' => $request->server()['HTTP_SEC_FETCH_DEST'] == 'iframe',
                'contracts' => Contract::count(),
                'writeoffs' => WriteOff::count(),
                'entries' => Entry::count(),
                'clients' => Client::count(),
                'suppliers' => Supplier::count()
            ]);

        } catch (Exception $exception) {
            dd($exception);
            report($exception);
            abort(500, $exception->getMessage());
        }
    }
}
