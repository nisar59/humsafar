<?php

namespace App\Exports;

use Modules\Branches\Entities\Branches;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class BranchesSampleExport implements FromCollection,WithHeadings
{
    public function headings(): array
    {
        $columns=['mis_sync_id','name','code','region_id','area_id'];
        return $columns;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Branches::select('mis_sync_id','name','code','region_id','area_id')->get();
    }
}
