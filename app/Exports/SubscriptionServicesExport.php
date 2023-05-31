<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\ClientSubscriptions;

class SubscriptionServicesExport implements FromView
{

    private $req=null;


    function __construct($request)
    {
        $this->req = $request;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $req=$this->req;

        $clientssub=ClientSubscriptions::query();

      if ($req->desk_code != null) {
        $desk=DeskDetailByCode($req->desk_code);
        if($desk!=null){
            $clientssub->where('desk_id', $desk->id);
        }
        else{
            $clientssub->where('desk_id', rand());
        }
      } 

      if ($req->package_id != null) {
        $clientssub->where('package_id',$req->package_id);
      }

      if ($req->subscription_date != null) {
        $clientssub->whereDate('subscription_date', $req->subscription_date);
      }

      if ($req->services != null) {
        $clientssub->where('services', $req->services);
      }

        $clientssub= $clientssub->get();


        return view('clientssubscriptions::export', [
            'subscriptions' => $clientssub
        ]);
    }



}
