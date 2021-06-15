<?php

namespace App\Imports;

use App\Nomenclatures;
use Maatwebsite\Excel\Concerns\ToModel;

class NomenclaturesImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return Nomenclatures::all();
        // return new Nomenclatures([
        //     'id' => $row[3],
        // ]); 
    }
}
