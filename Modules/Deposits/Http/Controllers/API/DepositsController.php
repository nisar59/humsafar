<?php

namespace Modules\Deposits\Http\Controllers\API;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Deposits\Entities\Deposits;
use App\Models\ClientSubscriptions;
use Validator;
use Throwable;
use DB;
use Auth;
use Carbon\Carbon;
class DepositsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
       try {
            $user=Auth::user();
            $deposits=Deposits::with('user', 'desk', 'bank')->where('user_id',$user->id)->where('desk_id',$user->desk->id)->get()->makeHidden(['created_at','updated_at','deleted_at', 'client_subscription_ids']);

            $res=['success'=>true,'message'=>'Deposits fetched successfully','errors'=>[],'data'=>$deposits];
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

    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $req)
    {
        DB::beginTransaction();
        try {

        $val = Validator::make($req->all(),[
            'client_subscription_ids'=>'required|array',
            'client_subscription_ids.*' => 'distinct|integer',
            'amount'=>'required',
            'deposit_slip'=>'required',
            'deposit_slip_no'=>['required', 'unique:deposits'],
            'bank_id'=>'required',
        ]);
        $res=['success'=>true,'message'=>null,'errors'=>[],'data'=>null];
        
            if ($val->fails()) {
                $res=['success'=>false,'message'=>'Required fields are missing','errors'=>$val->messages()->all(),'data'=>null];
                return response()->json($res);
            }

        $csids=$req->client_subscription_ids;
        $client_subscriptions=ClientSubscriptions::whereIn('id',$csids)->where('deposit_id',null);

        $client_subscriptions_copy=clone $client_subscriptions;

        $compensation=0;

        foreach ($client_subscriptions_copy->get() as $cs) {
            $compensation+= PackageDetail($cs->package_id)!=null ? (int) PackageDetail($cs->package_id)->compensation : 0; 
        }


        if($req->amount!=$client_subscriptions->sum('amount')){
        $res=['success'=>false,'message'=>"sorry amount not matched with our database records, please refresh and try again",'errors'=>[],'data'=>null];
        return response()->json($res);        
        }
        $user=Auth::user();
        $path=public_path('img/deposit-slips');
        $deposit=Deposits::create([
            'client_subscription_ids'=>json_encode($csids),
            'user_id'=>$user->id,
            'desk_id'=>$user->desk->id,
            'amount'=>$req->amount,
            'due_compensation'=>$compensation,
            'paid_compensation'=>0,
            'pending_compensation'=>$compensation,
            'desposit_date'=>now(),
            'deposit_slip'=>Base64FileUpload($req->deposit_slip, $path),
            'deposit_slip_no'=>$req->deposit_slip_no,
            'bank_id'=>$req->bank_id,
        ]);
        $client_subscriptions->update([
            'deposit_id'=>$deposit->id
        ]);
        DB::commit();

        $deposit=Deposits::find($deposit->id);
        $deposit->makeHidden(['created_at','updated_at','deleted_at','deposit_slip', 'client_subscription_ids']);
        $res=['success'=>true,'message'=>'Amount successfully deposited','errors'=>[],'data'=>$deposit];
        return response()->json($res);

        } catch (Exception $e) {
            DB::rollback();
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
            return response()->json($res);
                    }
        catch(Throwable $e){
            DB::rollback();
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
            return response()->json($res);        
        }






    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('deposits::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('deposits::edit');
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
