<?php

namespace App\Exports;

use Modules\Areas\Entities\Areas;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class AreasSampleExport implements FromCollection,WithHeadings
{
    public function headings(): array
    {
        $columns=['mis_sync_id','name','code','region_id'];
        return $columns;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Areas::select('mis_sync_id','name','code','region_id')->get();
    }
}
