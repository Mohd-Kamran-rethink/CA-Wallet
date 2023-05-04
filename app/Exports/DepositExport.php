<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DepositExport  implements FromCollection, WithStyles
{
    protected $data;
    protected $header;
    public function __construct($data, $header)
    {
        $this->data = $data;
        $this->header = $header;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(): Collection
    {
        $result = collect([$this->header]);
        $numColumns = count($this->header);
        $numRows = count($this->data);
        
        for ($i = 1; $i < $numRows; $i++) {
            $row = [];
            for ($j = 0; $j < $numColumns; $j++) {
                $row[] = $this->data[$i][$j];
            }
            $result->push(collect($row));
        }

        return $result;
    }
    public function styles(Worksheet $sheet)
    {
        // Apply bold font to the first row
        $sheet->getStyle('A1:' . $sheet->getHighestDataColumn() . '1')->getFont()->setBold(true);
    }
}
