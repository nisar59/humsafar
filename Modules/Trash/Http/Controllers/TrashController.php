<?php

namespace Modules\Trash\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Regions\Entities\Regions;
use Modules\Areas\Entities\Areas;
use Modules\Branches\Entities\Branches;
use Modules\Desks\Entities\Desk;
use Throwable;
use App\User;
use DB;
class TrashController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('trash::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('trash::create');
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
    public function show($module)
    {
        $data=[];
        $data=DB::table($module)->whereNotNull('deleted_at')->get();
        return view('trash::show')->withData($data)->withModule($module);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('trash::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function restore($module, $id)
    {
        try {
            DB::table($module)->where('id',$id)->update(['deleted_at'=>null]);

            return redirect()->back()->with('success', 'Record successfully restored');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        } catch (Throwable $e){
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());

        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($module,$id)
    {
        try {
            DB::table($module)->where('id',$id)->delete();
            return redirect()->back()->with('success', 'Record successfully deleted');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        } catch (Throwable $e){
            return redirect()->back()->withInput()->with('error', 'Something went wrong with this error: '.$e->getMessage());

        }
    }
}
