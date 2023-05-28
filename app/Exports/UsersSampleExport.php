<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersSampleExport implements FromCollection,WithHeadings
{
    public function headings(): array
    {
        $columns=['name','father_name','cnic','phone','email','emp_code','role_name','access_level','branch_id'];
        return $columns;
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::select('name','father_name','cnic','phone','email','emp_code','role_name','access_level','branch_id')->get();
    }
}
