<?php

namespace Modules\Clients\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Clients\Entities\Client;
use Modules\Desks\Entities\Desk;
use Modules\Packages\Entities\Packages;
use App\Models\ClientSubscriptions;
use DataTables;
use Throwable;
use DB;
use Auth;
use Carbon\Carbon;
class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {

        $req=request();
    if ($req->ajax()) {
        $strt   = $req->start;
        $length = $req->length;

        $clients=Client::query();

          if ($req->desk_code != null) {
            $desk=DeskDetailByCode($req->desk_code);

            if($desk!=null){
                $clients->where('desk_id', $desk->id);
            }
            else{
                $clients->where('desk_id', rand());
            }
          } 

          if ($req->name != null) {
            $clients->where('name','LIKE','%'.$req->name.'%');
          }

          if ($req->cnic != null) {
            $clients->where('cnic', $req->cnic);
          }
          if ($req->phone_primary != null) {
            $clients->where('phone_primary', $req->phone_primary);
          }    
          if ($req->status != null) {
            if($req->status==0){
                $clients->doesntHave('activesubscription');
            }
            else{
                $clients->whereHas('activesubscription');
            }
          }  

        $total = $clients->count();
        $clients   = $clients->offset($strt)->limit($length)->get();


            return DataTables::of($clients)
                ->setOffset($strt)
                ->with([
                  "recordsTotal"    => $total,
                  "recordsFiltered" => $total,
                ])
                ->addColumn('action', function ($row) {
                    $action='';
                if(Auth::user()->can('clients.edit')){
                $action.='<a class="btn btn-primary m-1 btn-sm" href="'.url('clients/edit/'.$row->id).'"><i class="fas fa-pencil-alt"></i></a>';
                }
                if(Auth::user()->can('clients.delete')){
                $action.='<a class="btn btn-danger m-1 btn-sm" href="'.url('clients/destroy/'.$row->id).'"><i class="fas fa-trash-alt"></i></a>';
                    }
                return $action;
                })



                ->addColumn('modified_by', function ($row) {
                    $modified='';
                    if($row->logs()->exists() && $row->logs!=null && $row->logs->count()<2){
                        foreach ($row->logs as $log) {
                           $modified.=($log->user()->exists() && $log->user!=null) ? '<a class="btn m-1 btn-info btn-sm" data-bs-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="'.$log->message.'" href="javascript:void(0)" >'.$log->user->name.'</a>' : null;
                        }
                    }
                    if($row->logs()->exists() && $row->logs!=null && $row->logs->count()>=2){
                        $messages='';
                        foreach ($row->logs as $key=> $log) {
                            $key++;
                            $messages.=$key.": ".$log->message."\n\n";
                        }

                        $modified.=($row->logs()->exists() && $row->logs!=null)? '<a class="btn btn-info btn-sm" data-bs-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="'.$messages.'" href="javascript:void(0)" >'.$row->logs->count().'</a>' : null;
                    }

                    return $modified;
                })





                ->editColumn('desk_id', function ($row) {
                    if($row->clientdesk()->exists() && $row->clientdesk!=null){
                    return $row->clientdesk->desk_code;
                    }
                })
                ->editColumn('name', function ($row) {
                    return $row->name;
                })

                ->editColumn('parentage', function ($row) {
                     return $row->parentage;
                 })
                ->editColumn('dob', function ($row) {
                    return Carbon::parse($row->dob)->format('d-m-Y');
                })

                ->editColumn('education', function ($row) {
                     return $row->education;
                 })
                ->editColumn('gender', function ($row) {
                     return $row->gender;
                 })
                ->editColumn('phone_primary', function ($row) {
                     return $row->phone_primary;
                 })
                ->editColumn('cnic', function ($row) {
                     return $row->cnic;
                 })
                ->editColumn('monthly_income', function ($row) {
                     return $row->monthly_income;
                 })
                ->editColumn('address', function ($row) {
                     return $row->address;
                 })
                ->editColumn('medical_expense', function ($row) {
                     return $row->medical_expense;
                 })

                ->editColumn('status', function ($row) {
                    if($row->activesubscription()->exists() && $row->activesubscription!=null){
                    return  '<a class="btn btn-success btn-sm client-subscription" data-href="'.url('clients/subscription/'.$row->id).'" href="javascript:void(0)">Active</a>';
                    }
                    else{
                    return  '<a class="btn btn-danger btn-sm client-subscription" data-href="'.url('clients/subscription/'.$row->id).'" href="javascript:void(0)">Expired</a>';
                    }
                })
                ->editColumn('phone_verified', function ($row) {
                    if($row->phone_verified==1){
                    return  '<a class="btn btn-success btn-sm" href="javascript:void(0)">verified</a>';
                    }
                    else{
                    return  '<a class="btn btn-danger btn-sm" href="javascript:void(0)">Not verified</a>';
                    }
                })


                ->rawColumns(['action', 'status', 'phone_verified', 'modified_by'])
                ->make(true);
    }


