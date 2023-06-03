<?php

namespace Modules\Logs\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Logs\Entities\Logs;
use Modules\Logs\Entities\SystemLogs;
use Modules\Logs\Entities\ImportExportLogs;
use Throwable;
use DataTables;
use Auth;
use File;
use DB;
class LogsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {

        if (request()->ajax()) {
            $logs=Logs::all();
                return DataTables::of($logs)
                    ->addColumn('action', function ($row) {
                        $action='';

                    if(Auth::user()->can('logs.view')){
                    $action.='<a class="btn btn-success m-1 btn-sm show-log" href="'.url('logs/show/'.$row->id).'"><i class="fas fa-eye"></i></a>';
                    }
                    if(Auth::user()->can('logs.delete')){
                    $action.='<a class="btn btn-danger m-1 btn-sm" href="'.url('logs/destroy/'.$row->id).'"><i class="fas fa-trash-alt"></i></a>';
                        }
                    
                    return $action;
                    })

                    ->editColumn('user_id', function ($row) {
                        return $row->user_id;
                    })
                    ->editColumn('model', function ($row) {
                        return $row->model;
                    })

                    ->editColumn('model_id', function ($row) {
                        return $row->model_id;
                    })

                    ->editColumn('message', function ($row) {
                        return $row->message;
                    })

                    ->rawColumns(['action'])
                    ->make(true);
        }



        return view('logs::index');
    }



    public function systemlogs()
    {

        if (request()->ajax()) {
            $logs=SystemLogs::orderBy('created_at')->get();
                return DataTables::of($logs)
                    ->addColumn('action', function ($row) {
                        $action='';
                    if(Auth::user()->can('logs.delete')){
                    $action.='<a class="btn btn-danger m-1 btn-sm" href="'.url('system-logs/destroy/'.$row->id).'"><i class="fas fa-trash-alt"></i></a>';
                        }
                    
                    return $action;
                    })

                    ->editColumn('model', function ($row) {
                        return $row->model;
                    })

                    ->editColumn('message', function ($row) {
                        return $row->message;
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d-m-Y');
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }



        return view('logs::index');
    }



    public function importexportlogs()
    {

        if (request()->ajax()) {
            $logs=ImportExportLogs::orderBy('created_at')->get();
                return DataTables::of($logs)
                    ->addColumn('action', function ($row) {
                        $action='';
                    if(Auth::user()->can('logs.view')){
                    $action.='<a class="btn btn-success m-1 btn-sm" href="'.url('import-export-logs/show/'.$row->id).'"><i class="fas fa-eye"></i></a>';
                        }

                    if(Auth::user()->can('logs.delete')){
                    $action.='<a class="btn btn-danger m-1 btn-sm" href="'.url('import-export-logs/destroy/'.$row->id).'"><i class="fas fa-trash-alt"></i></a>';
                        }
                    
                    return $action;
                    })

                    ->editColumn('file_name', function ($row) {
                        return '<a href="'.url('public/exports/'.$row->file_name).'" download>'.$row->file_name.'</a>';
                    })

                    ->editColumn('success', function ($row) {
                        return $row->success;
                    })
                    ->editColumn('failed', function ($row) {
                        return $row->failed;
                    })                    
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d-m-Y');
                    })
                    ->rawColumns(['file_name','action'])
                    ->make(true);
        }



        return view('logs::index');
    }



    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('logs::create');
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
        
        try {
            $log=Logs::find($id);
            $data['success']=true;
            $data['html']=view('logs::show')->withLog($log)->render();
            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['success'=>false, 'html'=>null]);
        }
        catch(Throwable $e){
            return response()->json(['success'=>false, 'html'=>null]);
        }

    }


    public function importexportlogsshow($id)
    {
        try {
            $log=ImportExportLogs::find($id);
            return view('logs::import-export')->withLog($log);

        } catch (Exception $e) {
                return redirect('/')->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
        catch(Throwable $e){
                return redirect('/')->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }

    }


    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('logs::edit');
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
            Logs::findOrFail($id)->delete();
            DB::commit();
            return redirect('logs')->with('success','Log successfully deleted');
        } catch (Exception $e) {
                DB::rollback();
                return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
        catch(Throwable $e){
                DB::rollback();
                return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
    
    }


    public function truncate()
    {
        
        try {
            Logs::truncate();
            return redirect('logs')->with('success','Logs Table successfully truncated');
        } catch (Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
        catch(Throwable $e){
                return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
    
    }



    public function systemlogsdestroy($id)
    {
        DB::beginTransaction();
            
        try {
            SystemLogs::findOrFail($id)->delete();
            DB::commit();
            return redirect('logs')->with('success','System Log successfully deleted');
        } catch (Exception $e) {
                DB::rollback();
                return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
        catch(Throwable $e){
                DB::rollback();
                return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
    
    }





    public function systemlogstruncate()
    {
        
        try {
            SystemLogs::truncate();
            return redirect('logs')->with('success','System Logs Table successfully truncated');
        } catch (Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
        catch(Throwable $e){
                return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
    
    }


    public function importexportlogsdestroy($id)
    {
        DB::beginTransaction();
            
        try {
            $iel=ImportExportLogs::findOrFail($id);

            if($iel!=null){
                $file=public_path('exports/'.$iel->file_name);
                if($iel->delete() && File::delete($file)){
                    DB::commit();
                    return redirect('logs')->with('success','Import Export Log successfully deleted');

                }else{
                    DB::commit();
                    return redirect('logs')->with('error','Import Export Log not deleted');
                }
            }else{
                    DB::commit();
                    return redirect('logs')->with('error','Import Export Log not deleted');

            }
            DB::commit();
            return redirect('logs')->with('success','Import Export Log successfully deleted');
        } catch (Exception $e) {
                DB::rollback();
                return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
        catch(Throwable $e){
                DB::rollback();
                return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
    
    }


    public function importexportlogstruncate()
    {
        
        try {
            $path=public_path('exports');
            if(ImportExportLogs::truncate() &&  File::cleanDirectory($path)){
            return redirect('logs')->with('success','Import Export Logs Table successfully truncated');
            }else{
            return redirect('logs')->with('error','Import Export Logs Table not truncated, please try again');
            }
            
            return redirect('logs')->with('success','System Logs Table successfully truncated');
        } catch (Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
        catch(Throwable $e){
                return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
    
    }




}
