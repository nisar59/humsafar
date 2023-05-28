<?php

namespace Modules\Feedback\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Feedback\Entities\Feedback;
use DataTables;
use Throwable;
use DB;
use Auth;
use Carbon\Carbon;
class FeedbackController extends Controller
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
        $feedback=Feedback::query();

          if ($req->desk_code != null) {
            $desk=DeskDetailByCode($req->desk_code);

            if($desk!=null){
                $feedback->where('desk_id', $desk->id);
            }
            else{
                $feedback->where('desk_id', rand());
            }
          } 

          if ($req->feedback_type != null) {
            $feedback->where('feedback_type', $req->feedback_type);
          }
          if ($req->feedback_date != null) {
            $feedback->whereDate('created_at', $req->feedback_date);
          }  

        $total = $feedback->count();
        $feedback   = $feedback->offset($strt)->limit($length)->get();

            return DataTables::of($feedback)
                ->setOffset($strt)
                ->with([
                  "recordsTotal"    => $total,
                  "recordsFiltered" => $total,
                ])
                ->addColumn('action', function ($row) {
                    $action='';
                if(Auth::user()->can('feedback.view')){
                $action.='<a class="btn btn-success m-1 btn-sm" href="'.url('feedback/show/'.$row->id).'"><i class="fas fa-eye"></i></a>';
                    }
                if(Auth::user()->can('feedback.delete')){
                $action.='<a class="btn btn-danger m-1 btn-sm" href="'.url('feedback/destroy/'.$row->id).'"><i class="fas fa-trash-alt"></i></a>';
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





                ->addColumn('desk_code', function ($row) {
                    if(ClientDetail($row->client_id)!=null && $client=ClientDetail($row->client_id)){
                        return DeskDetail($client->desk_id)!=null ? DeskDetail($client->desk_id)->desk_code : null;
                    }
                })
                ->editColumn('client_id', function ($row) {
                    return ClientDetail($row->client_id)!=null ? ClientDetail($row->client_id)->name : null;
                })

                ->editColumn('feedback_type', function ($row) {
                    if($row->feedback_type=="positive") {
                      return '<span class="btn btn-sm btn-success">Positive</span>';
                    }
                    else {
                            return '<span class="btn btn-sm btn-danger">Negative</span>';
                        }
                 })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d-m-Y');
                })


                ->rawColumns(['action','desk_code', 'feedback_type', 'modified_by'])
                ->make(true);
    }


        return view('feedback::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('feedback::create');
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
        $feedback=Feedback::with('response.question')->find($id);
        return view('feedback::show')->withData($feedback);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('feedback::edit');
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
            Feedback::find($id)->delete();
            DB::commit();
            return redirect('feedback')->with('success', 'Client Feedback successfully deleted');

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
