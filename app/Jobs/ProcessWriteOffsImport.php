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

                WriteOff::create($data);
            }
        } catch (\Exception $exception) {
            Log::error('Ошибка импорта: ' . $exception->getMessage());
        }
    }
}
