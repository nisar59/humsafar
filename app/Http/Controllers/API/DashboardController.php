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


            $data['user'] = Auth::user()->only('id','name','phone','cnic','emp_code','role_name','status','is_block','access_level','branch_id');
            $data['user']['desk']=Auth::user()->desk->only('id','desk_code','status');
            $data['clients']['registered']=Client::where('desk_id', Auth::user()->desk->id)->count();
            $data['clients']['active']=Client::WhereHas('activesubscription')->where('desk_id', Auth::user()->desk->id)->count();
            $data['subscriptions']=ClientSubscriptions::where('deposit_id',null)->sum('amount');
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
}
