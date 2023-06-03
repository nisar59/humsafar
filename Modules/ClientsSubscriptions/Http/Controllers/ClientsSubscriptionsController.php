<?php

namespace Modules\ClientsSubscriptions\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\ClientSubscriptions;
use Modules\Deposits\Entities\Deposits;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SubScriptionServices;
use App\Exports\SubscriptionServicesExport;
use App\Exports\GlobalSamplesExport;
use Modules\Clients\Entities\Client;
use DataTables;
use Throwable;
use DB;
use Auth;
use User;
use Carbon\Carbon;
class ClientsSubscriptionsController extends Controller
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

        $total = $clientssub->count();
        $clientssub   = $clientssub->offset($strt)->limit($length)->get();


            return DataTables::of($clientssub)
                ->setOffset($strt)
                ->with([
                  "recordsTotal"    => $total,
                  "recordsFiltered" => $total,
                ])
                ->addColumn('action', function ($row) {
                    $action='';
                if(Auth::user()->can('clients-subscriptions.delete')){
                $action.='<a class="btn btn-danger btn-sm" href="'.url('clients/delete-subscription/'.$row->id).'"><i class="fas fa-trash-alt"></i></a>';
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
                    if(DeskDetail($row->desk_id)!=null){
                    return DeskDetail($row->desk_id)->desk_code;
                    }
                })
                ->editColumn('client_id', function ($row) {
                    if(ClientDetail($row->client_id)!=null){
                    return ClientDetail($row->client_id)->name;
                    }
                })

                ->editColumn('user_id', function ($row) {
                     return User($row->user_id);
                 })
                ->editColumn('package_id', function ($row) {
                    if(PackageDetail($row->package_id)!=null){
                    return PackageDetail($row->package_id)->title;
                    }
                })

                ->editColumn('subscription_date', function ($row) {
                     return Carbon::parse($row->subscription_date)->format('d-m-Y');
                 })
                ->editColumn('expire_date', function ($row) {
                     return Carbon::parse($row->expire_date)->format('d-m-Y');
                 })
                ->editColumn('amount', function ($row) {
                     return number_format($row->amount);
                 })

                ->editColumn('services', function ($row) {
                    if($row->services==1){
                    return  '<a class="btn btn-success btn-sm" data-prompt-msg="" href="javascript:void(0)">Activated</a>';
                    }
                    else{
                    return  '<a class="btn btn-danger btn-sm " data-prompt-msg="Are you sure you want to Active this Service" data-href="'.url('clients-subscriptions/services/'.$row->id).'">Pending</a>';
                    }
                })
                ->editColumn('deposit_id', function ($row) {
                    if($row->deposit_id!=null){
                    return  '<a class="btn btn-success btn-sm" href="javascript:void(0)">Deposited</a>';
                    }
                    else{
                    return  '<a class="btn btn-danger btn-sm" href="javascript:void(0)">Not Deposited</a>';
                    }
                })

                ->rawColumns(['action', 'services', 'modified_by','deposit_id'])
                ->make(true);
    }




        return view('clientssubscriptions::index');
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


    public function export(Request $req)
    {   
        try {
         return Excel::download(new SubscriptionServicesExport($req), 'subscriptions.xlsx');

        //return redirect()->back()->with('success', 'Subscription & Services successfully downloaded');
        }catch(Exception $e){
            return redirect()->back()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }catch(Throwable $e){
            return redirect()->back()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
    }

    public function bulkservices(Request $req)
    {
        $req->validate([
        'file' => 'required|mimes:csv,xlsx'
        ]);

         DB::beginTransaction();
         try {
             
        $collection = Excel::toArray(new SubScriptionServices, $req->file('file'));

        $faulty=[];
        
            foreach ($collection[0] as $key => $row) {
               $client=Client::where('cnic', $row['cnic'])->first();

               if($client!=null && $client->clientsubscriptions()->exists() && isset($row['username']) && $row['password']){
                    $client->clientsubscriptions()->update(['services'=>1]);

                    /*/////////////////////Send SMS Notification///////////////////////////*/

                    $msg="Sehat Kahani Services are Activated. username :".$row['username']." password :".$row['password']. " For any issues, call us: 0309-8889395";
                    $msg_res=SendMessage($client->phone_primary, $msg);
                    if($msg_res->success){
                        $msg_res="And SMS Notification sent";
                    }else{
                        $client->clientsubscriptions()->update(['services'=>0]);
                        $faulty[]=$row['cnic'];
                    }

                    /*/////////////////////////End SMS Notification////////////////////////*/
               }else{
                $faulty[]=$row;
               }


            }
            DB::commit();
            if(count($faulty)>0){
                $req['file_name']='subscriptions-sample';
                $req['data']=$faulty;

                $name='Not-verified-subscriptions-'.strtotime(now()).'.xlsx';

                Excel::store(new GlobalSamplesExport($req), $name, 'exports');
                
               $log= GenerateImportExportLogs([
                    'file_name'=>$name,
                    'success'=>count($collection[0])- count($faulty),
                    'failed'=>count($faulty)
                ]);


                return redirect('import-export-logs/show/'.$log->id)->with('warning', 'File Uploaded successfully and Activated : '.count($collection[0])- count($faulty). " Pending : ".count($faulty));
            }


            return redirect()->back()->with('success', 'Services successfully Activated & SMS Notification sent');

         }catch(Throwable $e){
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }catch(Throwable $e){
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }

    }


    /**
     * Verify the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function services($id)
    {
        DB::beginTransaction();
        try {
            $sub=ClientSubscriptions::find($id);
            $client=ClientDetail($sub->client_id);
            if($client==null){
            DB::commit();
            return redirect('clients-subscriptions')->with('error', 'Client not found against this subscriptions');
            }
            $sub->update(['services'=>1]);
            DB::commit();

            /*/////////////////////Send SMS Notification///////////////////////////*/

            $msg="Your Services of ".Settings()->portal_name." have been successfully Actived.";
            $msg_res=SendMessage($client->phone_primary, $msg);
            if($msg_res->success){
                $msg_res="And SMS Notification sent";
            }else{
                $msg_res="And SMS Notification Not sent because ".$msg_res->message;
            }

            /*/////////////////////////End SMS Notification////////////////////////*/


            return redirect('clients-subscriptions')->with('success', 'Services successfully Actived '.$msg_res);

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
        //
    }
}
