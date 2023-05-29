<?php

namespace Modules\Clients\Http\Controllers\API;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Clients\Entities\Client;
use Modules\Packages\Entities\Packages;
use App\Models\ClientSubscriptions;
use Carbon\Carbon;
use Auth;
use DB;
use Validator;
use Throwable;
class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
       try {
            $client=Client::with('activesubscription.package')->where('desk_id', Auth::user()->desk->id);

            if($client->count()<1){
            $res=['success'=>false,'message'=>'Clients listing is empty','errors'=>[],'data'=>$client->get()];
            return response()->json($res);
            }

            $client=$client->get()->makeHidden(['status','created_at','updated_at','deleted_at']);
            $res=['success'=>true,'message'=>'Clients listing fetched successfully','errors'=>[],'data'=>$client];
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
        try {
            $data=[
                'education'=>Education(),
                'province_district'=>json_decode(ProvincesDistricts())
            ];

        $res=['success'=>true,'message'=>'Education and Province Districts List is fetched','errors'=>[],'data'=>$data];

            return response()->json($res);

        } catch (Exception $e) {
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
        } catch (Throwable $e){
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
        }
    }


    public function verify(Request $req)
    {
        try {

           $otp=Settings()->sms_notifications==1 ? GenerateOTP() : 123 ;

            $msg="Your OTP code for ".Settings()->portal_name." app is ".$otp." For any issues, contact us at ".Settings()->portal_email;
            $result=SendMessage($req->phone, $msg);
            if($result->success){
                $res=['success'=>true,'message'=>"OTP sent Successfully",'errors'=>[],'data'=>null];
            }
            else{
                $res=['success'=>false,'message'=>"Something went wrong while sending OTP with this error :".$result->message,'errors'=>[],'data'=>null];
            }


        $res=['success'=>true,'message'=>'Education and Province Districts List is fetched','errors'=>[],'data'=>$data];

            return response()->json($res);

        } catch (Exception $e) {
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
        } catch (Throwable $e){
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
        }
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
            'name'=>'required',
            'parentage'=>'required',
            'dob'=>'required',
            'education'=>'required',
            'gender'=>'required',
            'phone_primary'=>['required', 'max:12', 'unique:clients'],
            'cnic'=>['required', 'max:15', 'unique:clients'],
            'monthly_income'=>'required',
            'address'=>'required',
            'medical_expense'=>'required',

        ]);
        $res=['success'=>true,'message'=>null,'errors'=>[],'data'=>null];
        
            if ($val->fails()) {
                $res=['success'=>false,'message'=>'Required fields are missing','errors'=>$val->messages()->all(),'data'=>null];
                return response()->json($res);
            }

            $inputs=$req->all();

            $inputs['status']=0;
            $inputs['phone_verified']=0;
            if($req->phone_verified!=null){
                $inputs['phone_verified']=1;
            }
            $inputs['desk_id']=Auth::user()->desk->id;
            $inputs['dob']=Carbon::parse($req->dob);
            $client=Client::create($inputs);
            $sub=null;
            $msg_res=null;
            if($req->package_id!=null){
                $package=Packages::find($req->package_id);
                $sub_date=now();
                $sub_type=subscriptionTypes($package->subscription_type);
                $subscription_duration=$package->subscription_duration;
                $expire_date=now()->addMonth(1)->format('d-m-Y');

                $total_sub_duration=(int)$subscription_duration * (int) $sub_type['contains'];

                if($sub_type['type']=='day'){
                   $expire_date= $sub_date->addDay($total_sub_duration);
                }
                if($sub_type['type']=='month'){
                   $expire_date= $sub_date->addMonth($total_sub_duration);
                }
                if($sub_type['type']=='year'){
                   $expire_date= $sub_date->addYear($total_sub_duration);
                }

              $sub= ClientSubscriptions::create([
                    'desk_id'=>Auth::user()->desk->id,
                    'client_id'=>$client->id,
                    'user_id'=>Auth::user()->id,
                    'package_id'=>$req->package_id,
                    'subscription_date'=>now(),
                    'expire_date'=>$expire_date,
                    'amount'=>$package->amount,
                    'transaction_no'=>GenerateTransactionNo($client->cnic),
                    'status'=>1,
                ]);
               $client->status=1;
               $client->save();
                DB::commit();

            $sub->makeHidden(['created_at','updated_at','deleted_at']);
            $sub['subscription_date']=Carbon::parse($sub->subscription_date)->format('d-m-Y');
            $sub['expire_date']=Carbon::parse($sub->expire_date)->format('d-m-Y');

            /*/////////////////////Send SMS Notification///////////////////////////*/

            $msg="You have successfully subscribed to ".$package->title." package and Rs.".number_format($package->amount)." have been Received.";
            $msg_res=SendMessage($client->phone_primary, $msg);
            if($msg_res->success){
                $msg_res="And SMS Notification sent";
            }else{
                $msg_res="And SMS Notification Not sent because ".$msg_res->message;
            }

            }
            /*/////////////////////////End SMS Notification////////////////////////*/



            $client->makeHidden(['created_at','updated_at','deleted_at']);
            $client['dob']=Carbon::parse($client->dob)->format('d-m-Y');

            $data['client']=$client;
            $data['subscription']=$sub;
            $res=['success'=>true,'message'=>'Client successfully registered '.$msg_res,'errors'=>[],'data'=>$data];
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
    public function show(Request $req)
    {

       try {
            $rules =[
            'term' => 'required',
            ];
            $messages=[
                'term'=>"CNIC or Phone is required",
            ];

            $val = Validator::make($req->all(),$rules, $messages);

            $res=['success'=>true,'message'=>null,'errors'=>[],'data'=>null];
            if ($val->fails()) {
                $res=['success'=>false,'message'=>'Required fields are missing','errors'=>$val->messages()->all(),'data'=>null];
                return response()->json($res);
            }

            $client=Client::with('activesubscription.package')->where('desk_id', Auth::user()->desk->id)->where('cnic',$req->term)->orWhere('phone_primary',$req->term)->first();

            if($client==null){
            $res=['success'=>false,'message'=>'Client not found','errors'=>[],'data'=>null];
            return response()->json($res);
            }

            $client->makeHidden(['status','created_at','updated_at','deleted_at']);
            $res=['success'=>true,'message'=>'Client fetched successfully','errors'=>[],'data'=>$client];
            return response()->json($res);

        } catch (Exception $e) {
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
            return response()->json($res);
                    }
        catch(Throwable $e){
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
            return response()->json($res);        
        }


    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {

        try {
            if(Client::find($id)!=null && $client=Client::find($id)){
                $client->makeHidden(['created_at','updated_at','deleted_at']);
                $client['dob']=Carbon::parse($client->dob)->format('d-m-Y');
                $res=['success'=>true,'message'=>'Client successfully found','errors'=>[],'data'=>$client];
                return response()->json($res);        
            }else{
                $res=['success'=>false,'message'=>'Sorry User not found','errors'=>[],'data'=>null];
                return response()->json($res);                    
            }

        } catch (Exception $e) {
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
            return response()->json($res);
                    }
        catch(Throwable $e){
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
            return response()->json($res);        
        }

    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $req, $id)
    {
        

        DB::beginTransaction();
        try {
        $val = Validator::make($req->all(),[
            'name'=>'required',
            'parentage'=>'required',
            'dob'=>'required',
            'education'=>'required',
            'gender'=>'required',
            'phone_primary'=>['required', 'max:12', 'unique:clients,phone_primary,'.$id],
            'cnic'=>['required', 'max:15', 'unique:clients,cnic,'.$id],
            'monthly_income'=>'required',
            'address'=>'required',
            'medical_expense'=>'required',

        ]);
        $res=['success'=>true,'message'=>null,'errors'=>[],'data'=>null];
        
            if ($val->fails()) {
                $res=['success'=>false,'message'=>'Required fields are missing','errors'=>$val->messages()->all(),'data'=>null];
                return response()->json($res);
            }

            $inputs=$req->all();
            $inputs['dob']=Carbon::parse($req->dob);
            $client=Client::find($id);
            if($client==null){
                $res=['success'=>false,'message'=>'Sorry Client not found','errors'=>[],'data'=>null];
                return response()->json($res);                    
            }

            $client->update($inputs);
            DB::commit();
            $client=Client::find($id);

            $client->makeHidden(['created_at','updated_at','deleted_at']);
            $client['dob']=Carbon::parse($client->dob)->format('d-m-Y');
            $res=['success'=>true,'message'=>'Client successfully updated','errors'=>[],'data'=>$client];
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





    public function newsubscription(Request $req, $id)
    {
        DB::beginTransaction();
        try {

            $val = Validator::make($req->all(),[
                'package_id'=>'required',
            ]);
            $res=['success'=>true,'message'=>null,'errors'=>[],'data'=>null];
            
                if ($val->fails()) {
                    $res=['success'=>false,'message'=>'Required fields are missing','errors'=>$val->messages()->all(),'data'=>null];
                    return response()->json($res);
                }

                $client=Client::find($id);
                if($client==null){
                    $res=['success'=>false,'message'=>'Client Not Found','errors'=>[],'data'=>null];
                    return response()->json($res);                        
                }
                if($client->activesubscription()->exists() && $client->activesubscription!=null){
                    $res=['success'=>false,'message'=>'Client has already active subscription','errors'=>[],'data'=>null];
                    return response()->json($res);        
                }

                $user=Auth::user();
                $package=Packages::find($req->package_id);
                if($package==null){
                    $res=['success'=>false,'message'=>'Package Not Found','errors'=>[],'data'=>null];
                    return response()->json($res);                        
                }
                $sub_date=now();
                $sub_type=subscriptionTypes($package->subscription_type);
                $subscription_duration=$package->subscription_duration;
                $expire_date=now()->addMonth(1)->format('d-m-Y');

                $total_sub_duration=(int)$subscription_duration * (int) $sub_type['contains'];

                if($sub_type['type']=='day'){
                   $expire_date= $sub_date->addDay($total_sub_duration);
                }
                if($sub_type['type']=='month'){
                   $expire_date= $sub_date->addMonth($total_sub_duration);
                }
                if($sub_type['type']=='year'){
                   $expire_date= $sub_date->addYear($total_sub_duration);
                }

               $sub=ClientSubscriptions::create([
                    'desk_id'=>$user->desk->id,
                    'client_id'=>$client->id,
                    'user_id'=>$user->id,
                    'package_id'=>$req->package_id,
                    'subscription_date'=>now(),
                    'expire_date'=>$expire_date,
                    'amount'=>$package->amount,
                    'transaction_no'=>GenerateTransactionNo($client->cnic),
                    'status'=>1,
                ]);
               $client->status=1;
               $client->save();
                DB::commit();

                /*/////////////////////// Send SMS Notification////////////////////////*/
                $msg_res=null;

                $msg="You have successfully subscribed to ".$package->title." package and Rs.".number_format($package->amount)." have been Received.";
                $msg_res=SendMessage($client->phone_primary, $msg);
                if($msg_res->success){
                    $msg_res="And SMS Notification sent";
                }else{
                    $msg_res="And SMS Notification Not sent because ".$msg_res->message;
                }

                /*////////////////////////End SMS Notification///////////////////////////*/


                $sub->makeHidden(['created_at','updated_at','deleted_at']);
                $sub['subscription_date']=Carbon::parse($sub->subscription_date)->format('d-m-Y');
                $sub['expire_date']=Carbon::parse($sub->expire_date)->format('d-m-Y');

                $client->makeHidden(['created_at','updated_at','deleted_at']);
                $client['dob']=Carbon::parse($client->dob)->format('d-m-Y');

                $data['client']=$client;
                $data['subscription']=$sub;


                $res=['success'=>true,'message'=>'Client subscribed to new package successfully '.$msg_res,'errors'=>[],'data'=>$data];
                return response()->json($res);        

        } catch (Exception $e) {
            DB::rollback();
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
            return response()->json($res);        
        
        }catch(Throwable $e){
            DB::rollback();
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
            return response()->json($res);        
        }



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
