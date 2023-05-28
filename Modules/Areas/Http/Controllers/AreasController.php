<?php

namespace Modules\Areas\Http\Controllers;

use Throwable;
use App\Imports\AreasImport;
use Illuminate\Http\Request;
use Modules\Areas\Entities\Areas;
use App\Exports\AreasSampleExport;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Regions\Entities\Regions;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\Support\Renderable;

class AreasController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
      
        if (request()->ajax()) {
            $areas=Areas::with('regions')->latest();
                return DataTables::of($areas)
                    ->addColumn('action', function ($row) {
                        $action='';

                    if(Auth::user()->can('areas.edit')){
                    $action.='<a class="btn btn-primary m-1 btn-sm" href="'.url('areas/edit/'.$row->id).'"><i class="fas fa-pencil-alt"></i></a>';
                    }
                    if(Auth::user()->can('areas.delete')){
                    $action.='<a class="btn btn-danger m-1 btn-sm" href="'.url('areas/destroy/'.$row->id).'"><i class="fas fa-trash-alt"></i></a>';
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

                    ->editColumn('status', function ($row) {
                        if($row->status==1){
                            return  '<a class="btn btn-success btn-sm" href="'.url('areas/status/'.$row->id).'">Active</a>';
                        }
                        else{
                            return  '<a class="btn btn-danger btn-sm" href="'.url('areas/status/'.$row->id).'">Deactive</a>';
                        }
                    })

                    ->rawColumns(['action','status', 'modified_by'])
                    ->make(true);
        }

        return view('areas::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $regions = Regions::all();
        return view('areas::create',compact('regions'));
    }

    public function import()
    {
        return view('areas::import');
    }

    public function exportsample()
    {
         return Excel::download(new AreasSampleExport, 'areas-sample.xlsx');
    }


    public function importstore(Request $req)
    {
        $req->validate([
        'file' => 'required|mimes:csv,xlsx'
        ]);

        $error_index=0;
        DB::beginTransaction();
        try {
            $existing_areas=[];
            $collection = Excel::toArray(new AreasImport, $req->file('file'));
            foreach($collection[0] as $key => $row){
                $error_index=$key+1;

                if( Areas::where('mis_sync_id',$row['mis_sync_id'])
                    ->orWhere('name',$row['name'])
                    ->orWhere('code',$row['code'])
                    ->orWhere('region_id',$row['region_id'])
                    ->count()<1 )
                {
                Areas::updateOrCreate(
                    ['mis_sync_id'=>$row['mis_sync_id'], 
                    'name'=>$row['name'], 'code'=>$row['code'],
                    'region_id'=>$row['region_id']
                    ],$row);
                }
                else{
                    $existing_areas[]=$row['mis_sync_id']."\n";
                }

            }
            DB::commit();
            return redirect('areas')->with('success', 'Areas successfully imported except: '.implode(',', $existing_areas));

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
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'mis_sync_id'=>['required'],
            'name'=>['required'],
            'code'=>['required', 'max:10'],
            'region_id'=>['required'],
            ]);

            DB::beginTransaction();
            try {
                Areas::create($request->all());  
                DB::commit();
                return redirect('areas')->with('success', 'Area successfully created');

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
        return view('areas::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $this->data['area']=Areas::findOrFail($id);
        $this->data['regions'] = Regions::all();
        return view('areas::edit')->withData($this->data);;
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
        ]);

        DB::beginTransaction();
            
        try {
                Areas::findOrFail($id)->update($request->all());
                DB::commit();
                return redirect('areas')->with('success', 'Area successfully Updated');
               
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
            Areas::findOrFail($id)->delete();
            DB::commit();
            return redirect('areas')->with('success','Area successfully deleted');

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
            $area=Areas::find($id);
            if($area->status==0){
                $area->status=1;
            }
            else{
                $area->status=0;
            }
            $area->save();
            DB::commit();
            return redirect('areas')->with('success', 'Area status updated successfully');

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
