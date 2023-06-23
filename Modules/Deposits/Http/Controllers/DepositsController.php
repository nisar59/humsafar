<?php

namespace Modules\Deposits\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Deposits\Entities\Deposits;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ClientSubscriptions;
use App\Imports\DepositsVerificationImport;
use App\Exports\GlobalSamplesExport;
use App\Exports\DepositsExport;
use DataTables;
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
        $req=request();
    if ($req->ajax()) {
        $strt   = $req->start;
        $length = $req->length;
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

        $total = $deposits->count();
        $deposits   = $deposits->offset($strt)->limit($length)->get();

            return DataTables::of($deposits)
                ->setOffset($strt)
                ->with([
                  "recordsTotal"    => $total,
                  "recordsFiltered" => $total,
                ])            
                ->addColumn('action', function ($row) {
                    $action='';
                if(Auth::user()->can('deposits.view')){
/*                $action.='<a class="btn btn-info m-1 btn-sm pay-compensation" data-href="'.url('deposits/compensation/'.$row->id).'" href="javascript:void(0)"><i class="fas fa-money-bill-alt"></i></a>';
*/
                $action.='<a class="btn btn-success m-1 btn-sm show-details " href="javascript:void(0)" data-href="'.url('deposits/show/'.$row->id).'"><i class="fas fa-eye"></i></a>';
                }
                if(Auth::user()->can('deposits.delete')){
                $action.='<a class="btn btn-danger m-1 btn-sm" href="'.url('deposits/destroy/'.$row->id).'"><i class="fas fa-trash-alt"></i></a>';
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

                ->editColumn('client_subscription_ids', function($row){
                    return implode(' ',json_decode($row->client_subscription_ids));
                })

                ->editColumn('desk_id', function ($row) {
                    if($row->desk()->exists() && $row->desk!=null){
                        return $row->desk->desk_code;
                    }
                })
                ->addColumn('user_id', function ($row) {
                    if($row->user()->exists() && $row->user!=null){
                        return $row->user->name;
                    }                
                })
                ->editColumn('bank_account_no', function ($row) {
                    if($row->user()->exists() && $row->user!=null){
                        return $row->user->bank_account_no;
                    }                
                })

                ->editColumn('amount', function ($row) {
                     return number_format($row->amount);
                 })
                ->editColumn('due_compensation', function ($row) {
                     return number_format($row->due_compensation);
                 })
                ->editColumn('paid_compensation', function ($row) {
                     return number_format($row->paid_compensation);
                 })
                ->editColumn('pending_compensation', function ($row) {
                     return number_format($row->pending_compensation);
                 })    

                ->editColumn('deposit_slip_no', function ($row) {
                    return $row->deposit_slip_no;
                })

                ->editColumn('desposit_date', function ($row) {
                     return Carbon::parse($row->desposit_date)->format('d-m-Y');
                 })
                ->editColumn('deposit_slip', function ($row) {
                     return '<img width="50" class="img" src="'.url('img/deposit-slips/'.$row->deposit_slip).'">';
                 })

                ->editColumn('is_verified', function ($row) {
                    if($row->is_verified==1){
                    return  '<a class="btn btn-success btn-sm" data-prompt-msg="" href="javascript:void(0)">verified</a>';
                    }
                    else{
                    return  '<a class="btn btn-danger btn-sm verify-prompt" data-prompt-msg="Are you sure you want to mark this deposit verified" href="'.url('deposits/verify/'.$row->id).'">Pending</a>';
                    }
                })

                ->rawColumns(['action', 'deposit_slip','modified_by', 'is_verified'])
                ->make(true);
    }



        return view('deposits::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('deposits::create');
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

    public function export(Request $req)
    {
        try {
         return Excel::download(new DepositsExport($req), 'deposit.xlsx');
        //return redirect()->back()->with('success', 'Subscription & Services successfully downloaded');
        }catch(Exception $e){
            return redirect()->back()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }catch(Throwable $e){
            return redirect()->back()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */

    public function show($id)
    {

        $deposit=Deposits::with('desk')->find($id);
        $html= view('deposits::deposit-detail')->withDeposit($deposit)->render();
        return response()->json(['success'=>true, 'data'=>$html]);

    }

    public function compensation($id)
    {

        $deposit=Deposits::find($id);
        $html= view('deposits::compensation')->withDeposit($deposit)->render();
        return response()->json(['success'=>true, 'data'=>$html]);

    }

    public function compensationstore(Request $req, $id)
    {
        $req->validate([
            'compensation_to_pay'=>['required', 'numeric']
        ]);
        DB::beginTransaction();
        try {

            $deposit=Deposits::find($id);
            if($deposit==null){
            return redirect()->back()->with('error', 'Something went wrong with this error: Deposit not found');
            }

            $pending=$deposit->pending_compensation;
            $paid=$deposit->paid_compensation;

            $deposit->paid_compensation=(int)$paid + (int)$req->compensation_to_pay;
            $deposit->pending_compensation=(int)$pending - (int)$req->compensation_to_pay;

            $deposit->save();

            DB::commit();
            return redirect('deposits')->with('success', 'Compensation successfully marked as paid');
            
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
        catch(Throwable $e){
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
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


    public function verify($id)
    {
        DB::beginTransaction();
        try {
            Deposits::find($id)->update(['is_verified'=>1]);
            DB::commit();
            return redirect('deposits')->with('success', 'Deposit successfully marked as verified');

        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
        catch(Throwable $e){
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }

    }



    public function bulkverification(Request $req)
    {
        $req->validate([
        'file' => 'required|mimes:csv,xlsx'
        ]);

         DB::beginTransaction();
         try {
             
        $collection = Excel::toArray(new DepositsVerificationImport, $req->file('file'));
        $faulty=[];
        foreach ($collection[0] as $key => $row) {
            $deposits=Deposits::where(['deposit_slip_no'=>$row['deposit_slip_no'], 'amount'=>$row['amount']])->first();
            if($deposits!=null){
                if(!$deposits->update(['is_verified'=>1])){
                    $faulty[]=$row;
                }
            }
            else{
                $faulty[]=$row;
            }
        }
        DB::commit();

        if(count($faulty)>0){
            $req['file_name']='deposit-verify-sample';
            $req['data']=$faulty;

            $name='Not-verified-deposits-'.strtotime(now()).'.xlsx';

            Excel::store(new GlobalSamplesExport($req), $name, 'exports');
            
           $log= GenerateImportExportLogs([
                'file_name'=>$name,
                'success'=>count($collection[0])- count($faulty),
                'failed'=>count($faulty)
            ]);


            return redirect('import-export-logs/show/'.$log->id)->with('warning', 'File Uploaded successfully and verified : '.count($collection[0])- count($faulty). " Not verified : ".count($faulty));
        }
        return redirect()->back()->with('success', 'Deposits successfully marked as verified');

         }catch(Throwable $e){
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }catch(Throwable $e){
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }

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
        DB::beginTransaction();
        try {
            ClientSubscriptions::where('deposit_id', $id)->update(['deposit_id'=>null]);
            Deposits::find($id)->delete();
            DB::commit();
            return redirect('deposits')->with('success', 'Deposit deleted successfully');

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
