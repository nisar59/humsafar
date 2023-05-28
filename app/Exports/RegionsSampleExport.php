<?php

namespace App\Exports;

use Modules\Regions\Entities\Regions;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class RegionsSampleExport implements FromCollection,WithHeadings
{
    public function headings(): array
    {
        $columns=['mis_sync_id','name','code'];
        return $columns;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Regions::select('mis_sync_id','name','code')->get();
    }
}
