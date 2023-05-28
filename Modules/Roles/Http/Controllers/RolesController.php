<?php

namespace Modules\Roles\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\Support\Renderable;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    function __construct()
    {
    $permissions_array=[];
    foreach(AllPermissions() as $name=> $permissions){
        foreach($permissions as $permission){
        $permissions_array[]=$name.'.'.$permission;
        }
    }
    $this->_deleteRemovedPermissions($permissions_array);
    $this->__createPermissionIfNotExists($permissions_array);

    }
    public function index()
    {

    if (request()->ajax()) {
        $role=Role::select('id','name')->orderBy('id','ASC')->get();

            return DataTables::of($role)
                ->addColumn('action', function ($row) {
                    if($row->name=='super-admin'){
                        return '';
                    }
                    else{
                        $action='';
                    if(Auth::user()->can('permissions.edit')){
                        $action.='<a class="btn m-1 btn-primary btn-sm" href="'.url('roles/edit/'.$row->id).'"><i class="fas fa-pencil-alt"></i></a>';
                    }
                    if(Auth::user()->can('permissions.edit')){
                        $action.='<a class="btn m-1 btn-danger btn-sm" href="'.url('roles/destroy/'.$row->id).'"><i class="fas fa-trash-alt"></i></a>';
                    }
                        return $action;
                    }
                })
                ->editColumn('name', function ($row) {
                    return $row->name;
                })
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
    }


        return view('roles::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $this->data['permission'] = Permission::get();
        return view('roles::create')->withData($this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $req)
    {
        $req->validate([
            'role' => 'required',
            'permissions' => 'required',
        ]);

    $role = Role::firstOrNew(['name' => $req->role]);
    $role->name=$req->role;
    $role->save();
    $permissions = $req->permissions;

    if (!empty($permissions)) {
        $role->syncPermissions($permissions);
    }
        return redirect('roles')->with('success','Role successfully created');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('roles::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $this->data['role'] = Role::find($id);
        $this->data['permission'] = Permission::get();
        $this->data['rolepermissions'] = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();

        return view('roles::edit')->withData($this->data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'role' => 'required',
            'permissions' => 'required',
        ]);

        $role = Role::find($id);
        $role->name = $request->input('role');
        $role->save();
        $role->syncPermissions($request->input('permissions'));
        return redirect('roles')->with('success','Role successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $role = Role::find($id);
      $role->syncPermissions([]);
      Role::find($id)->delete();
    return redirect('roles')->with('success','Role successfully deleted');
    }

    private function __createPermissionIfNotExists($permissions)
    {

        $exising_permissions = Permission::whereIn('name', $permissions)
                                    ->pluck('name')
                                    ->toArray();

        $non_existing_permissions = array_diff($permissions, $exising_permissions);

        if (!empty($non_existing_permissions)) {
            foreach ($non_existing_permissions as $new_permission) {
                $time_stamp = Carbon::now()->toDateTimeString();
                Permission::create([
                    'name' => $new_permission,
                    'guard_name' => 'web'
                ]);
            }
        }
    }
    private function _deleteRemovedPermissions($permissions)
    {
        $perm=Permission::query();

        foreach (AllPermissions() as $name => $permissions) {
            foreach ($permissions as $permission) {
                $prm=$name.'.'.$permission;
                $perm->where('name','!=',$prm);
            }
        }
        $getall=$perm->get();
        $delperm_array=[];
        foreach($getall as $delperm){
        $delperm_array[]=$delperm->id;        
        }
        //dd($delperm_array);
         $rhp=DB::table('role_has_permissions')->whereIn('permission_id', $delperm_array)->delete();
         //dd($rhp);
        return $perm->delete();

    }
}
