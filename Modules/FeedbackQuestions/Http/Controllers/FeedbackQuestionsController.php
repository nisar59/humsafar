<?php

namespace Modules\FeedbackQuestions\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\FeedbackQuestions\Entities\FeedbackQuestions;
use Modules\FeedbackQuestions\Entities\QuestionOptions;
use Carbon\Carbon;
use DataTables;
use Throwable;
use DB;
use Auth;
class FeedbackQuestionsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {

    if (request()->ajax()) {
        $question=FeedbackQuestions::orderBy('id','ASC')->get();
            return DataTables::of($question)
                ->addColumn('action', function ($row) {
                    $action='';
                if(Auth::user()->can('feedback-questions.edit')){
                $action.='<a class="btn btn-primary m-1 btn-sm" href="'.url('feedback-questions/edit/'.$row->id).'"><i class="fas fa-pencil-alt"></i></a>';
                }
                if(Auth::user()->can('feedback-questions.delete')){
                $action.='<a class="btn btn-danger m-1 btn-sm" href="'.url('feedback-questions/destroy/'.$row->id).'"><i class="fas fa-trash-alt"></i></a>';
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





                ->editColumn('feedback_type', function ($row) {
                    return ucwords( str_replace('_', ' ',$row->feedback_type));
                })
                ->editColumn('question_type', function ($row) {
                    return $row->question_type;
                })

                ->editColumn('question', function ($row) {
                     return $row->question;
                 })
                ->editColumn('status', function ($row) {
                    if($row->status==1){
                    return  '<a class="btn btn-success btn-sm" href="'.url('feedback-questions/status/'.$row->id).'">Active</a>';
                    }
                    else{
                    return  '<a class="btn btn-danger btn-sm" href="'.url('feedback-questions/status/'.$row->id).'">Deactive</a>';
                    }
                })

                ->rawColumns(['action', 'status', 'modified_by'])
                ->make(true);
    }


        return view('feedbackquestions::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('feedbackquestions::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $req)
    {

        $req->validate([
            'feedback_type'=>'required',
            'question'=>'required',
            'question_type'=>'required',
        ]);

        DB::beginTransaction();
        try {

            $inputs=$req->except('_token','option');
            $inputs['status']=1;

            $feedback_question=FeedbackQuestions::create($inputs);
            if($req->question_type!='text' && $req->question_type!='' && $req->option!=null){
                foreach ($req->option as $key => $value) {
                QuestionOptions::create([
                    'feedback_question_id'=>$feedback_question->id,
                    'option_value'=>$value,
                ]);
                }

            }

            DB::commit();
            return redirect('feedback-questions')->with('success', 'Feedback Questions successfully created');

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
        return view('feedbackquestions::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $question=FeedbackQuestions::with('options')->find($id);
        return view('feedbackquestions::edit')->withData($question);
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
            'feedback_type'=>'required',
            'question'=>'required',
            'question_type'=>'required',
        ]);

        DB::beginTransaction();
        try {
            $inputs=$req->except('_token');
            $feedback_question=FeedbackQuestions::find($id)->update($inputs);
            QuestionOptions::where('feedback_question_id', $id)->delete();
            if($req->question_type!='text' && $req->question_type!='' && $req->option!=null){
                foreach ($req->option as $key => $value) {
                QuestionOptions::create([
                    'feedback_question_id'=>$id,
                    'option_value'=>$value,
                ]);
                }

            }

            DB::commit();
            return redirect('feedback-questions')->with('success', 'Feedback Questions successfully updated');

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
            $question=FeedbackQuestions::find($id);
            if($question->status==0){
                $question->status=1;
            }
            else{
                $question->status=0;
            }
            $question->save();
            DB::commit();
            return redirect()->back()->with('success', 'Feedback Question status updated successfully');

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
            $question=FeedbackQuestions::find($id);
            $question->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Feedback Question successfully deleted');

        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }
        catch(Throwable $e){
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }

    }

    public function optiondestroy($id)
    {

        DB::beginTransaction();
        try {
            $question=QuestionOptions::find($id);
            $question->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Feedback Question option successfully deleted');

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
