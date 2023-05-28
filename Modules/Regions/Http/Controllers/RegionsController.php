<?php

namespace Modules\Regions\Http\Controllers;

use Throwable;
use Illuminate\Http\Request;
use App\Imports\RegionsImport;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Exports\RegionsSampleExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Regions\Entities\Regions;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\Support\Renderable;

class RegionsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (request()->ajax()) {
            $regions=Regions::latest();
                return DataTables::of($regions)
                    ->addColumn('action', function ($row) {
                        $action='';

                    if(Auth::user()->can('regions.edit')){
                    $action.='<a class="btn btn-primary m-1 btn-sm" href="'.url('regions/edit/'.$row->id).'"><i class="fas fa-pencil-alt"></i></a>';
                    }
                    if(Auth::user()->can('regions.delete')){
                    $action.='<a class="btn btn-danger m-1 btn-sm" href="'.url('regions/destroy/'.$row->id).'"><i class="fas fa-trash-alt"></i></a>';
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

                    ->editColumn('status', function ($row) {
                        if($row->status==1){
                            return  '<a class="btn btn-success btn-sm" href="'.url('regions/status/'.$row->id).'">Active</a>';
                        }
                        else{
                            return  '<a class="btn btn-danger btn-sm" href="'.url('regions/status/'.$row->id).'">Deactive</a>';
                        }
                    })

                    ->rawColumns(['action','status', 'modified_by'])
                    ->make(true);
        }

        return view('regions::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('regions::create');
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
            'name'=>['required','unique:regions'],
            'code'=>['required', 'max:10'],
            ]);

            DB::beginTransaction();
            try {
                Regions::create($request->all());
                DB::commit();
                return redirect('regions')->with('success', 'Region successfully created');

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
        return view('regions::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $this->data['region']=Regions::findOrFail($id);
        return view('regions::edit')->withData($this->data);
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
        ]);

        DB::beginTransaction();
            
        try {
                Regions::findOrFail($id)->update($request->all());
                DB::commit();
                return redirect('regions')->with('success', 'Region successfully Updated');
               
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
            Regions::findOrFail($id)->delete();
            DB::commit();
            return redirect('regions')->with('success','Region successfully deleted');
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
            $region=Regions::find($id);
            if($region->status==0){
                $region->status=1;
            }
            else{
                $region->status=0;
            }
            $region->save();
            DB::commit();
            return redirect('regions')->with('success', 'Region status updated successfully');

        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
        catch(Throwable $e){
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
    }


    public function import()
    {
        return view('regions::import');
    }

    public function exportsample()
    {
         return Excel::download(new RegionsSampleExport, 'regions-sample.xlsx');
    }


    public function importstore(Request $req)
    {
        $req->validate([
        'file' => 'required|mimes:csv,xlsx'
        ]);

        $error_index=0;
        DB::beginTransaction();
        try {
            $existing_regions=[];
            $collection = Excel::toArray(new RegionsImport, $req->file('file'));
            foreach($collection[0] as $key => $row){
                $error_index=$key+1;
                if(
                    Regions::where('mis_sync_id',$row['mis_sync_id'])
                    ->orWhere('name',$row['name'])
                    ->orWhere('code',$row['code'])
                    ->count()<1){

                Regions::updateOrCreate(
                    ['mis_sync_id'=>$row['mis_sync_id'], 
                    'name'=>$row['name'], 
                    'code'=>$row['code']
                    ],$row);
                }
                else{
                    $existing_regions[]=$row['mis_sync_id']."\n";
                }

            }
            DB::commit();
            return redirect('regions')->with('success', 'Regions successfully imported except: '.implode(',', $existing_regions));

        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong at index '.$error_index.' with this error: '.$e->getMessage());
        }
        catch(Throwable $e){
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong at index '.$error_index.' with this error: '.$e->getMessage());
        }


    }

}
