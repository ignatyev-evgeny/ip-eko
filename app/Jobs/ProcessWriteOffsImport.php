<?php

namespace App\Jobs;

use App\Models\Contract;
use App\Models\WriteOff;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ProcessWriteOffsImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $rows;
    protected $supplierType;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($rows, $supplierType)
    {
        $this->rows = $rows;
        $this->supplierType = $supplierType;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            foreach ($this->rows as $row) {

                if ($this->supplierType == 'VCR') {
                    $data = [
                        'external' => $row[2],
                        'store' => $row[3],
                        'date' => Carbon::createFromFormat('d.m.Y', $row[4])->format('Y-m-d H:i'),
                        'total_weight' => $row[5] + $row[6] + $row[7] + $row[8] + $row[9],
                        'total_amount' => 0,
                        'total_detail' => [
                            'fruits_weight' => $row[5],
                            'bread_weight' => $row[6],
                            'milk_weight' => $row[7],
                            'groceries_weight' => $row[8],
                            'other_weight' => $row[9],
                        ],
                    ];

                    WriteOff::create($data);
                }

                if ($this->supplierType == 'CROSSROAD') {

                    Log::channel('import')->debug("ROW: ".json_encode($row));

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


                    $weight['total'] = floatval($weight['fruits']) + floatval($weight['bread']) + floatval($weight['milk']) + floatval($weight['other']) + floatval($weight['used_oil']);

                    $date = Carbon::parse($row['data_provodki'])->format('Y-m-d'); // Ваша дата в формате Y-m-d
                    Carbon::setLocale('ru');
                    $carbonDate = Carbon::parse($date);
                    $dayOfWeek = mb_ucfirst($carbonDate->translatedFormat('l'));

                    $contractQuery = Contract::where('status', 'Активный')
                        ->whereJsonContains('export_week_days', $dayOfWeek)
                        ->where('shop', $row['zavod'])
                        ->where('retailer', 'Перекресток');

                    if ($contractQuery->exists()) {
                        $contractCount = $contractQuery->count();

                        if ($contractCount > 1) {
                            $status = 'moreOneContract';
                        } else {
                            $contract = $contractQuery->first();
                            $status = 'find';
                            $contract['id'] = $contract->id;
                            $contract['title'] = $contract->title;
                            $price = [
                                'base' => (float) $contract->price,
                                'fruits' => floatval($weight['fruits']) * (float) $contract->price_fruits_vegetables,
                                'bread' => floatval($weight['bread']) * (float) $contract->price_bakery,
                                'milk' => floatval($weight['milk']) * (float) $contract->price_dairy,
                                'used_oil' => floatval($weight['used_oil']) * (float) $contract->price_used_oil,
                                'other' => floatval($weight['other']) * (float) $contract->price_other,
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
                        ],
                        'create' => [
                            'status' => $status,
                            'contract_id' => $contract['id'],
                            'contract' => $contract['title'],
                            'total_amount' => empty($price['total']) ? $price['base'] * $weight['total'] : $price['total'],
                            'retailer' => 'Перекресток',
                            'total_weight' => $weight['total'],
                            'total_detail' => [
                                'weight' => [
                                    'fruits' => floatval($weight['fruits']),
                                    'bread' => floatval($weight['bread']),
                                    'milk' => floatval($weight['milk']),
                                    'used_oil' => floatval($weight['used_oil']),
                                    'other' => floatval($weight['other']),
                                ],
                                'price' => [
                                    'base' => $price['base'],
                                    'fruits' => floatval($price['fruits']),
                                    'bread' => floatval($price['bread']),
                                    'milk' => floatval($price['milk']),
                                    'used_oil' => floatval($price['used_oil']),
                                    'other' => floatval($price['other'])
                                ]
                            ],
                        ]
                    ];

                    $importData = $this->mergeIfMultiple($data);

                }

                if ($this->supplierType == 'BIO') {

                    $data = [
                        'store' => $row[3],
                        'date' => Carbon::createFromFormat('d/m/Y', $row[2])->format('Y-m-d H:i'),
                        'total_weight' => $row[4],
                    ];

                    WriteOff::create($data);
                }

            }

            if ($this->supplierType == 'CROSSROAD') {

                foreach ($importData as $value) {
                    $writeOff = WriteOff::firstOrCreate($value['find'], $value['create']);

                    if ($writeOff->wasRecentlyCreated) {
                        Log::channel('debug')->debug('FIND: ' . json_encode($value['find']) . ' | CREATE: ' . json_encode($value['create']));
                    }
                }

                Log::channel('debug')->debug('Импорт завершен');

            }

        } catch (\Exception $exception) {
            Log::channel('import')->debug('Ошибка импорта: ' . $exception->getMessage(). '| On Line: ' . $exception->getLine());
        }
    }

    private static function mergeIfMultiple(array $data)
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

    private static function mergeAndSum(array $data)
    {
        $merged = [
            'find' => [
                'external' => null,
                'store_number' => null,
                'store' => null,
                'date' => null,
                'day_of_week' => null,
            ],
            'create' => [
                'status' => null,
                'contract_id' => null,
                'contract' => null,
                'total_amount' => 0, // Сумма всех total_amount
                'retailer' => null,
                'total_weight' => 0, // Сумма всех total_weight
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
            $merged['create']['total_weight'] += $item['create']['total_weight'];

            // Поля create (также запоминаем только один раз)
            if (!$merged['create']['status']) {
                $merged['create']['status'] = $item['create']['status'];
                $merged['create']['contract_id'] = $item['create']['contract_id'];
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
