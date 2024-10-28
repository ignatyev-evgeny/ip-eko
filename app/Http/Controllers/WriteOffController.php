<?php

namespace App\Http\Controllers;

use App\Http\Requests\WriteOffRequest;
use App\Imports\WriteOffsImport;
use App\Models\Contract;
use App\Models\ContractsBalanceHistory;
use App\Models\WriteOff;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class WriteOffController extends Controller {
    public function list(Request $request) {

        if($request->dd == "true") {

            $rows = json_decode('[{"data_provodki":"8/26/2024","dokument_materiala":5984603941,"finansovyi_god":2024,"zakaza_na_otgr":4230377149,"zavod":2910,"naimenovanie_zavoda":"Никулинская 21","podriadcik":80220165,"naimenovanie_podriadcika":"ИП Кучаев Дмитрий Николаевич","material":50000070,"naimenovanie_materiala":"Пищевой отход","kolicestvo":21.5,"bazisnaia_ei":"КГ","vid_dvizeniia":161,"transportnaia_nakladnaia":4230377149,"tekst_zagolovka_dokumenta":"GK/2910","data_vvoda_dok_ta":"8/26/2024","vremia_vvoda":"7:58:21 PM","avtor_dok_ta":"BJOB_FI_ER2","tip_korrektirovki":null,"edi":null,"tabelnyi_nomer":0,"podtverzden_cov":null,"fio":null,"nomer_telefona":null,"nomer_ts":null},{"data_provodki":"8/26/2024","dokument_materiala":5984603941,"finansovyi_god":2024,"zakaza_na_otgr":4230377149,"zavod":2910,"naimenovanie_zavoda":"Никулинская 21","podriadcik":80220165,"naimenovanie_podriadcika":"ИП Кучаев Дмитрий Николаевич","material":50000063,"naimenovanie_materiala":"Молочная гастрономия","kolicestvo":12,"bazisnaia_ei":"КГ","vid_dvizeniia":161,"transportnaia_nakladnaia":4230377149,"tekst_zagolovka_dokumenta":"GK/2910","data_vvoda_dok_ta":"8/26/2024","vremia_vvoda":"7:58:21 PM","avtor_dok_ta":"BJOB_FI_ER2","tip_korrektirovki":null,"edi":null,"tabelnyi_nomer":0,"podtverzden_cov":null,"fio":null,"nomer_telefona":null,"nomer_ts":null},{"data_provodki":"8/26/2024","dokument_materiala":5984568499,"finansovyi_god":2024,"zakaza_na_otgr":4230386180,"zavod":"B051","naimenovanie_zavoda":"Подольск, Юбилейная","podriadcik":80220165,"naimenovanie_podriadcika":"ИП Кучаев Дмитрий Николаевич","material":50000063,"naimenovanie_materiala":"Молочная гастрономия","kolicestvo":12,"bazisnaia_ei":"КГ","vid_dvizeniia":161,"transportnaia_nakladnaia":4230386180,"tekst_zagolovka_dokumenta":"GK/B051","data_vvoda_dok_ta":"8/26/2024","vremia_vvoda":"5:00:53 PM","avtor_dok_ta":"BJOB_FI_ER2","tip_korrektirovki":null,"edi":null,"tabelnyi_nomer":0,"podtverzden_cov":null,"fio":null,"nomer_telefona":null,"nomer_ts":null}]', true);

            foreach ($rows as $row) {

                $price['total'] = $price['base'] = 0.0;
                $contract['title'] = null;
                $status = 'new';

                $weight['fruits'] = match ($row['material']) {
                    50000066 => $row['kolicestvo'],
                    default => null
                };

                $weight['bread'] = match ($row['material']) {
                    50000069 => $row['kolicestvo'],
                    default => null
                };

                $weight['milk'] = match ($row['material']) {
                    50000063 => $row['kolicestvo'],
                    default => null
                };

                $weight['used_oil'] = match ($row['material']) {
                    50000094 => $row['kolicestvo'],
                    default => null
                };

                $weight['other'] = match ($row['material']) {
                    50000070 => $row['kolicestvo'],
                    default => null
                };


                $weight['total'] = $weight['fruits'] + $weight['bread'] + $weight['milk'] + $weight['other'] + $weight['used_oil'];

                $date = Carbon::createFromFormat('m/d/Y', $row['data_provodki'])->format('Y-m-d'); // Ваша дата в формате Y-m-d
                Carbon::setLocale('ru');
                $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
                $dayOfWeek = mb_ucfirst($carbonDate->translatedFormat('l'));

                $contractQuery = Contract::where('status', 'Активный')
                    ->whereJsonContains('export_week_days', $dayOfWeek)
                    ->whereRaw("title LIKE '% / %'")
                    ->where('title', 'LIKE', '%' . $row['zavod'] . '%')
                    ->where('retailer', 'Перекресток');

                if ($contractQuery->exists()) {
                    $contractCount = $contractQuery->count();

                    if ($contractCount > 1) {
                        $status = 'moreOneContract';
                    } else {
                        $contract = $contractQuery->first();
                        $status = 'find';
                        $contract['title'] = $contract->title;
                        $price = [
                            'base' => (float) $contract->price,
                            'fruits' => $weight['fruits'] * (float) $contract->price_fruits_vegetables,
                            'bread' => $weight['bread'] * (float) $contract->price_bakery,
                            'milk' => $weight['milk'] * (float) $contract->price_dairy,
                            'used_oil' => $weight['used_oil'] * (float) $contract->price_used_oil,
                            'other' => $weight['other'] * (float) $contract->price_other,
                        ];
                        $price['total'] = $price['fruits'] + $price['bread'] + $price['milk'] + $price['other'];
                    }
                }

                $data[$row['dokument_materiala']][] = [
                    'find' => [
                        'external' => $row['dokument_materiala'],
                        'store_number' => $row['zavod'],
                        'store' => $row['naimenovanie_zavoda'],
                        'date' => $date,
                        'day_of_week' => $dayOfWeek,
                        'total_weight' => $weight['total'],
                    ],
                    'create' => [
                        'status' => $status,
                        'contract' => $contract['title'],
                        'total_amount' => empty($price['total']) ? $price['base'] * $weight['total'] : $price['total'],
                        'retailer' => 'Перекресток',
                        'total_detail' => [
                            'weight' => [
                                'fruits' => $weight['fruits'],
                                'bread' => $weight['bread'],
                                'milk' => $weight['milk'],
                                'used_oil' => $weight['used_oil'],
                                'other' => $weight['other'],
                            ],
                            'price' => [
                                'base' => $price['base'],
                                'fruits' => $price['fruits'],
                                'bread' => $price['bread'],
                                'milk' => $price['milk'],
                                'used_oil' => $price['used_oil'],
                                'other' => $price['other'],
                            ]
                        ],
                    ]

                ];


            }

            foreach ($this->mergeIfMultiple($data) as $value) {
                dump($value);
            }

            dd($data, $this->mergeIfMultiple($data));
        }

        return view('writeOff.index', [
            'writeOffs' => WriteOff::all(),
            'iframe' => $request->server()['HTTP_SEC_FETCH_DEST'] == 'iframe',
        ]);
    }

    public function data()
    {
        $writeOffs = WriteOff::select(['id', 'status', 'comment', 'external', 'store_number', 'store', 'date', 'day_of_week', 'total_weight', 'total_amount', 'counteragent', 'contract', 'retailer']);

        return DataTables::of($writeOffs)
            ->addColumn('status', function($row) {

                $status = match ($row->status) {
                    'new' => 'Новое',
                    'duplicate' => 'Дубликат',
                    'passed' => 'Проведено',
                    'canceled' => 'Аннулирован',
                    'free' => 'Бесплатный',
                    'moreOneContract' => 'Договор > 1',
                    'find' => 'Найден',
                    default => $row->status
                };

                return $status;
            })
            ->addColumn('date', function($row) {
                return Carbon::parse($row->date)->format('Y-m-d').'<br>'.$row->day_of_week;
            })
            ->addColumn('contract', function($row) {
                return '<a target="_blank" href="/contract/list?search=' . $row->contract . '">' . $row->contract . '</a>';
            })
            ->addColumn('store', function($row) {
                return '<a target="_blank" href="/contract/list?search=' . $row->store_number . '">' . $row->store_number . '</a><br>'.$row->store;
            })
            ->addColumn('total_amount', function($row) {
                return number_format($row->total_amount, 2, '.', ' ').' ₽';
            })
            ->addColumn('total_weight', function($row) {
                $return = number_format($row->total_weight, 2, '.', ' ').' кг.<br>';
                $return .= $row->total_amount > 0 ? '≈'.number_format($row->total_amount / $row->total_weight, 2, '.', ' ')." ₽" : number_format(0, 2, '.', ' ')." ₽";
                return $return;
            })
            ->addColumn('actions', function($row) {
                return '<button data-write-off-id="'.$row->id.'" type="button" class="btn btn-warning m-0 text-start changeRow"><i class="fa-solid fa-pen-to-square"></i></button>';
            })
            ->rawColumns(['actions', 'date', 'store', 'contract', 'total_weight'])
            ->make(true);
    }

    public function detail(WriteOff $writeoff)
    {
        $response = $writeoff->toArray();
        $response['date'] = Carbon::parse($writeoff->date)->format('Y-m-d');

        return response()->json([
            'message' => 'Данные успешно получены!',
            'writeOff' => $response,
        ]);
    }

    public function store(WriteOffRequest $request) {
        $data = $request->validated();

        $data['total_detail'] = [
            'fruits_amount' => $data['fruits_amount'] ?? 0,
            'fruits_weight' => $data['fruits_weight'] ?? 0,
            'bread_amount' => $data['bread_amount'] ?? 0,
            'bread_weight' => $data['bread_weight'] ?? 0,
            'milk_amount' => $data['milk_amount'] ?? 0,
            'milk_weight' => $data['milk_weight'] ?? 0,
            'food_waste_amount' => $data['food_waste_amount'] ?? 0,
            'food_waste_weight' => $data['food_waste_weight'] ?? 0,
            'used_vegetable_oil_amount' => $data['used_vegetable_oil_amount'] ?? 0,
            'used_vegetable_oil_weight' => $data['used_vegetable_oil_weight'] ?? 0,
            'groceries_amount' => $data['groceries_amount'] ?? 0,
            'groceries_weight' => $data['groceries_weight'] ?? 0,
            'other_amount' => $data['other_amount'] ?? 0,
            'other_weight' => $data['other_weight'] ?? 0,
        ];

        if(empty($data['total_weight'])) {
            $data['total_weight'] = $data['total_detail']['fruits_weight'] + $data['total_detail']['bread_weight'] + $data['total_detail']['milk_weight'] + $data['total_detail']['food_waste_weight'] + $data['total_detail']['used_vegetable_oil_weight'] + $data['total_detail']['groceries_weight'] + $data['total_detail']['other_weight'];
            $data['total_amount'] = $data['total_detail']['fruits_amount'] + $data['total_detail']['bread_amount'] + $data['total_detail']['milk_amount'] + $data['total_detail']['food_waste_amount'] + $data['total_detail']['used_vegetable_oil_amount'] + $data['total_detail']['groceries_amount'] + $data['total_detail']['other_amount'];
        }

        $writeoff = WriteOff::create($data);
        return response()->json([
            'message' => 'Данные успешно сохранены!',
            'writeoff' => $writeoff,
        ], 201);
    }

    public function update(WriteOff $writeoff, WriteOffRequest $request) {
        $data = $request->validated();

        if($writeoff->status == 'passed') {
            return response()->json([
                'message' => 'Запрещено редактировать уже проведенное списание',
            ], 403);
        }

        $data['total_detail'] = [
            'weight' => [
                'fruits' => $data['fruits_weight'] ? (float) $data['fruits_weight'] : 0.0,
                'bread' => $data['bread_weight'] ? (float) $data['bread_weight'] : 0.0,
                'milk' => $data['milk_weight'] ? (float) $data['milk_weight'] : 0.0,
                'food_waste' => $data['food_waste_weight'] ? (float) $data['milk_weight'] : 0.0,
                'used_vegetable_oil' => $data['used_vegetable_oil_weight'] ? (float) $data['used_vegetable_oil_weight'] : 0.0,
                'groceries' => $data['groceries_weight'] ? (float) $data['groceries_weight'] : 0.0,
                'other' => $data['other_weight'] ? (float) $data['other_weight'] : 0.0,
            ],
            'price' => [
                'fruits' => !empty($data['fruits_price']) ? (float) $data['fruits_price'] : 0.0,
                'bread' =>  !empty($data['bread_price']) ? (float) $data['bread_price'] : 0.0,
                'milk' => !empty($data['milk_price']) ? (float) $data['milk_price'] : 0.0,
                'food_waste' => !empty($data['food_waste_price']) ? (float) $data['food_waste_price'] : 0.0,
                'used_vegetable_oil' => !empty($data['used_vegetable_oil_price']) ? (float) $data['used_vegetable_oil_price'] : 0.0,
                'groceries' => !empty($data['groceries_price']) ? (float) $data['groceries_price'] : 0.0,
                'other' => !empty($data['other_price']) ? (float) $data['other_price'] : 0.0,
            ],
        ];

        if(empty($data['total_weight'])) {
            $data['total_weight'] = array_sum([
                $data['total_detail']['weight']['fruits'],
                $data['total_detail']['weight']['bread'],
                $data['total_detail']['weight']['milk'],
                $data['total_detail']['weight']['food_waste'],
                $data['total_detail']['weight']['used_vegetable_oil'],
                $data['total_detail']['weight']['groceries'],
                $data['total_detail']['weight']['other'],
            ]);
        }

        if(!empty($data['detail_view']) && $data['detail_view'] == "on" && empty($data['take_contract'])) {

            $data['total_amount'] = array_sum([
                $data['total_detail']['weight']['fruits'] * $data['total_detail']['price']['fruits'],
                $data['total_detail']['weight']['bread'] * $data['total_detail']['price']['bread'],
                $data['total_detail']['weight']['milk'] * $data['total_detail']['price']['milk'],
                $data['total_detail']['weight']['food_waste'] * $data['total_detail']['price']['food_waste'],
                $data['total_detail']['weight']['used_vegetable_oil'] * $data['total_detail']['price']['used_vegetable_oil'],
                $data['total_detail']['weight']['groceries'] * $data['total_detail']['price']['groceries'],
                $data['total_detail']['weight']['other'] * $data['total_detail']['price']['other'],
            ]);

        }

        if(!empty($data['take_contract']) && $data['take_contract'] == "on") {

            $contract = Contract::where('title', "like", getTextAfterFirstDashIfMatched($data['contract']))->first();

            if(empty($contract)) {
                return response()->json([
                    'message' => 'Договор не найден.',
                ], 400);
            }

            if(empty($contract->price)) {
                return response()->json([
                    'message' => 'В договоре не указана базовая стоимость.',
                ], 400);
            }

            $data['total_detail']['price'] = [
                'fruits' => $contract->price_fruits_vegetables ?? 0.0,
                'bread' =>  $contract->price_bakery ?? 0.0,
                'milk' => $contract->price_dairy ?? 0.0,
                'food_waste' => $contract->price_waste ?? 0.0,
                'used_vegetable_oil' => $contract->price_used_oil ?? 0.0,
                'groceries' => $contract->price_grocery ?? 0.0,
                'other' => $contract->other ?? 0.0,
            ];

            $data['total_amount'] = array_sum([
                $data['total_detail']['weight']['fruits'] * $data['total_detail']['price']['fruits'],
                $data['total_detail']['weight']['bread'] * $data['total_detail']['price']['bread'],
                $data['total_detail']['weight']['milk'] * $data['total_detail']['price']['milk'],
                $data['total_detail']['weight']['food_waste'] * $data['total_detail']['price']['food_waste'],
                $data['total_detail']['weight']['used_vegetable_oil'] * $data['total_detail']['price']['used_vegetable_oil'],
                $data['total_detail']['weight']['groceries'] * $data['total_detail']['price']['groceries'],
                $data['total_detail']['weight']['other'] * $data['total_detail']['price']['other'],
            ]);

            $data['status'] = 'find';

            $data['total_amount'] = empty($data['total_amount']) ? (float) $data['total_weight'] * (float) $contract->price : $data['total_amount'];
        }

        $data['total_weight'] = empty($data['total_weight']) ? 0.0 : $data['total_weight'];
        $data['total_amount'] = empty($data['total_amount']) ? 0.0 : $data['total_amount'];


        $writeoff->update($data);

        return response()->json([
            'message' => 'Списание успешно обновлено',
            'writeoff' => $writeoff,
        ], 201);
    }

    public function passed(Request $request) {

        if(empty($request['writeoffs'])) {
            return response()->json([
                'message' => 'Не выбран ни один элемент для проведения',
            ], 400);
        }

        DB::beginTransaction();

        try {

            foreach ($request['writeoffs'] as $entry) {

                $entry = WriteOff::where('id', $entry)->first();

                if($entry->status == 'passed') {
                    continue;
                }

                if(empty($entry->contract)) {
                    continue;
                }

                $contract = Contract::where('title', "like", "%" . getTextAfterFirstDashIfMatched($entry->contract) . "%")->first();

                if(empty($contract)) {
                    continue;
                }

                $balanceSnapshot = $contract->local_balance;
                $contract->local_balance = $contract->local_balance - $entry->total_amount;
                $contract->save();

                $entry->status = 'passed';
                $entry->save();

                ContractsBalanceHistory::create([
                    'contract_id' => $contract->id,
                    'start_balance' => $balanceSnapshot,
                    'amount' => -$entry->total_amount,
                    'end_balance' => $balanceSnapshot - $entry->total_amount,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Данные успешно проведены!',
            ]);

        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'При проведении некоторых списаний, произошла ошибка!',
            ], 500);

        }

    }

    public function free(Request $request) {

        if(empty($request['writeoffs'])) {
            return response()->json([
                'message' => 'Не выбран ни один элемент для проведения',
            ], 400);
        }

        DB::beginTransaction();

        try {

            foreach ($request['writeoffs'] as $entry) {

                $entry = WriteOff::where('id', $entry)->first();

                if($entry->status == 'passed') {

                    if(empty($entry->contract)) {
                        continue;
                    }

                    $contract = Contract::where('title', "like", "%" . getTextAfterFirstDashIfMatched($entry->contract) . "%")->first();

                    if(empty($contract)) {
                        continue;
                    }

                    $balanceSnapshot = $contract->local_balance;
                    $contract->local_balance = $contract->local_balance + $entry->total_amount;
                    $contract->save();

                    ContractsBalanceHistory::create([
                        'contract_id' => $contract->id,
                        'start_balance' => $balanceSnapshot,
                        'amount' => $entry->total_amount,
                        'end_balance' => $balanceSnapshot + $entry->total_amount,
                    ]);

                }

                $entry->status = 'free';
                $entry->save();

            }

            DB::commit();

            return response()->json([
                'message' => 'Статус успешно изменен на - Бесплатный',
            ]);

        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'При проведении некоторых списаний, произошла ошибка!',
            ], 500);
        }


    }

    public function canceled(Request $request) {

        if(empty($request['writeoffs'])) {
            return response()->json([
                'message' => 'Не выбран ни один элемент для проведения',
            ], 400);
        }

        DB::beginTransaction();

        try {

            foreach ($request['writeoffs'] as $entry) {

                $entry = WriteOff::where('id', $entry)->first();

                if($entry->status == 'passed') {

                    if(empty($entry->contract)) {
                        continue;
                    }

                    $contract = Contract::where('title', "like", "%" . getTextAfterFirstDashIfMatched($entry->contract) . "%")->first();

                    if(empty($contract)) {
                        continue;
                    }

                    $contract->local_balance = $contract->local_balance + $entry->total_amount;
                    $contract->save();

                    $balanceSnapshot = $contract->local_balance;
                    ContractsBalanceHistory::create([
                        'contract_id' => $contract->id,
                        'start_balance' => $balanceSnapshot,
                        'amount' => $entry->total_amount,
                        'end_balance' => $balanceSnapshot + $entry->total_amount,
                    ]);

                }

                $entry->status = 'canceled';
                $entry->save();

            }

            DB::commit();

            return response()->json([
                'message' => 'Статус успешно изменен на - Аннулирован',
            ]);

        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'При проведении некоторых списаний, произошла ошибка!',
            ], 500);
        }
    }

    public function upload(Request $request) {
        $request->validate([
            'supplierType' => 'required|string',
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file');

        $supplierType = $request->input('supplierType');

        Excel::import(new WriteOffsImport($supplierType), $file, null, \Maatwebsite\Excel\Excel::CSV, [
            'delimiter' => ';',
            'enclosure' => '"',
            'escape_character' => '\\',
        ]);

        return response()->json([
            'message' => 'Файл успешно поставлен в очередь на обработку.',
        ], 201);
    }

    public function comment(Request $request, WriteOff $writeoff)
    {
        try {
            $writeoff->comment = $request->input('comment');
            $writeoff->save();

            return response()->json([
                'message' => 'Комментарий успешно обновлен',
            ]);
        } catch (Exception $exception) {
            report($exception);
            return response()->json([
                'message' => 'Ошибка при обновлении комментария',
            ], 500);
        }

    }

    public function delete(Request $request) {

        if(empty($request['writeoffs'])) {
            return response()->json([
                'message' => 'Не выбран ни один элемент для удаления',
            ], 400);
        }

        foreach ($request['writeoffs'] as $entry) {
            WriteOff::where('id', $entry)->delete();
        }

        return response()->json([
            'message' => 'Данные успешно удалены!',
        ]);
    }

    public static function mergeIfMultiple(array $data)
    {
        $result = [];

        foreach ($data as $key => $items) {
            if (count($items) > 1) {
                // Если под ключом больше одного элемента, то суммируем их
                $result[$key] = self::mergeAndSum($items);
            } else {
                // Если под ключом один элемент, оставляем его как есть
                $result[$key] = $items[0];
            }
        }

        return $result;
    }

    public static function mergeAndSum(array $data)
    {
        $merged = [
            'find' => [
                'external' => null,
                'store_number' => null,
                'store' => null,
                'date' => null,
                'day_of_week' => null,
                'total_weight' => 0, // Сумма всех total_weight
            ],
            'create' => [
                'status' => null,
                'contract' => null,
                'total_amount' => 0, // Сумма всех total_amount
                'retailer' => null,
                'total_detail' => [
                    'weight' => [
                        'fruits' => 0,
                        'bread' => 0,
                        'milk' => 0,
                        'used_oil' => 0,
                        'other' => 0,
                    ],
                    'price' => [
                        'base' => 0, // Если base одинаковый для всех массивов
                        'fruits' => 0,
                        'bread' => 0,
                        'milk' => 0,
                        'used_oil' => 0,
                        'other' => 0,
                    ],
                ],
            ],
        ];

        foreach ($data as $item) {
            // Поля find (запоминаем только один раз, так как они одинаковы)
            if (!$merged['find']['external']) {
                $merged['find']['external'] = $item['find']['external'];
                $merged['find']['store_number'] = $item['find']['store_number'];
                $merged['find']['store'] = $item['find']['store'];
                $merged['find']['date'] = $item['find']['date'];
                $merged['find']['day_of_week'] = $item['find']['day_of_week'];
            }

            // Суммируем total_weight
            $merged['find']['total_weight'] += $item['find']['total_weight'];

            // Поля create (также запоминаем только один раз)
            if (!$merged['create']['status']) {
                $merged['create']['status'] = $item['create']['status'];
                $merged['create']['contract'] = $item['create']['contract'];
                $merged['create']['retailer'] = $item['create']['retailer'];
            }

            // Суммируем total_amount
            $merged['create']['total_amount'] += $item['create']['total_amount'];

            // Суммируем детали веса
            foreach ($merged['create']['total_detail']['weight'] as $key => $value) {
                $merged['create']['total_detail']['weight'][$key] += $item['create']['total_detail']['weight'][$key] ?? 0;
            }

            // Оставляем цену такой же, если она одинакова во всех записях
            if (!$merged['create']['total_detail']['price']['base']) {
                $merged['create']['total_detail']['price']['base'] = $item['create']['total_detail']['price']['base'];
            }
        }

        return $merged;
    }

}
