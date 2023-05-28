<?php

namespace Modules\Banks\Http\Controllers;

use Throwable;
use App\Imports\BanksImport;
use Illuminate\Http\Request;
use Modules\Banks\Entities\Bank;
use App\Exports\BanksSampleExport;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\Support\Renderable;

class BanksController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (request()->ajax()) {
            $banks=Bank::orderBy('id','ASC')->get();
                return DataTables::of($banks)
                    ->addColumn('action', function ($row) {
                        $action='';
                        if(Auth::user()->can('banks.edit')){
                        $action.='<a class="btn btn-primary m-1 btn-sm" href="'.url('banks/edit/'.$row->id).'"><i class="fas fa-pencil-alt"></i></a>';
                        }
                        if(Auth::user()->can('banks.delete')){
                        $action.='<a class="btn btn-danger m-1 btn-sm" href="'.url('banks/destroy/'.$row->id).'"><i class="fas fa-trash-alt"></i></a>';
                        }
                        return $action;
                    })
    
    
                    ->editColumn('status', function ($row) {
                        if($row->status==1){
                            return  '<a class="btn btn-success btn-sm" href="'.url('banks/status/'.$row->id).'">Active</a>';
                        }
                        else{
                            return  '<a class="btn btn-danger btn-sm" href="'.url('banks/status/'.$row->id).'">Deactive</a>';
                        }
                    })
    
                    ->rawColumns(['action','status'])
                    ->make(true);
        }
        return view('banks::index');
    }

    public function status($id)
    {
        DB::beginTransaction();
        try {
            $bank=Bank::find($id);
            if($bank->status==0){
                $bank->status=1;
            }
            else{
                $bank->status=0;
            }
            $bank->save();
            DB::commit();
            return redirect('banks')->with('success', 'Bank status updated successfully');

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
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('banks::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'account_title'=>'required',
            'account_no'=>['required', 'max:50', 'unique:banks'],
            'code'=>['required', 'max:20'],
            ]);
    
            DB::beginTransaction();
            try {
                $inputs=$request->except('_token');
                $inputs['status']=1;
                Bank::create($inputs);
                DB::commit();
                return redirect('banks')->with('success', 'Bank successfully created');
    
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
        return view('banks::import');
    }

    public function exportsample()
    {
         return Excel::download(new BanksSampleExport, 'banks-sample.xlsx');
    }


    public function importstore(Request $request)
    {
        $request->validate([
        'file' => 'required|mimes:csv,xlsx'
        ]);

        $error_index=0;
        DB::beginTransaction();
        try {
            $existing_banks=[];
            $collection = Excel::toArray(new BanksImport, $request->file('file'));
            foreach($collection[0] as $key => $row){
                $error_index=$key+1;
                if(Bank::where([
                    'name'=>$row['name'],
                    'account_title'=>$row['account_title'],
                    'account_no'=>$row['account_no'],
                    'code'=>$row['code'],
                 ])->count()<1){
                Bank::updateOrCreate(['name'=>$row['name'], 
                        'account_title'=>$row['account_title'], 
                        'account_no'=>$row['account_no'],
                        'code'=>$row['code']],$row);
                }
                else{
                    $existing_banks[]=$row['account_no'];
                }
            }
            DB::commit();
            return redirect('banks')->with('success', 'Banks successfully imported except: '.implode(',', $existing_banks));

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
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('banks::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $this->data['bank'] = Bank::findOrFail($id);
        return view('banks::edit')->withData($this->data);
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
            'name'=>'required',
            'account_title'=>'required',
            'account_no'=>['required', 'max:50', 'unique:banks,account_title,'.$id],
            'code'=>['required', 'max:20'],
            ]);
    
            DB::beginTransaction();
            try {
                Bank::findOrFail($id)->update($request->all());
                DB::commit();
                return redirect('banks')->with('success', 'Bank successfully updated');
    
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
            Bank::findOrFail($id)->delete();
            DB::commit();
            return redirect('banks')->with('success','Bank successfully deleted');

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
