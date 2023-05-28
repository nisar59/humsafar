<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Branches\Entities\Branches;
use Throwable;
use DB;
class NetworkBranches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'network:branches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all branches from MIS';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            $url='https://mis.akhuwat.org.pk/api/cih/get-network';
            $data=['key'=>'branches'];
            $type="post";
            $res=ApiCall($type,$url,$data);
            if($res->success){
                $data=json_decode($res->data);
                if(isset($data->data)){
                    foreach ($data->data as $key => $branch) {
                       Branches::updateOrCreate(['mis_sync_id'=>$branch->id],[
                        'mis_sync_id'=>$branch->id,
                        'region_id'=>$branch->region_id,
                        'area_id'=>$branch->area_id,
                        'name'=>$branch->name,
                        'code'=>$branch->code,
                        'status'=>$branch->status,
                       ]);
                    }
                }
            }
            DB::commit();
            GenerateSystemLog(['model'=>'branches','message'=>now().' Branches successfully sync to MIS']);
            $this->line('Branches successfully sync to MIS');

        } catch (Exception $e) {
            DB::rollback();
            GenerateSystemLog(['model'=>'branches','message'=>now().' Something went wrong while branches sync to MIS with this error: '.$e->getMessage()]);
            $this->line('Something went wrong while regions sync to MIS with this error: '.$e->getMessage());
        } catch (Throwable $e){
            DB::rollback();
            GenerateSystemLog(['model'=>'branches','message'=>now().' Something went wrong while branches sync to MIS with this error: '.$e->getMessage()]);
            $this->line('Something went wrong while regions sync to MIS with this error: '.$e->getMessage());

        }


    }
}
