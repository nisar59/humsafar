<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Banks\Entities\Bank;

class BanksSampleExport implements FromCollection,WithHeadings
{
    public function headings(): array
    {
        $columns=['name','account_title','account_no','code'];
        return $columns;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Bank::select('name','account_title','account_no','code')->get();
    }
}
