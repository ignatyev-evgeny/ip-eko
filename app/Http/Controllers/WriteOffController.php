<?php

namespace App\Http\Controllers;

use App\Http\Requests\WriteOffRequest;
use App\Imports\WriteOffsImport;
use App\Models\Contract;
use App\Models\ContractsBalanceHistory;
use App\Models\Invoice;
use App\Models\InvoiceWriteOffs;
use App\Models\WriteOff;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Storage;
use Yajra\DataTables\Facades\DataTables;

class WriteOffController extends Controller {

    public function list(Request $request) {
        return view('writeOff.index', [
            'writeOffs' => WriteOff::all(),
            'iframe' => $request->server()['HTTP_SEC_FETCH_DEST'] == 'iframe',
        ]);
    }

    public function data()
    {
        $writeOffs = WriteOff::select(['id', 'status', 'comment', 'external', 'store_number', 'store', 'date', 'day_of_week', 'total_weight', 'total_amount', 'counteragent', 'contract_id', 'contract', 'retailer']);

        return DataTables::of($writeOffs)

            ->addColumn('status', function($row) {
                return match ($row->status) {
                    'new' => 'Новое',
                    'duplicate' => 'Дубликат',
                    'passed' => 'Проведено',
                    'canceled' => 'Аннулирован',
                    'free' => 'Бесплатный',
                    'moreOneContract' => 'Договор > 1',
                    'find' => 'Найден',
                    default => $row->status
                };
            })

            ->addColumn('date', function($row) {
                return Carbon::parse($row->date)->format('Y-m-d').'<br>'.$row->day_of_week;
            })

            ->addColumn('date_timestamp', function($row) {
                return Carbon::parse($row->date)->format('Y-m-d');
            })

            ->addColumn('contract', function($row) {

                if(!empty($row->contractDetail)) {
                    $return = '<a target="_blank" href="/contract/list?numberFilter=' . $row->contractDetail->number . '">' . $row->contract . '</a>';
                } else {
                    $return = $row->contract;
                }

                return $return;
            })

            ->addColumn('store', function($row) {
                return '<a target="_blank" href="/contract/list?shopFilter=' . $row->store_number . '">' . $row->store_number . '</a><br>'.$row->store;
            })

            ->addColumn('total_amount', function($row) {
                return number_format($row->total_amount, 2, '.', ' ').' ₽';
            })

            ->addColumn('total_weight', function($row) {
                $return = number_format($row->total_weight, 2, '.', ' ') . ' кг.<br>';
                $return .= $row->total_amount > 0 ? '≈'.number_format($row->total_amount / $row->total_weight, 2, '.', ' ')." ₽" : number_format(0, 2, '.', ' ')." ₽";
                return $return;
            })

            ->addColumn('actions', function($row) {

                $buttons = '';

                if($row->status != 'passed') {
                    $buttons .= '<button data-write-off-id="'.$row->id.'" type="button" class="btn btn-warning m-0 text-start changeRow"><i class="fa-solid fa-pen-to-square"></i></button>';
                }

                if($row->status == 'passed') {
                    $buttons .= '<button data-write-off-id="'.$row->id.'" type="button" class="btn btn-primary m-1 text-start viewRow"><i class="fas fa-eye"></i></button>';
                }
                return $buttons;
            })

            ->rawColumns(['actions', 'date', 'store', 'contract', 'total_weight'])

            ->make(true);
    }

    public function detail(WriteOff $writeoff)
    {
        $response = $writeoff->toArray();
        $response['date'] = Carbon::parse($writeoff->date)->format('Y-m-d');

        if(!empty($writeoff->contract_id)) {
            $contract = Contract::find($writeoff->contract_id);
        }

        $response['price'] = $contract->price ?? 0;

        return response()->json([
            'message' => 'Данные успешно получены!',
            'writeOff' => $response,
        ]);
    }

