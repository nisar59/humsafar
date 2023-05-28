<?php

namespace Modules\Feedback\Http\Controllers\API;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Feedback\Entities\Feedback;
use Modules\FeedbackQuestions\Entities\FeedbackQuestions;
use Modules\Feedback\Entities\FeedbackResponse;
use Validator;
use Throwable;
use Auth;
use DB;
class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('feedback::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($type)
    {
        try {

            $res=['success'=>true,'message'=>null,'errors'=>[],'data'=>null];
            $questions=FeedbackQuestions::with('options')->where('feedback_type', $type)->where('status',1)->select('id','feedback_type','question','question_type')->get();

            $res=['success'=>true,'message'=>'Feedback Questions successfully fetched','errors'=>[],'data'=>$questions];

            return response()->json($res);
        } catch (Exception $e) {
            DB::rollback();
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
            return response()->json($res);
                    }
        catch(Throwable $e){
            DB::rollback();
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
            return response()->json($res);        
        }    
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $req)
    {

        DB::beginTransaction();
        try {
        $val = Validator::make($req->all(),[
            'client_id'=>'required',
            'feedback_type'=>'required',
            'questions_answers'=>'required|array|min:1'
        ]);

            $res=['success'=>true,'message'=>null,'errors'=>[],'data'=>null];
            if ($val->fails()) {
                $res=['success'=>false,'message'=>'Required fields are missing','errors'=>$val->messages()->all(),'data'=>null];
                return response()->json($res);
            }

            $feedback=Feedback::create(['client_id'=>$req->client_id, 'feedback_type'=>$req->feedback_type]);

            foreach ($req->questions_answers as $key => $qa) {
                    $response=is_array($qa['response']) ? implode(',', $qa['response']) : $qa['response']; 
                    FeedbackResponse::create(['clients_feedback_id'=>$feedback->id,'feedback_question_id'=>$qa['id'],'response'=>$response]);
            }

            DB::commit();
            $res=['success'=>true,'message'=>'Client Feedback successfully saved','errors'=>[],'data'=>null];
            return response()->json($res);
        } catch (Exception $e) {
            DB::rollback();
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
            return response()->json($res);
                    }
        catch(Throwable $e){
            DB::rollback();
            $res=['success'=>false,'message'=>'Something went wrong with this error: '.$e->getMessage(),'errors'=>[],'data'=>null];
            return response()->json($res);        
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('feedback::show');
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
        //
    }
}
