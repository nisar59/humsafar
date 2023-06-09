<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Desks\Entities\Desk;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GlobalSamplesExport;
use App\Models\User;
use Throwable;
use DB;
class BulkDeskCreation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'desk:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk Desk Creation';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $req=request();
        $index=0;
        $created=[];
        try {
                $users= User::join('desks', 'users.id', '!=', 'desks.user_id')->select('users.*')->get(); 

                  $faulty=[];
                  foreach ($users as $key => $user) {
                    DB::beginTransaction();
                    $branch=BranchDetail($user->branch_id);

                    if($branch==null){
                        $faulty[]=$user;
                    }
                    else if(Desk::where('branch_id',$user->branch_id)->where('user_id',$user->id)->count()<1){
                      $desk_code=GenerateDeskCode($branch->code);

                     $desk=Desk::create([
                            'desk_code'=>$desk_code,                        
                            'user_id'=>$user->id,
                            'branch_id'=>$branch->mis_sync_id,
                            'area_id'=>$branch->area_id,
                            'region_id'=>$branch->region_id ,
                            'status'=>1,
                      ]);
                     $created[]=$desk->id;
                    }
                    else{
                        $faulty[]=$user;
                    }
                    $index=$index+1;
                    DB::commit();
                }


            if(count($faulty)>0){
                $req['file_name']='users-sample';
                $req['data']=$faulty;

                $name='Not-desk-created-'.strtotime(now()).'.xlsx';

                Excel::store(new GlobalSamplesExport($req), $name, 'exports');
                
               $log= GenerateImportExportLogs([
                    'file_name'=>$name,
                    'success'=>count($users)- count($faulty),
                    'failed'=>count($faulty)
                ]);
            }


            GenerateSystemLog(['model'=>'Desks','message'=>now().' Bulk Desk Creation successfull with Success: '.count($users) - count($faulty).' And failed : '.count($faulty)]);

            $this->line('Bulk Desk Creation successfull with Success: '.count($users) - count($faulty).' And failed : '.count($faulty));        

            
        
        } catch (Exception $e) {
            Desk::whereIn('id',$created)->delete();
            GenerateSystemLog(['model'=>'Desks','message'=>now().' Something went wrong while Bulk Desk Creation with this error: '.$e->getMessage()]);

            $this->line('Something went wrong while Bulk Desk Creation at index '.$index.' with this error: '.$e->getMessage());

        } catch (Throwable $e){
            Desk::whereIn('id',$created)->delete();
            GenerateSystemLog(['model'=>'Desks','message'=>now().' Something went wrong while Bulk Desk Creation with this error: '.$e->getMessage()]);

            $this->line('Something went wrong while Bulk Desk Creation at index '.$index.' with this error: '.$e->getMessage());


        }


    }
}
