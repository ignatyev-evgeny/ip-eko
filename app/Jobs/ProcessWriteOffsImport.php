<?php

namespace App\Jobs;

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
                $data = [];

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
                }

                if ($this->supplierType == 'CROSSROAD') {

                    Log::debug(json_encode($row));

                    $detail['fruits_weight'] = match ($row[8]) {
                        50000066 => $row[10],
                        default => null
                    };

                    $detail['bread_weight'] = match ($row[8]) {
                        50000069 => $row[10],
                        default => null
                    };

                    $detail['milk_weight'] = match ($row[8]) {
                        50000063 => $row[10],
                        default => null
                    };

                    $detail['other_weight'] = match ($row[8]) {
                        50000070 => $row[10],
                        default => null
                    };

                    $data = [
                        'external' => $row[1],
                        'store' => $row[4].' | '.$row[5],
                        'date' => Carbon::createFromFormat('d/m/Y', $row[0])->format('Y-m-d H:i'),
                        'total_weight' => $row[10],
                        'total_amount' => 0,
                        'total_detail' => [
                            'fruits_weight' => $detail['fruits_weight'],
                            'bread_weight' => $detail['bread_weight'],
                            'milk_weight' => $detail['bread_weight'],
                            'groceries_weight' => $detail['milk_weight'],
                            'other_weight' => $detail['other_weight'],
                        ],
                    ];
                }

                if ($this->supplierType == 'BIO') {

                    Log::debug(json_encode($row));

                    $data = [
                        'store' => $row[3],
                        'date' => Carbon::createFromFormat('d/m/Y', $row[2])->format('Y-m-d H:i'),
                        'total_weight' => $row[4],
                    ];
                }

                WriteOff::create($data);
            }
        } catch (\Exception $exception) {
            Log::error('Ошибка импорта: ' . $exception->getMessage());
        }
    }
}
