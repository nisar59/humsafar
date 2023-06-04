<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Modules\Deposits\Entities\Deposits;

class DepositsExport implements FromView
{

    private $req=null;


    function __construct($request)
    {
        $this->req = $request;
    }

    public function view(): View
    {
        $req=$this->req;

        $deposits=Deposits::query();


      if ($req->desk_code != null) {
        $desk=DeskDetailByCode($req->desk_code);
        if($desk!=null){
            $deposits->where('desk_id', $desk->id);
        }
        else{
            $deposits->where('desk_id', rand());
        }
      } 
      if ($req->deposit_slip_no != null) {
        $deposits->where('deposit_slip_no', $req->deposit_slip_no);
      }
      if ($req->desposit_date != null) {
        $deposits->whereDate('desposit_date', $req->desposit_date);
      }    
      if ($req->is_verified != null) {
        $deposits->where('is_verified', $req->is_verified);
      }  

      $deposits=$deposits->get();

        return view('deposits::export', [
            'deposits' => $deposits
        ]);
    }


}
