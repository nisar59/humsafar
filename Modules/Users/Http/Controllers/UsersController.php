<?php

namespace Modules\Users\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersSampleExport;
use App\Imports\UsersImport;
use Auth;
use Throwable;
class UsersController extends Controller
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

        $users=User::query();


      if ($req->name != null) {
        $users->where('name','LIKE','%'.$req->name.'%');
      }

      if ($req->cnic != null) {
        $users->where('cnic', $req->cnic);
      }
      if ($req->phone != null) {
        $users->where('phone', $req->phone);
      }    
      if ($req->emp_code != null) {
        $users->where('emp_code', $req->emp_code);
      }
      if ($req->branch_id != null) {
        $users->where('branch_id', $req->branch_id);
      }



        $total = $users->count();
        $users   = $users->offset($strt)->limit($length)->get();

            return DataTables::of($users)
                ->setOffset($strt)
                ->with([
                  "recordsTotal"    => $total,
                  "recordsFiltered" => $total,
                ])
                ->addColumn('action', function ($row) {
                    $action='';

                if(Auth::user()->hasRole('super-admin') AND $row->hasRole('super-admin')){
                $action.='<a class="btn btn-primary btn-sm" href="'.url('users/edit/'.$row->id).'"><i class="fas fa-pencil-alt"></i></a>';

                }
                elseif($row->hasRole('super-admin'))
                {
                    return '';
                }
                    else{
                if(Auth::user()->can('users.edit')){
                $action.='<a class="btn btn-primary m-1 btn-sm" href="'.url('users/edit/'.$row->id).'"><i class="fas fa-pencil-alt"></i></a>';
                }
                if(Auth::user()->can('users.delete')){
                $action.='<a class="btn btn-danger m-1 btn-sm" href="'.url('users/destroy/'.$row->id).'"><i class="fas fa-trash-alt"></i></a>';
                    }
                if(Auth::user()->can('users.edit')){
                $action.='<a class="btn btn-success m-1 btn-sm" href="'.url('desks/create/'.$row->id).'"><i class="fas fa-tv"></i></a>';
                }



                }
                return $action;
                })

                ->editColumn('name', function ($row) {
                    return $row->name;
                })
                ->editColumn('role', function ($row) {
                    return count($row->roles)>0 ? $row->roles[0]->name : '';
                }) 
                ->editColumn('father_name', function ($row) {
                    return $row->father_name;
                })

                ->editColumn('cnic', function ($row) {
                    return $row->cnic;
                })
                ->editColumn('phone', function ($row) {
                    return $row->phone;
                })

                ->editColumn('role_name', function ($row) {
                    return $row->role_name;
                })

                ->editColumn('emp_code', function ($row) {
                    return $row->emp_code;
                })

                ->editColumn('status', function ($row) {
                    if($row->status==1){
                        return  '<a class="btn btn-success btn-sm" href="'.url('users/status/'.$row->id).'">Active</a>';
                    }
                    else{
                        return  '<a class="btn btn-danger btn-sm" href="'.url('users/status/'.$row->id).'">Deactive</a>';
                    }
                })

                ->editColumn('branch_id', function ($row) {
                    return BranchDetail($row->branch_id)!=null ? BranchDetail($row->branch_id)->name: '';
                })

                ->editColumn('access_level', function ($row) {
                    return $row->access_level;
                })
                ->rawColumns(['action','role','status'])
                ->make(true);
    }


        return view('users::index');    
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $this->data['role']=Role::where('name','!=','super-admin')->get();
        return view('users::create')->withData($this->data);
    }



    public function import()
    {
        return view('users::import');
    }

    public function exportsample()
    {
         return Excel::download(new UsersSampleExport, 'users-sample.xlsx');
    }


    public function importstore(Request $req)
    {
        $req->validate([
        'file' => 'required|mimes:csv,xlsx'
        ]);

        $error_index=0;
        DB::beginTransaction();
        try {
            $existing_users=[];
            $collection = Excel::toArray(new UsersImport, $req->file('file'));
            foreach($collection[0] as $key => $row){
                $error_index=$key+1;

                if(User::where('cnic',$row['cnic'])->orWhere('phone',$row['phone'])->orWhere('emp_code',$row['emp_code'])->count()<1){
                User::updateOrCreate(['cnic'=>$row['cnic'], 'phone'=>$row['phone'], 'emp_code'=>$row['emp_code']],$row);
                }
                else{
                    $existing_users[]=$row['cnic']."\n";
                }

            }
            DB::commit();
            return redirect('users')->with('success', 'Users successfully imported except: '.implode(',', $existing_users));

        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong at index '.$error_index.' with this error: '.$e->getMessage());
        }
        catch(Throwable $e){
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong at index '.$error_index.' with this error: '.$e->getMessage());
        }


    }








    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $req)
    {

        $req->validate([
        'name'=>'required',
        'father_name'=>'required',
        'cnic'=>['required', 'max:15', 'unique:users'],
        'phone'=>['required', 'max:12', 'unique:users'],
        'emp_code'=>['required', 'max:7', 'unique:users'],
        'role_name'=>['required', 'max:2'],
        'access_level'=>['required', 'max:15'],
        'branch_id'=>['required', 'max:7'],
        'password' => ['required', 'string', 'min:8'],
        ]);

        DB::beginTransaction();
        try {
            $branch=BranchDetail($req->branch_id);
            $inputs=$req->except('_token','role');
            $inputs['password']=Hash::make($req->password);
            $inputs['image']='dummy.png';
            $inputs['status']=1;
            $inputs['is_block']=0;
            $inputs['area_id']=$branch->area_id;
            $inputs['region_id']=$branch->region_id;

            $user=User::create($inputs);
            $user->assignRole($req->role);
            DB::commit();
            return redirect('users')->with('success', 'User successfully created');

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
        return view('users::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $this->data['role']=Role::where('name','!=','super-admin')->get();
        $this->data['user']=User::with('roles')->find($id);
        return view('users::edit')->withData($this->data);
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
        'name'=>'required',
        'father_name'=>'required',
        'cnic'=>['required', 'max:15', 'unique:users,cnic,'.$id],
        'phone'=>['required', 'max:12', 'unique:users,phone,'.$id],
        'emp_code'=>['required', 'max:7', 'unique:users,emp_code,'.$id],
        'role_name'=>['required', 'max:2'],
        'access_level'=>['required', 'max:15'],
        'branch_id'=>['required', 'max:7'],
         ]);

       

        DB::beginTransaction();
        try {
            $branch=BranchDetail($req->branch_id);
            $inputs=$req->except('_token','role');
            if($req->password!=null){
            $inputs['password']=Hash::make($req->password);
            }
            $inputs['area_id']=$branch->area_id;
            $inputs['region_id']=$branch->region_id;

            $user=User::find($id);
            $user->update($inputs);
            $user->roles()->detach();
            $user->assignRole($req->role);
            DB::commit();
            return redirect('users')->with('success', 'User successfully updated');

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
            $user=User::find($id);
            if($user->status==0){
                $user->status=1;
            }
            else{
                $user->status=0;
            }
            $user->save();
            DB::commit();
            return redirect('users')->with('success', 'User status updated successfully');

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
            $user=User::find($id);
            $user->roles()->detach();
            $user->delete();
            DB::commit();
            return redirect('users')->with('success', 'User successfully deleted');

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