        return view('clients::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $desks=Desk::where('status',1)->get();
        $packages=Packages::where('status',1)->get();
        return view('clients::create')->withDesks($desks)->with('packages',$packages);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $req)
    {

        $req->validate([
            'desk_id'=>'required',
            'name'=>'required',
            'parentage'=>'required',
            'dob'=>'required',
            'education'=>'required',
            'gender'=>'required',
            'weight'=>'required',
            'height'=>'required',
            'blood_group'=>'required',
            'marital_status'=>'required',
            'relation'=>'required',
            'phone_primary'=>['required', 'max:12', 'unique:clients'],
            'cnic'=>['required', 'max:15', 'unique:clients'],
            'email'=>['required', 'unique:clients'],
            'monthly_income'=>'required',
            'address'=>'required',
            'medical_expense'=>'required',

        ]);

        DB::beginTransaction();
        try {
            $inputs=$req->except('_token');

            $inputs['status']=0;
            $inputs['phone_verified']=0;


            if($req->phone_verified!=null){
                $inputs['phone_verified']=1;
            }

            $client=Client::create($inputs);

            if($req->package_id!=null){
                $desk=Desk::find($req->desk_id);
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

                $expire_date=$expire_date->addDay(3);

               ClientSubscriptions::create([
                    'desk_id'=>$desk->id,
                    'client_id'=>$client->id,
                    'user_id'=>$desk->deskuser()->exists() ? $desk->deskuser->id : null,
                    'package_id'=>$req->package_id,
                    'subscription_date'=>now(),
                    'expire_date'=>$expire_date,
                    'amount'=>$package->amount,
                    'transaction_no'=>GenerateTransactionNo($client->cnic),
                    'status'=>1,
                ]);
               $client->status=1;
               $client->save();
            }


            DB::commit();
            return redirect('clients')->with('success', 'Client successfully registered');

        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
        catch(Throwable $e){
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }






    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('clients::show');
    }


    public function subscription($id)
    {
        $client=Client::with('clientsubscriptions')->find($id);
        $packages=Packages::where('status',1)->get();
        $html= view('clients::subscription')->withClient($client)->with('packages',$packages)->render();
        return response()->json(['success'=>true, 'data'=>$html]);
    }


    public function newsubscription(Request $req, $id)
    {
        
        try {

            $req->validate([
                'package_id'=>'required',

            ]);

            if($req->package_id!=null){
                $client=Client::find($id);
                if($client->activesubscription()->exists() && $client->activesubscription!=null){
                return redirect('clients')->with('warning', 'Client has already active subscription');
                }
                $desk=Desk::find($client->desk_id);
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

                $expire_date=$expire_date->addDay(3);

               ClientSubscriptions::create([
                    'desk_id'=>$desk->id,
                    'client_id'=>$client->id,
                    'user_id'=>$desk->deskuser()->exists() ? $desk->deskuser->id : null,
                    'package_id'=>$req->package_id,
                    'subscription_date'=>now(),
                    'expire_date'=>$expire_date,
                    'amount'=>$package->amount,
                    'transaction_no'=>GenerateTransactionNo($client->cnic),
                    'status'=>1,
                ]);
               $client->status=1;
               $client->save();
            }
            DB::commit();
            return redirect('clients')->with('success', 'Client successfully subscribed to new package');

        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
        catch(Throwable $e){
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }



    }



    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $desks=Desk::where('status',1)->get();
        $packages=Packages::where('status',1)->get();
        $client=Client::find($id);
        return view('clients::edit')->withDesks($desks)->with('packages',$packages)->with('client',$client);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $req, $id)
    {
        
        $req->validate([
            'desk_id'=>'required',
            'name'=>'required',
            'parentage'=>'required',
            'dob'=>'required',
            'education'=>'required',
            'gender'=>'required',
            'weight'=>'required',
            'height'=>'required',
            'blood_group'=>'required',            
            'marital_status'=>'required',
            'relation'=>'required',
            'phone_primary'=>['required', 'max:12', 'unique:clients,phone_primary,'.$id],
            'cnic'=>['required', 'max:15', 'unique:clients,cnic,'.$id],
            'email'=>['required', 'unique:clients,email,'.$id],
            'monthly_income'=>'required',
            'address'=>'required',
            'medical_expense'=>'required',

        ]);

        DB::beginTransaction();
        try {
            $inputs=$req->all();
            $client=Client::find($id)->update($inputs);
            DB::commit();
            return redirect('clients')->with('success', 'Client successfully Updated');

        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
        catch(Throwable $e){
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }


    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {

        DB::beginTransaction();
        try {
            Client::find($id)->delete();
            DB::commit();
            return redirect('clients')->with('success', 'Client successfully deleted');

        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
        catch(Throwable $e){
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }

    }


    public function deletesubscription($id)
    {

        DB::beginTransaction();
        try {
            ClientSubscriptions::find($id)->delete();
            DB::commit();
            return redirect('clients')->with('success', 'Client subscription successfully deleted');

        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
        catch(Throwable $e){
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }


    }



}
