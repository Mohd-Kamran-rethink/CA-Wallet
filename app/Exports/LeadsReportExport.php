<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LeadsReportExport implements FromCollection, WithStyles
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
        $this->data = collect([$this->header])->concat(collect($this->data));
       
        return $this->data;
    }
   

    public function styles(Worksheet $sheet)
    {
        // Apply bold font to the first row
        $sheet->getStyle('A1:' . $sheet->getHighestDataColumn() . '1')->getFont()->setBold(true);
    }
    
    
}