    public function store(WriteOffRequest $request)
    {

        $data = $request->validated();

        if(!empty($data['contract'])) {

            $contract = Contract::where('title', "like", getTextAfterFirstDashIfMatched($data['contract']))->first();

            if(empty($contract)) {
                return response()->json([
                    'message' => "Указанный договор - {$data['contract']} не найден.",
                ], 404);
            }

            $data['contract_id'] = $contract->id;
        }

        if(!empty($data['detail_view']) && $data['detail_view'] == 'on') {

            $data['total_detail'] = [
                'price' => [
                    'base' => $data['price'] ?? 0,
                    'fruits' => $data['fruits_price'] ?? 0,
                    'bread' => $data['bread_price'] ?? 0,
                    'milk' => $data['milk_price'] ?? 0,
                    'food_waste' => $data['food_waste_price'] ?? 0,
                    'used_vegetable_oil' => $data['used_vegetable_oil_price'] ?? 0,
                    'groceries' => $data['groceries_price'] ?? 0,
                    'other' => $data['other_price'] ?? 0
                ],
                'weight' => [
                    'fruits' => $data['fruits_weight'] ?? 0,
                    'bread' => $data['bread_weight'] ?? 0,
                    'milk' => $data['milk_weight'] ?? 0,
                    'food_waste' => $data['food_waste_weight'] ?? 0,
                    'used_vegetable_oil' => $data['used_vegetable_oil_weight'] ?? 0,
                    'groceries' => $data['groceries_weight'] ?? 0,
                    'other' => $data['other_weight'] ?? 0,
                ]
            ];

            $data['total_weight'] = array_sum([
                $data['total_detail']['weight']['fruits'],
                $data['total_detail']['weight']['bread'],
                $data['total_detail']['weight']['milk'],
                $data['total_detail']['weight']['food_waste'],
                $data['total_detail']['weight']['used_vegetable_oil'],
                $data['total_detail']['weight']['groceries'],
                $data['total_detail']['weight']['other'],
            ]);

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

        $writeoff = WriteOff::create($data);

        return response()->json([
            'message' => 'Данные успешно сохранены!',
            'writeoff' => $writeoff,
        ], 201);

    }

    public function update(WriteOff $writeoff, WriteOffRequest $request)
    {

        $data = $request->validated();

        if($writeoff->status == 'passed') {
            return response()->json([
                'message' => 'Запрещено редактировать уже проведенное списание',
            ], 403);
        }

        if(empty($data['contract'])) {
            return response()->json([
                'message' => 'Договор является обязательным для заполнения',
            ], 404);
        }

        $contract = Contract::where('title', "like", getTextAfterFirstDashIfMatched($data['contract']))->first();

        if(empty($contract)) {
            return response()->json([
                'message' => "Договор - {$data['contract']} - не найден",
            ], 404);
        }

        $data['contract_id'] = $contract->id;

        if(!empty($data['detail_view']) && $data['detail_view'] == "on") {

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

            $data['total_weight'] = array_sum([
                $data['total_detail']['weight']['fruits'],
                $data['total_detail']['weight']['bread'],
                $data['total_detail']['weight']['milk'],
                $data['total_detail']['weight']['food_waste'],
                $data['total_detail']['weight']['used_vegetable_oil'],
                $data['total_detail']['weight']['groceries'],
                $data['total_detail']['weight']['other'],
            ]);

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

        $data['status'] = 'find';
        $writeoff->update($data);

        return response()->json([
            'message' => 'Списание успешно обновлено',
            'writeoff' => $writeoff,
        ], 201);
    }

    public function transfer(WriteOff $writeoff, Request $request) {

        if($writeoff->status != 'passed') {
            return response()->json([
                'message' => 'Изменять договор в списании можно только на проведенном списании',
            ], 404);
        }

        if(empty($request->newContract)) {
            return response()->json([
                'message' => 'Невозможно перенести списание, если новый договор не заполнен',
            ], 404);
        }

        if(empty($request->contract)) {
            return response()->json([
                'message' => 'Невозможно перенести списание, если договор не заполнен',
            ], 404);
        }

        $contract = Contract::where('title', "like", "%" . getTextAfterFirstDashIfMatched($request->contract) . "%")->first();
        $newContract = Contract::where('title', "like", "%" . getTextAfterFirstDashIfMatched($request->newContract) . "%")->first();

        if(empty($newContract)) {
            return response()->json([
                'message' => 'Невозможно перенести списание, введенный договор не найден на стороне приложения',
            ], 404);
        }

        if(empty($contract)) {
            return response()->json([
                'message' => 'Невозможно перенести списание, введенный указанный договор не найден на стороне приложения',
            ], 404);
        }

        $contractBalanceSnapshot = $contract->local_balance;
        $newContractBalanceSnapshot = $newContract->local_balance;

        $contract->local_balance = $contract->local_balance - floatval($writeoff->total_amount);
        $contract->save();

        $newContract->local_balance = $newContract->local_balance + floatval($writeoff->total_amount);
        $newContract->save();


        ContractsBalanceHistory::create([
            'type' => 'transfer',
            'type_relation' => $contract->id,
            'contract_id' => $contract->id,
            'start_balance' => $contractBalanceSnapshot,
            'amount' => -floatval($writeoff->total_amount),
            'end_balance' => $contractBalanceSnapshot - floatval($writeoff->total_amount),
            'comment' => "Перенос с договора {$contract->title} на договор {$newContract->title}",
        ]);

        ContractsBalanceHistory::create([
            'type' => 'transfer',
            'type_relation' => $newContract->id,
            'contract_id' => $newContract->id,
            'start_balance' => $newContractBalanceSnapshot,
            'amount' => floatval($writeoff->total_amount),
            'end_balance' => $newContractBalanceSnapshot + floatval($writeoff->total_amount),
            'comment' => "Перенос с договора {$contract->title} на договор {$newContract->title}",
        ]);

        $writeoff->contract = $newContract->title;
        $writeoff->save();

        return response()->json([
            'message' => 'Списание успешно перенесено на другой договор',
            'writeOff' => $writeoff,
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

            foreach ($request['writeoffs'] as $writeoff) {

                $writeoff = WriteOff::find($writeoff);

                if($writeoff->total_amount <= 0) {
                    continue;
                }

                if($writeoff->status == 'passed') {
                    continue;
                }

                if(empty($writeoff->contract_id)) {
                    continue;
                }

                $contract = Contract::find($writeoff->contract_id);

                if(empty($contract)) {
                    continue;
                }

                $balanceSnapshot = $contract->local_balance;
                $contract->local_balance = $contract->local_balance - $writeoff->total_amount;
                $contract->save();

                $writeoff->status = 'passed';
                $writeoff->save();

                ContractsBalanceHistory::create([
                    'type' => 'writeOff',
                    'type_relation' => $writeoff->id,
                    'contract_id' => $contract->id,
                    'start_balance' => $balanceSnapshot,
                    'amount' => -$writeoff->total_amount,
                    'end_balance' => $balanceSnapshot - $writeoff->total_amount,
                    'comment' => "Отгрузка / {$writeoff->date} / BH {$writeoff->store_number}",
                ]);

                $invoice = Invoice::firstOrCreate([
                    'contract_id' => $contract->id,
                    'generated' => false
                ], [
                    'date_created' => Carbon::now()->toDateTimeString(),
                ]);

                InvoiceWriteOffs::updateOrCreate([
                    'invoice_id' => $invoice->id,
                    'write_off_id' => $writeoff->id,
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

            foreach ($request['writeoffs'] as $writeoff) {

                $writeoff = WriteOff::find($writeoff);

                if($writeoff->status == 'passed') {

                    if(empty($writeoff->contract_id)) {
                        continue;
                    }

                    $contract = Contract::find($writeoff->contract_id);

                    if(empty($contract)) {
                        continue;
                    }

                    $balanceSnapshot = $contract->local_balance;
                    $contract->local_balance = $contract->local_balance + $writeoff->total_amount;
                    $contract->save();

                    ContractsBalanceHistory::create([
                        'contract_id' => $contract->id,
                        'start_balance' => $balanceSnapshot,
                        'amount' => $writeoff->total_amount,
                        'end_balance' => $balanceSnapshot + $writeoff->total_amount,
                    ]);

                    InvoiceWriteOffs::where([
                        'write_off_id' => $writeoff->id,
                    ])->delete();

                }

                $writeoff->status = 'free';
                $writeoff->save();

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

            foreach ($request['writeoffs'] as $writeoff) {

                $writeoff = WriteOff::find($writeoff);

                if($writeoff->status == 'passed') {

                    if(empty($writeoff->contract_id)) {
                        continue;
                    }

                    $contract = Contract::find($writeoff->contract_id);

                    if(empty($contract)) {
                        continue;
                    }

                    $contract->local_balance = $contract->local_balance + $writeoff->total_amount;
                    $contract->save();

                    $balanceSnapshot = $contract->local_balance;
                    ContractsBalanceHistory::create([
                        'contract_id' => $contract->id,
                        'start_balance' => $balanceSnapshot,
                        'amount' => $writeoff->total_amount,
                        'end_balance' => $balanceSnapshot + $writeoff->total_amount,
                    ]);

                    InvoiceWriteOffs::where([
                        'write_off_id' => $writeoff->id,
                    ])->delete();

                }

                $writeoff->status = 'canceled';
                $writeoff->save();

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

    public function upload(Request $request)
    {
        $request->validate([
            'supplierType' => 'required|string',
            'file' => 'required|file|mimes:csv,txt,xls,xlsx',
        ]);

        try {

            $file = $request->file('file');
            $supplierType = $request->input('supplierType');
            $filePath = $file->getRealPath();

            $tempDirectory = storage_path('app/converted_files');
            if (!file_exists($tempDirectory)) {
                mkdir($tempDirectory, 0777, true);
            }

            $csvFilePath = $tempDirectory . '/' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.csv';

            if (in_array($file->getClientOriginalExtension(), ['xls', 'xlsx'])) {

                $reader = IOFactory::createReader('Xlsx');
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();

                $csvFile = fopen($csvFilePath, 'w');
                foreach ($worksheet->getRowIterator() as $row) {
                    $rowData = [];
                    foreach ($row->getCellIterator() as $cell) {
                        $value = $cell->getValue();

                        // Проверяем, что текущая колонка - это "A"
                        if ($cell->getColumn() === 'A') {
                            if (is_numeric($value)) {
                                try {
                                    // Преобразуем числовую дату в читаемый формат
                                    $value = Date::excelToDateTimeObject($value)->format('d/m/Y');
                                } catch (\Exception $e) {
                                    // Если значение не удаётся преобразовать, оставляем как есть
                                }
                            }
                        }

                        $rowData[] = $value;
                    }
                    fputcsv($csvFile, $rowData, ';');
                }
                fclose($csvFile);

                $filePath = $csvFilePath;
            }

            Excel::import(new WriteOffsImport($supplierType), $filePath, null, \Maatwebsite\Excel\Excel::CSV, [
                'delimiter' => ';',
                'enclosure' => '"',
                'escape_character' => '\\',
            ]);

            if (file_exists($csvFilePath)) {
                unlink($csvFilePath);
            }

            return response()->json([
                'message' => 'Файл успешно поставлен в очередь на обработку.',
            ], 201);

        } catch (Exception $exception) {
            Log::error('Ошибка при обработке файла: ' . $exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            return response()->json([
                'message' => 'Произошла ошибка при обработке файла.',
                'error' => $exception->getMessage(),
            ], 500);
        }
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

        foreach ($request['writeoffs'] as $writeoff) {

            $writeoffDetail = WriteOff::find($writeoff);

            if($writeoffDetail->status == 'passed') {
                continue;
            }

            $writeoffDetail->delete();
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
