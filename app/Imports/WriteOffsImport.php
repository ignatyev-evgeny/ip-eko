<?php

namespace App\Imports;

use App\Jobs\ProcessWriteOffsImport;
use Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Illuminate\Support\Collection;

class WriteOffsImport implements ToCollection, WithChunkReading, WithEvents, WithStartRow
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

    public function collection(Collection $rows)
    {
        ProcessWriteOffsImport::dispatch($rows->toArray(), $this->supplierType);
    }

    public function chunkSize(): int
    {
        return 1000;
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
