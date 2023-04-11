<?php

namespace App\Imports;

use App\Models\Lead;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class LeadsImport implements ToCollection
{
   
    public function collection(Collection $rows)
    {
        return $rows;
    }
}
