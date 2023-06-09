<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Clients\Entities\Client;
use App\Models\ClientSubscriptions;
use Throwable;
use DB;
use Auth;
class DashboardController extends Controller
{


    public function index()
    {
       try {
            if(!Auth::user()->desk()->exists() && Auth::user()->desk==null){
            $res=['success'=>false,'message'=>'Sorry unathorized access, either desk is closed, or user transfered or blocked','errors'=>[],'data'=>null];
            return response()->json($res);            
            }


            $user = Auth::user()->only('id','name','phone','cnic','emp_code','role_name','status','is_block','access_level','branch_id','bank_name','bank_account_title','bank_account_no','bank_account_verified');

            $data['user']['branch']=Auth::user()->branch;
            $data['user']['area']=Auth::user()->area;
            $data['user']['region']=Auth::user()->region;

            $data['user']['desk']=Auth::user()->desk->only('id','desk_code','status');


            $data['clients']['registered']=Client::where('desk_id', Auth::user()->desk->id)->count();
            $data['clients']['active']=Client::WhereHas('activesubscription')->where('desk_id', Auth::user()->desk->id)->count();
            $data['subscriptions']=ClientSubscriptions::where('deposit_id',null)->where('user_id', Auth::user()->id)->sum('amount');
            $res=['success'=>true,'message'=>'Dashboard stats fetched successfully','errors'=>[],'data'=>$data];
            return response()->json($res);



        } catch (Exception $e) {
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
            return response()->json($res);
        }catch(Throwable $e){
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
            return response()->json($res);        
        }    
    }

    public function bankverification(Request $req)
    {
        try {
           
            $verification=$req->bank_account_verified;
            $user=Auth::user();

            $user->update(['bank_account_verified'=>$verification]);

            $res=['success'=>true,'message'=>'Bank Account verification status successfully updated','errors'=>[],'data'=>null];

            return response()->json($res);

        } catch (Exception $e) {
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
            return response()->json($res);
        }catch(Throwable $e){
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
            return response()->json($res);        
        } 
    }


}
