<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Modules\Clients\Entities\Client;
use App\Models\ClientSubscriptions;
use Modules\Deposits\Entities\Deposits;
use Modules\Feedback\Entities\Feedback;
use Modules\Packages\Entities\Packages;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GlobalSamplesExport;
use Carbon\Carbon;
use Artisan;
use Auth;
use Throwable;
use Hash;
use DB;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {        
        User::find(Auth::id())->update([
            'lock_screen_token'=>Hash::make(Auth::id().now()),
        ]);

        $registered_clients=Client::count();
        $active_clients=Client::has('activesubscription')->count();
        $subscriptions=ClientSubscriptions::where('deposit_id',null)->sum('amount');
        $deposits=ClientSubscriptions::where('deposit_id','!=',null)->sum('amount');
        $actual_deposit=Deposits::sum('amount');

/*/////////////////////////////////////////////////Subscription Progress PIE Chart//////////////////////////////////////////*/

        $active_subscriptions=ClientSubscriptions::whereDate('expire_date','>',now())->count();
        $expired_subscriptions=ClientSubscriptions::whereDate('expire_date','<=',now())->count();

        $piechart=json_encode([
            'labels'=>[
                'Active Subscriptions',
                'Expired Subscriptions'
            ],
            'datasets'=>[
                    [
                        'label'=>' No of Subscriptions',
                        'data'=>[
                            $active_subscriptions,$expired_subscriptions
                        ],
                        'backgroundColor'=>ColorsPack(),
                    ],

            ]

        ]);
/*///////////////////////////////////////////////////END subscription PIE CHART/////////////////////////////////////////////*/


/*///////////////////////////////////////Deposit Bar Chart///////////////////////////////////////////////*/

        $deposits_amounts=Deposits::selectRaw('DATE_FORMAT(desposit_date, "%Y-%m") as month, SUM(amount) as amount')->groupBy('month')->whereYear('desposit_date', now()->format('Y'))->get();

        $subscriptions_amount=ClientSubscriptions::selectRaw('DATE_FORMAT(subscription_date, "%Y-%m") as month, SUM(amount) as amount')->groupBy('month')->where('deposit_id',null)->whereYear('subscription_date', now()->format('Y'))->get();

        $barchart['labels']=[];
        $barchart['datasets'][0]['type']='bar';
        $barchart['datasets'][0]['label']='Cash In Hand';
        $barchart['datasets'][0]['data']=[];
        $barchart['datasets'][0]['backgroundColor']=[];
        $barchart['datasets'][0]['borderColor']=[];
        $barchart['datasets'][0]['borderWidth']=2;



        $barchart['datasets'][1]['type']='bar';
        $barchart['datasets'][1]['label']='Deposits';
        $barchart['datasets'][1]['data']=[];
        $barchart['datasets'][1]['backgroundColor']=[];
        $barchart['datasets'][1]['borderColor']=[];
        $barchart['datasets'][1]['borderWidth']=2;
        
        foreach($subscriptions_amount as $sba){
            $barchart['labels'][]=Carbon::parse($sba->month)->format('F');
            $barchart['datasets'][0]['data'][]=$sba->amount;
            $barchart['datasets'][0]['backgroundColor'][]=ColorsPack()[1];
            $barchart['datasets'][0]['borderColor'][]=ColorsPack()[1];
        }

        foreach($deposits_amounts as $index=> $dpa){
            $barchart['datasets'][1]['data'][]=$dpa->amount;
            $barchart['datasets'][1]['backgroundColor'][]=ColorsPack()[0];
            $barchart['datasets'][1]['borderColor'][]=ColorsPack()[0];
        }

        
        $barchart=json_encode($barchart);
       
/*///////////////////////////////////////END of Deposit Bar Chart/////////////////////////////////////////////*/




/*//////////////////////////////////////////////Feedback Line Chart//////////////////////////////////////////////*/
        $feedback_types=[];
        if(is_array(FeedBackTypes()) && count(FeedBackTypes())>0){
        $feedback_types=FeedBackTypes();
        }

        $feedback=Feedback::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, feedback_type, count(*) as total')->groupBy('month','feedback_type')->whereYear('created_at', now()->format('Y'))->get();

        $linechart['labels']=[];
        
        $fcount=0;
        foreach($feedback_types as $fkey=> $ftype){
            $linechart['datasets'][$fcount]['label']=$ftype;
            $linechart['datasets'][$fcount]['data']=[];    
            $linechart['datasets'][$fcount]['fill']=false;    
            $linechart['datasets'][$fcount]['borderColor']=ColorsPack()[$fcount];    
            $linechart['datasets'][$fcount]['tension']=0.1;  
            $fcount++;
        }
      


        foreach($feedback as $index=> $fdb){
            if(!in_array(Carbon::parse($fdb->month)->format('F'), $linechart['labels'])){           
            $linechart['labels'][]=Carbon::parse($fdb->month)->format('F');
                }

             $fb_index=array_search($fdb->feedback_type,array_keys($feedback_types));

             $linechart['datasets'][$fb_index]['data'][]=$fdb->total;
            }

        $linechart=json_encode($linechart);
       
/*/////////////////////////////////////////////END of Feedback Line Chart///////////////////////////////////////*/

/*///////////////////////////////////////////Clients By Age Pie Chart ////////////////////////////////////////*/

        $clients=Client::selectRaw("TIMESTAMPDIFF(YEAR, DATE(dob), CURDATE()) AS age, COUNT(id) as total")->groupBy('age')
                ->get();

        $piechart_clients['labels']=[];
        $count_clients=[];
        $periods_counts=[
                ['label'=>'Clients of Age 15-25',
                 'count'=>0,],
                ['label'=>'Clients of Age 25-35',
                 'count'=>0,],
                ['label'=>'Clients of Age 35-45',
                 'count'=>0,],
                ['label'=>'Clients of Age 45-55',
                 'count'=>0,],
                ['label'=>'Clients of Age 55-65',
                 'count'=>0,],
                ['label'=>'Clients of Age 65-80',
                 'count'=>0,],
                ['label'=>'Clients older than 80',
                 'count'=>0,],
        ];

        foreach($clients as $clnts){
            if($clnts->age>=15 && $clnts->age<=25){
                $periods_counts[0]['count']=$periods_counts[0]['count']+$clnts->total;
            }elseif($clnts->age>=25 && $clnts->age<=35){
                $periods_counts[1]['count']=$periods_counts[1]['count']+$clnts->total;
            }elseif($clnts->age>=35 && $clnts->age<=45){
                $periods_counts[2]['count']=$periods_counts[2]['count']+$clnts->total;
            }elseif($clnts->age>=45 && $clnts->age<=55){
                $periods_counts[3]['count']=$periods_counts[3]['count']+$clnts->total;
            }elseif($clnts->age>=55 && $clnts->age<=65){
                $periods_counts[4]['count']=$periods_counts[4]['count']+$clnts->total;
            }elseif($clnts->age>=65 && $clnts->age<=80){
                $periods_counts[5]['count']=$periods_counts[5]['count']+$clnts->total;
            }elseif($clnts->age>80){
                $periods_counts[6]['count']=$periods_counts[6]['count']+$clnts->total;
            } 
            else{}    
        }

        foreach($periods_counts as $index => $pc){
            $piechart_clients['labels'][]=$pc['label'];
            $piechart_clients['datasets'][0]['label']='No of Clients';
            $piechart_clients['datasets'][0]['data'][$index]=$pc['count'];
            $piechart_clients['datasets'][0]['backgroundColor'][$index]=ColorsPack()[$index];  
        }

        $piechart_clients=json_encode($piechart_clients);

/*////////////////////////////////////////////END of Clients By Age Pie Chart///////////////////////////////////*/

/*///////////////////////////////////////////////Packages Pie Chart////////////////////////////////////////////*/

        $packages=Packages::with('subscriptions')->get();
        $piechart_packages['labels']=[];
        foreach($packages as $index => $pckg){
            
            $piechart_packages['labels'][]=$pckg->title;
            $piechart_packages['datasets'][0]['label']='No of Subscriptions';
            $piechart_packages['datasets'][0]['data'][$index]=($pckg->subscriptions()->exists() && $pckg->subscriptions!=null) ? $pckg->subscriptions->count() : 0 ;
            $piechart_packages['datasets'][0]['backgroundColor'][$index]=ColorsPack()[$index];  
        }

        $piechart_packages=json_encode($piechart_packages);

/*/////////////////////////////////////END of Packages Pie Chart///////////////////////////////////////////*/


/*/////////////////////////////////////Start Education wise chart/////////////////////////////////////////*/



        $get_clients=Client::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')->groupBy('month')->whereYear('created_at', now()->format('Y'));
       $edu_wise_clients['labels']=[];
       $edu_count=0;
       
        foreach (Education() as $key => $edu) {

            $copy_clients= clone $get_clients;

            $edu_clients=$copy_clients->where('education', $key)->get();

            $edu_wise_clients['datasets'][$edu_count]['type']='bar';
            $edu_wise_clients['datasets'][$edu_count]['label']=$edu;
            $edu_wise_clients['datasets'][$edu_count]['data']=[];
            $edu_wise_clients['datasets'][$edu_count]['backgroundColor']=[];
            $edu_wise_clients['datasets'][$edu_count]['borderColor']=[];
            $edu_wise_clients['datasets'][$edu_count]['borderWidth']=2;
            

                foreach($edu_clients as $educ){
                    $mnth=Carbon::parse($educ->month)->format('F');
                    if(!in_array($mnth,$edu_wise_clients['labels'])){
                        $edu_wise_clients['labels'][]=$mnth;
                    }
                    $edu_wise_clients['datasets'][$edu_count]['data'][]=$educ->count;
                }

            $edu_wise_clients['datasets'][$edu_count]['backgroundColor'][]=ColorsPack()[$edu_count];
            $edu_wise_clients['datasets'][$edu_count]['borderColor'][]=ColorsPack()[$edu_count];


            $edu_count++;

        }

        $edu_wise_clients=@json_encode($edu_wise_clients);


/*/////////////////////////////////////End Education wise chart/////////////////////////////////////////*/



/*////////////////////////////////////////Start User wise cash in hand  ////////////////////////////////////*/

$users=User::with('cash_in_hand')->get();

/*/////////////////////////////////////////End User wise cash in hand /////////////////////////////////////*/






        if($deposits!=$actual_deposit){
            //$deposits=0;
            return view('home', compact(['registered_clients','active_clients','subscriptions','deposits','piechart','barchart', 'linechart','piechart_clients','piechart_packages', 'edu_wise_clients', 'users']))->with('error', 'Something went wrong, there is difference between subscriptions deposits and actual deposits : '.$actual_deposit.' - '.$deposits.'='.$actual_deposit-$deposits);
        }



        return view('home', compact(['registered_clients','active_clients','subscriptions','deposits','piechart','barchart', 'linechart','piechart_clients','piechart_packages', 'edu_wise_clients', 'users']));
    }



    public function checkauth()
    {
       return Auth::check();
    }

    public function lockscreen(Request $req)
    {
        try {
            $user=User::where('lock_screen_token', $req->id)->first();
            if($user==null){
            return redirect('login');
            }
            return view('auth.lock-screen')->withUser($user);    
        } catch (Exception $e) {
            return redirect('login');
        } catch(Throwable $e){
            return redirect('login');
        }

    }


    public function sample(Request $req)
    {
        try {
         return Excel::download(new GlobalSamplesExport($req), $req->file_name.'.xlsx');

        //return redirect()->back()->with('success', 'Subscription & Services successfully downloaded');
        }catch(Exception $e){
            return redirect()->back()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }catch(Throwable $e){
            return redirect()->back()->with('error', 'Something went wrong with this error: '.$e->getMessage());
        }

    }



    public function artisan($command)
    {
        DB::beginTransaction();
        try{
            $sett=Settings();
            $sett->logging=0;
            $sett->save();
            Artisan::call("'".$command."'");
            $res=Artisan::output();
            $sett=Settings();
            $sett->logging=1;
            $sett->save();
            DB::commit(); 
            return redirect()->back()->with('info',$res);
        } catch (Exception $e){
            DB::rollback();
            return redirect()->back()->with('error','Something went wrong with this error '.$e->getMessage());
        }catch (Throwable $e){
            DB::rollback();
            return redirect()->back()->with('error','Something went wrong with this error '.$e->getMessage());
        }


    }


}
