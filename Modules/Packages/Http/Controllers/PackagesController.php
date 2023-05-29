<?php

namespace Modules\Packages\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Packages\Entities\Packages;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\DB;
use Throwable;

class PackagesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (request()->ajax()) {
            $packages=Packages::latest();
                return DataTables::of($packages)
                    ->addColumn('action', function ($row) {
                    $action='';
                    if(Auth::user()->can('packages.edit')){
                    $action.='<a class="btn btn-primary m-1 btn-sm" href="'.url('packages/edit/'.$row->id).'"><i class="fas fa-pencil-alt"></i></a>';
                    }
                    if(Auth::user()->can('packages.delete')){
                    $action.='<a class="btn btn-danger m-1 btn-sm" href="'.url('packages/destroy/'.$row->id).'"><i class="fas fa-trash-alt"></i></a>';
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




                    ->editColumn('title', function ($row) {
                        return $row->title;
                    })
                    ->editColumn('amount', function ($row) {
                        return $row->amount;
                    })
                    ->editColumn('subscription_type', function ($row) {
                        return subscriptionTypes($row->subscription_type)['title'];
                    })
                    ->editColumn('subscription_duration', function ($row) {
                        return $row->subscription_duration;
                    })

                    ->editColumn('status', function ($row) {
                        if($row->status==1){
                            return  '<a class="btn btn-success btn-sm" href="'.url('packages/status/'.$row->id).'">Active</a>';
                        }
                        else{
                            return  '<a class="btn btn-danger btn-sm" href="'.url('packages/status/'.$row->id).'">Deactive</a>';
                        }
                    })

                    ->rawColumns(['action','status', 'modified_by'])
                    ->make(true);
        }

        return view('packages::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('packages::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        // return $request->all();
        $request->validate([
            'title'=>['required','unique:packages'],
            'amount'=>['required','numeric'],
            'subscription_type'=>['required'],
            //'subscription_duration'=>['required', 'numeric'],
            ]);

            DB::beginTransaction();
            try {
                $inputs=$request->all();
                $inputs['subscription_duration']=1;
                Packages::create($inputs);
                DB::commit();
                return redirect('packages')->with('success', 'Package successfully created');

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
        return view('packages::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $this->data['package']=Packages::find($id);
        return view('packages::edit')->withData($this->data);
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
            'title'=>['required'],
            'amount'=>['required','numeric'],
            'subscription_type'=>['required'],
            //'subscription_duration'=>['required','numeric'],
        ]);

        DB::beginTransaction();
            
        try {
                Packages::findOrFail($id)->update($request->all());
                DB::commit();
                return redirect('packages')->with('success', 'Package successfully Updated');
               
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
            Packages::findOrFail($id)->delete();
            DB::commit();
            return redirect('packages')->with('success','Packages successfully deleted');
               
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
            $package=Packages::find($id);
            if($package->status==0){
                $package->status=1;
            }
            else{
                $package->status=0;
            }
            $package->save();
            DB::commit();
            return redirect('packages')->with('success', 'Package status updated successfully');

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
