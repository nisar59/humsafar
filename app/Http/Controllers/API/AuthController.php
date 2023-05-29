<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Validator;
use Throwable;
class AuthController extends Controller
{

    public function __construct()
    {
        auth()->setDefaultDriver('api');
    }


    public function verifyuser(Request $req)
    {        
        $res=['success'=>true,'message'=>null,'errors'=>[],'data'=>null];
        
        try {
                $val = Validator::make($req->all(),[
                    'phone' => 'required|string',
                    'cnic' => 'required|string',
                ]);
        
                if ($val->fails()) {
                        $res=['success'=>false,'message'=>'Required fields are missing','errors'=>$val->messages()->all(),'data'=>null];
                }

                elseif(User::where('cnic',$req->cnic)->where('phone',$req->phone)->first()!=null){
                    
                    $otp=Settings()->sms_notifications==1 ? GenerateOTP() : 123 ;
                    $msg="Your OTP code for ".Settings()->portal_name." app is ".$otp." For any issues, contact us at ".Settings()->portal_email;
                    $result=SendMessage($req->phone, $msg);
                    if($result->success){
                        $res=['success'=>true,'message'=>"OTP sent Successfully",'errors'=>[],'data'=>null];
                        User::where('cnic',$req->cnic)->where('phone',$req->phone)->first()->update([
                            "otp"=>$otp
                        ]);
                    }
                    else{
                        $res=['success'=>false,'message'=>"Something went wrong while sending OTP with this error :".$result->message,'errors'=>[],'data'=>null];
                    }
                }
                else{
                        $res=['success'=>false,'message'=>"CNIC or phone is invalid",'errors'=>[],'data'=>null];
                }

                return response()->json($res);

            } catch (Exception $e) {
                $res=['success'=>false,'message'=>'Something went wrong with this error '.$e->getMessage(),'errors'=>[],'data'=>null];
                return response()->json($res);

            } catch (Throwable $e){
                $res=['success'=>false,'message'=>'Something went wrong with this error '.$e->getMessage(),'errors'=>[],'data'=>null];
                return response()->json($res);

            }



    }





    public function verifyotp(Request $req)
    {        
        $res=['success'=>true,'message'=>null,'errors'=>[],'data'=>null];

        try {
                $val = Validator::make($req->all(),[
                    'phone' => 'required|string',
                    'cnic' => 'required|string',
                    'otp'=>'required|string'
                ]);
        
                if ($val->fails()) {
                        $res=['success'=>false,'message'=>'Required Fields are missing','errors'=>$val->messages()->all(),'data'=>null];
                }

                elseif(User::where('cnic',$req->cnic)->where('phone',$req->phone)->where('otp',$req->otp)->first()!=null){
                        $res=['success'=>true,'message'=>"OTP Successfully verified",'errors'=>[],'data'=>null];
                }
                else{
                        $res=['success'=>false,'message'=>"OTP is invalid",'errors'=>[],'data'=>null];
                }

                return response()->json($res);

            } catch (Exception $e) {
                $res=['success'=>false,'message'=>'Something went wrong with this error '.$e->getMessage(),'errors'=>[],'data'=>null];
                return response()->json($res);

            } catch (Throwable $e){
                $res=['success'=>false,'message'=>'Something went wrong with this error '.$e->getMessage(),'errors'=>[],'data'=>null];
                return response()->json($res);

            }



    }





    public function setpin(Request $req)
    {        
        $res=['success'=>true,'message'=>null,'errors'=>[],'data'=>null];

        try {
                $val = Validator::make($req->all(),[
                    'phone' => 'required|string',
                    'cnic' => 'required|string',
                    'pin'=>'required|string'
                ]);
        
                if ($val->fails()) {
                        $res=['success'=>false,'message'=>'Required Fields are missing','errors'=>$val->messages()->all(),'data'=>null];
                }

                elseif(User::where('cnic',$req->cnic)->where('phone',$req->phone)->first()->update(['pin'=>$req->pin])){
                        $res=['success'=>true,'message'=>"Pin Successfully updated",'errors'=>[],'data'=>null];
                }
                else{
                        $res=['success'=>false,'message'=>"Something went wrong while setting your pin",'errors'=>[],'data'=>null];
                }

                return response()->json($res);

            } catch (Exception $e) {
                $res=['success'=>false,'message'=>'Something went wrong with this error '.$e->getMessage(),'errors'=>[],'data'=>null];
                return response()->json($res);

            } catch (Throwable $e){
                $res=['success'=>false,'message'=>'Something went wrong with this error '.$e->getMessage(),'errors'=>[],'data'=>null];
                return response()->json($res);

            }



    }








