<?php

namespace App\Imports;

use App\Models\WriteOff;
use Carbon\Carbon;
use Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;

class WriteOffsImport implements ToModel, WithChunkReading, WithEvents, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    protected $supplierType;

    public function __construct($supplierType)
    {
        $this->supplierType = $supplierType;
    }

    public function model(array $row)
    {
        try {
            $data = [];

            if($this->supplierType == 'VCR') {
                $data = [
                    'external' => $row[2],
                    'store' => $row[3],
                    'date' => Carbon::createFromFormat('d.m.Y', $row[4])->format('Y-m-d H:i:s'),
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
            }

            return new WriteOff($data);

        } catch (\Exception $exception) {
            Log::error('Ошибка импорта: ' . $exception->getMessage());
        }

    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                Log::channel('import')->debug('Начало импорта файла');
            },
            AfterImport::class => function (AfterImport $event) {
                Log::channel('import')->debug('Импорт файла завершен');
            },
        ];
    }

}
