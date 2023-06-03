<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class GlobalSamplesExport implements FromView
{

    private $sample=null;
    private $data=null;


    function __construct($req)
    {
        $this->sample = $req->file_name;
        $this->data = $req->data;

    }

    /**
    * @return \Illuminate\Support\View
    */
    public function view():View
    {
        return view('export-samples.'.$this->sample, [
            'data'=>$this->data
        ]);    
    }
}
