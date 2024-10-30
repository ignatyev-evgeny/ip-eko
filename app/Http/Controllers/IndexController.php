<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Entry;
use App\Models\SberIntegration;
use App\Models\Supplier;
use App\Models\WriteOff;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class IndexController extends Controller {

    public const PLACEMENT_SMART_PROCESS = 'CRM_DYNAMIC_172_DETAIL_TAB';

    public function index(Request $request) {

        try {

            if(isset($request->code)) {

                $issetIntegration = SberIntegration::find(1);

                $client_id = !empty($issetIntegration->client_id) ? $issetIntegration->client_id : '9672';
                $client_secret = !empty($issetIntegration->client_secret) ? $issetIntegration->client_secret : 'je5r6h00';

                $response = Http::withOptions([
                    'cert' => '/var/www/ip-eko.bitrix.expert/html/storage/crt/SBBAPI_9672_7953ec3e-1851-4411-b953-5fd5d168cdc5.pem', '0328Dima',
                    'verify' => false,
                ])->asForm()->post('https://fintech.sberbank.ru:9443/ic/sso/api/v2/oauth/token', [
                    'grant_type' => 'authorization_code',
                    'code' => $request->code,
                    'client_id' => $client_id,
                    'redirect_uri' => 'https://ip-eko.bitrix.expert',
                    'client_secret' => $client_secret,
                ]);

                $data = $response->object();

                SberIntegration::updateOrCreate([
                    'id' => 1
                ], [
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'scope' => $data->scope,
                    'access_token' => $data->access_token,
                    'refresh_token' => $data->refresh_token,
                    'response' => $data,
                ]);

            }

            if($request->PLACEMENT == self::PLACEMENT_SMART_PROCESS) {
                $placement = json_decode($request->PLACEMENT_OPTIONS, true)['ID'];

                $contract = Contract::where('bitrix_id', $placement)->first();
                $contract->load('transactions');

                return view('contract.history', [
                    'iframe' => $request->server()['HTTP_SEC_FETCH_DEST'] == 'iframe',
                    'contract' => $contract,
                    'transactions' => $contract->transactions,
                    'balanceFrame' => true
                ]);
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
            report($exception);
            abort(500, $exception->getMessage());
        }
    }
}