    public function login(Request $req)
    {   

        $res=['success'=>true,'message'=>null,'errors'=>[],'data'=>null];

        try {
            $val = Validator::make($req->all(),[
                'phone' => 'required|string',
                'pin' => 'required|string',
            ]);

            if ($val->fails()) {
                $res=['success'=>false,'message'=>'Required Fields are missing','errors'=>$val->messages()->all(),'data'=>null];
            }
            elseif(User::where('pin',$req->pin)->where('phone',$req->phone)->first()==null OR !$token = Auth::login(User::where('pin',$req->pin)->where('phone',$req->phone)->first()))
            {
                $res=['success'=>false,'message'=>'Unauthorized, phone or pin is wrong','errors'=>[],'data'=>null];
            }
            elseif(Auth::user()->status==0 OR Auth::user()->is_block==1){
                $this->logout();
                $res=['success'=>false,'message'=>'Authentication Failed, User is Blocked or Inactive','errors'=>[],'data'=>null];
            }        
            elseif(!Auth::user()->desk()->exists() OR Auth::user()->desk->status==0){
                $this->logout();
                $res=['success'=>false,'message'=>'Authentication Failed, Desk is closed or User transfered','errors'=>[],'data'=>null];
            }
            else{
                $user = Auth::user()->only('id','name','phone','cnic','emp_code','role_name','status','is_block','access_level','branch_id');

                $user['access_token']=$token;
                $user['desk']=Auth::user()->desk->only('id','desk_code','status');
                $res=['success'=>true,'message'=>'Authentication Successfull','errors'=>[],'data'=>$user];
            }

            return response()->json($res);

            
            } catch (Exception $e) {
                $res=['success'=>false,'message'=>'Something went wrong with this error '.$e->getMessage(),'errors'=>[],'data'=>null];
                return response()->json($res);

            } catch (Throwable $e){
                $res=['success'=>false,'message'=>'Something went wrong with this error '.$e->getMessage(),'errors'=>[],'data'=>null];
                return response()->json($res);


            }




    }



    public function logout()
    {   
        try {
            Auth::logout();
            $res=['success'=>true,'message'=>'Successfully logged out','errors'=>[],'data'=>null];
            return response()->json($res);        
            } catch (Exception $e) {
                $res=['success'=>false,'message'=>'Something went wrong with this error '.$e->getMessage(),'errors'=>[],'data'=>null];
                return response()->json($res);

            } catch (Throwable $e){
                $res=['success'=>false,'message'=>'Something went wrong with this error '.$e->getMessage(),'errors'=>[],'data'=>null];
                return response()->json($res);

            }

    }

    public function refresh($id)
    {   
        try {
            
            //Auth::logout();
            $user=User::find($id);
            if ($user==null) {
                $res=['success'=>false,'message'=>'Sorry Invalid User','errors'=>[],'data'=>null];
            }
            elseif(!$token = Auth::login($user))
            {
                $res=['success'=>false,'message'=>'Unauthorized, phone or pin is wrong','errors'=>[],'data'=>null];
            }
            elseif(Auth::user()->status==0 OR Auth::user()->is_block==1){
                $this->logout();
                $res=['success'=>false,'message'=>'Authentication Failed, User is Blocked or Inactive','errors'=>[],'data'=>null];
            }        
            elseif(!Auth::user()->desk()->exists() OR Auth::user()->desk->status==0){
                $this->logout();
                $res=['success'=>false,'message'=>'Authentication Failed, Desk is closed or User transfered','errors'=>[],'data'=>null];
            }
            else{
                $user = Auth::user()->only('id','name','phone','cnic','emp_code','role_name','status','is_block','access_level','branch_id');
                $user['access_token']=$token;
                $user['desk']=Auth::user()->desk->only('id','desk_code','status');
                $res=['success'=>true,'message'=>'Token successfully refreshed','errors'=>[],'data'=>$user];
            }

            return response()->json($res);


        } catch (Exception $e) {
            $res=['success'=>false,'message'=>'Something went wrong with this error '.$e->getMessage(),'errors'=>[],'data'=>null];
            return response()->json($res);

        } catch (Throwable $e){
            $res=['success'=>false,'message'=>'Something went wrong with this error '.$e->getMessage(),'errors'=>[],'data'=>null];
            return response()->json($res);

        }


    }

}
