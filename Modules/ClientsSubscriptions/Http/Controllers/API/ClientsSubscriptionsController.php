<?php

namespace Modules\ClientsSubscriptions\Http\Controllers\API;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\ClientSubscriptions;
use Modules\Banks\Entities\Bank;
use DB;
use Auth;
use Carbon\Carbon;
use Throwable;
class ClientsSubscriptionsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
       try {
            $user=Auth::user();
            $subscriptions_ids=[];
            $get_subscriptions=ClientSubscriptions::where('deposit_id', null)->where('user_id', $user->id)->where('desk_id', $user->desk->id);

            $total_subscription=$get_subscriptions->sum('amount');
            $subscriptions=$get_subscriptions->get();

            foreach ($subscriptions as $key => $value) {
               $subscriptions_ids[]=$value->id;
            }
            $banks=Bank::where('status',1)->get()->makeHidden(['created_at','updated_at','deleted_at']);

            $data['total_subscription']=$total_subscription;
            $data['subscriptions']=$subscriptions_ids;
            $data['banks']=$banks;
            $res=['success'=>true,'message'=>'Clients subscriptions total fetched','errors'=>[],'data'=>$data];
            return response()->json($res);

        } catch (Exception $e) {
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
            return response()->json($res);
        }catch(Throwable $e){
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
            return response()->json($res);        
        }    
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('clientssubscriptions::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('clientssubscriptions::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('clientssubscriptions::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
