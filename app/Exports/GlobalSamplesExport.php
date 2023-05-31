<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class GlobalSamplesExport implements FromView
{

    private $sample=null;


    function __construct($sample)
    {
        $this->sample = $sample;
    }

    /**
    * @return \Illuminate\Support\View
    */
    public function view():View
    {
        return view('export-samples.'.$this->sample);    
    }
}
