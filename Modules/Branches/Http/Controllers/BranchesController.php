<?php

namespace Modules\Branches\Http\Controllers;

use Throwable;
use Illuminate\Http\Request;
use App\Imports\BranchesImport;
use Modules\Areas\Entities\Areas;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BranchesSampleExport;
use Modules\Regions\Entities\Regions;
use Modules\Branches\Entities\Branches;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\Support\Renderable;

class BranchesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (request()->ajax()) {
            $branches=Branches::with(['regions','areas'])->latest();
                return DataTables::of($branches)
                    ->addColumn('action', function ($row) {
                        $action='';

                    if(Auth::user()->can('branches.edit')){
                    $action.='<a class="btn btn-primary m-1 btn-sm" href="'.url('branches/edit/'.$row->id).'"><i class="fas fa-pencil-alt"></i></a>';
                    }
                    if(Auth::user()->can('branches.delete')){
                    $action.='<a class="btn btn-danger m-1 btn-sm" href="'.url('branches/destroy/'.$row->id).'"><i class="fas fa-trash-alt"></i></a>';
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



                    ->editColumn('mis_sync_id', function ($row) {
                        return $row->mis_sync_id;
                    })
                    ->editColumn('name', function ($row) {
                        return $row->name;
                    })

                    ->editColumn('code', function ($row) {
                        return $row->code;
                    })
                  
                    ->editColumn('region_id', function ($row) {
                        return $row->regions->name ?? "Null";
                    })

                    ->editColumn('area_id', function ($row) {
                        return $row->areas->name ?? "Null";
                    })

                    ->editColumn('status', function ($row) {
                        if($row->status==1){
                            return  '<a class="btn btn-success btn-sm" href="'.url('branches/status/'.$row->id).'">Active</a>';
                        }
                        else{
                            return  '<a class="btn btn-danger btn-sm" href="'.url('branches/status/'.$row->id).'">Deactive</a>';
                        }
                    })

                    ->rawColumns(['action','status', 'modified_by'])
                    ->make(true);
        }
        return view('branches::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $this->data['regions'] = Regions::all();
        return view('branches::create')->withData($this->data);
    }

    public function import()
    {
        return view('branches::import');
    }

    public function exportsample()
    {
         return Excel::download(new BranchesSampleExport, 'branches-sample.xlsx');
    }


    public function importstore(Request $req)
    {
        $req->validate([
        'file' => 'required|mimes:csv,xlsx'
        ]);

        $error_index=0;
        DB::beginTransaction();
        try {
            $existing_branches=[];
            $collection = Excel::toArray(new BranchesImport, $req->file('file'));
            foreach($collection[0] as $key => $row){
                $error_index=$key+1;

                if( Branches::where('mis_sync_id',$row['mis_sync_id'])
                    ->orWhere('name',$row['name'])
                    ->orWhere('code',$row['code'])
                    ->orWhere('region_id',$row['region_id'])
                    ->orWhere('area_id',$row['area_id'])
                    ->count()<1 )
                {
                Branches::updateOrCreate(
                    ['mis_sync_id'=>$row['mis_sync_id'], 
                    'name'=>$row['name'], 'code'=>$row['code'],
                    'region_id'=>$row['region_id'],
                    'area_id'=>$row['area_id']
                    ],$row);
                }
                else{
                    $existing_branches[]=$row['mis_sync_id']."\n";
                }

            }
            DB::commit();
            return redirect('branches')->with('success', 'Branches successfully imported except: '.implode(',', $existing_branches));

        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong at index '.$error_index.' with this error: '.$e->getMessage());
        }
        catch(Throwable $e){
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong at index '.$error_index.' with this error: '.$e->getMessage());
        }


    }

    public function regionAreas(Request $request)
    {
        $areas = Areas::where('region_id',$request->region_id)->get();
        $options='';
        $options .= '<option value="">Select Areas </option>';
        foreach($areas as $area)
        {
            $options .='<option value="'.$area->mis_sync_id.'">'.$area->name.'</option>';
        }
        return response()->json([
            "data"=>$options,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {

       $request->validate([
        'mis_sync_id'=>['required'],
        'name'=>['required'],
        'code'=>['required', 'max:10'],
        'region_id'=>['required'],
        'area_id'=>['required'],
        ]);

        DB::beginTransaction();

        try {
          
           Branches::create($request->all());             
           DB::commit();
           return redirect('branches')->with('success', 'Branch successfully created');

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
        return view('branches::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function loadEditAreas($region_id,$area_id)
    {
        $areas = Areas::where('region_id',$region_id)->get();
        $options='';
        $options .= '<option value="">Select Areas </option>';
        foreach($areas as $area)
        {
           if($area->mis_sync_id == $area_id)
           {
            $options .='<option selected value="'.$area->mis_sync_id.'">'.$area->name.'</option>';
           }else{
            $options .='<option value="'.$area->mis_sync_id.'">'.$area->name.'</option>';
           }
           
        }
        return response()->json([
            "data"=>$options,
        ]);
    }


    public function edit($id)
    {
        $this->data['branches']=Branches::findOrFail($id);
        $this->data['regions'] = Regions::all();
        return view('branches::edit')->withData($this->data);
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
            'mis_sync_id'=>['required'],
            'name'=>['required'],
            'code'=>['required', 'max:10'],
            'region_id'=>['required'],
            'area_id'=>['required'],
        ]);

        DB::beginTransaction();         
        try {
                Branches::findOrFail($id)->update($request->all());
                DB::commit();
                return redirect('branches')->with('success', 'Branch successfully Updated');
               
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
            Branches::findOrFail($id)->delete();
            DB::commit();
            return redirect('branches')->with('success','Branch successfully deleted');
               
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
            $branch=Branches::find($id);
            if($branch->status==0){
                $branch->status=1;
            }
            else{
                $branch->status=0;
            }
            $branch->save();
            DB::commit();
            return redirect('branches')->with('success', 'Branch status updated successfully');

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
