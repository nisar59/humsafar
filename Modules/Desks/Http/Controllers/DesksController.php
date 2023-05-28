<?php

namespace Modules\Desks\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Desks\Entities\Desk;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use DataTables;
use Throwable;
use DB;
use Auth;
class DesksController extends Controller
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
        $desks=Desk::query();

      if ($req->desk_code != null) {
        $desks->where('desk_code','LIKE','%'.$req->desk_code.'%');
      }

      if ($req->branch_id != null) {
        $desks->where('branch_id', $req->branch_id);
      }

        $total = $desks->count();
        $desks   = $desks->offset($strt)->limit($length)->get();


            return DataTables::of($desks)
                ->setOffset($strt)
                ->with([
                  "recordsTotal"    => $total,
                  "recordsFiltered" => $total,
                ])            
                ->addColumn('action', function ($row) {
                    $action='';
                if(Auth::user()->can('desks.edit')){
                $action.='<a class="btn btn-primary btn-sm m-1" href="'.url('desks/edit/'.$row->id).'"><i class="fas fa-pencil-alt"></i></a>';
                }
                if(Auth::user()->can('desks.delete')){
                $action.='<a class="btn btn-danger btn-sm m-1" href="'.url('desks/destroy/'.$row->id).'"><i class="fas fa-trash-alt"></i></a>';
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

                ->editColumn('desk_code', function ($row) {
                    return $row->desk_code;
                })
                ->editColumn('branch_id', function ($row) {
                    return is_null(BranchDetail($row->branch_id)) ? null : BranchDetail($row->branch_id)->name;
                })

                ->editColumn('user_name', function ($row) {
                    if($row->deskuser()->exists() && $row->deskuser!=null){
                    return $row->deskuser->name;
                    }
                })
                ->editColumn('user_phone', function ($row) {
                    if($row->deskuser()->exists() && $row->deskuser!=null){
                    return $row->deskuser->phone;
                    }
                })

                ->editColumn('role_name', function ($row) {
                    if($row->deskuser()->exists() && $row->deskuser!=null){
                    return $row->deskuser->role_name;
                    }                
                })

                ->editColumn('status', function ($row) {
                    if($row->status==1){
                        return  '<a class="btn btn-success btn-sm" href="'.url('desks/status/'.$row->id).'">Active</a>';
                    }
                    else{
                        return  '<a class="btn btn-danger btn-sm" href="'.url('desks/status/'.$row->id).'">Closed</a>';
                    }
                })


                ->editColumn('is_associated', function ($row) {
                    if($row->deskuser()->exists() && $row->deskuser!=null){
                    return '<a class="btn btn-success btn-sm" href="javascript:void()">Associated</a>';;
                    }
                    else{
                    return '<a class="btn btn-danger btn-sm" href="javascript:void()">Not Associated</a>';;
                    }
                })
                ->rawColumns(['action', 'status', 'is_associated', 'modified_by'])
                ->make(true);
    }



        return view('desks::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $users=User::where('status', 1)->get();
        return view('desks::create')->withUsers($users);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store($id)
    {
        DB::beginTransaction();
        try {
              $user= User::find($id);
              $branch=BranchDetail($user->branch_id);
              if($branch==null){
                return redirect()->back()->with('error', 'Sorry Branch Detail not found with this Branch MIS Sync ID :'.$user->branch_id);
              }
              $desk_code=GenerateDeskCode($branch->code);
                if(Desk::where('branch_id',$user->branch_id)->where('user_id',$user->id)->where('status',1)->count()>0){
                return redirect()->back()->with('success', 'This user is alerady exists with this branch, And the associated desk is active and working');
                }
                elseif(Desk::where('branch_id',$user->branch_id)->where('user_id',$user->id)->count()<1){
                  Desk::create([
                        'desk_code'=>$desk_code,                        
                        'user_id'=>$user->id,
                        'branch_id'=>$branch->mis_sync_id,
                        'area_id'=>$branch->area_id,
                        'region_id'=>$branch->region_id ,
                        'status'=>1,
                  ]);
                  DB::commit();
                }
                else{
            return redirect()->back()->with('warning', 'Sorry this user alerady exists with this branch, consider re-association or open your desk');
                }
            return redirect('desks')->with('success', 'Desk successfully created and associated');

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
        return view('desks::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $desk=Desk::find($id);
        $users=User::where('status',1)->where('branch_id',$desk->branch_id)->get();
        return view('desks::edit')->withData($desk)->with('users',$users);
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
            $inputs=$req->only('status');

            if($req->user_id!=null){
                $inputs['user_id']=$req->user_id;
            }

            Desk::find($id)->update($inputs);

            DB::commit();
            return redirect('desks')->with('success', 'Desk updated successfully');

        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
        catch(Throwable $e){
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }




    }



    public function status($id)
    {
        DB::beginTransaction();
        try {
            $user=Desk::find($id);
            if($user->status==0){
                $user->status=1;
            }
            else{
                $user->status=0;
            }
            $user->save();
            DB::commit();
            return redirect('desks')->with('success', 'Desk status updated successfully');

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
           Desk::find($id)->delete();
            DB::commit();
            return redirect('desks')->with('success', 'Desk deleted successfully');

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
