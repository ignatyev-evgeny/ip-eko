<?php

namespace App\Imports;

use App\Models\WriteOff;
use Maatwebsite\Excel\Concerns\ToModel;

class WriteOffsImport implements ToModel
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

        $data = [];

        if($this->supplierType == 'VCR') {
            $data = [
                'external' => $row['№'],
                'store' => $row['Магазин'],
                'date' => $row['Дата отгрузки'],
                'total_weight' => 1,
                'total_amount' => 1,
                'total_detail' => [
                    'fruits_weight' => $row['Овощи/Фрукты'],
                    'bread_weight' => $row['Хлеб'],
                    'milk_weight' => $row['Молочная продукция'],
                    'groceries_weight' => $row['Мясная гастрономия'],
                    'other_weight' => $row['Прочее'],
                ],
            ];
        }

        return new WriteOff($data);
    }
}
