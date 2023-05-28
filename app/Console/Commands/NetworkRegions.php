<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Regions\Entities\Regions;
use Throwable;
use DB;
class NetworkRegions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'network:regions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all regions from MIS';

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
            $data=['key'=>'regions'];
            $type="post";
            $res=ApiCall($type,$url,$data);
            if($res->success){
                $data=json_decode($res->data);
                if(isset($data->data)){
                    foreach ($data->data as $key => $region) {
                       Regions::updateOrCreate(['mis_sync_id'=>$region->id],[
                        'mis_sync_id'=>$region->id,
                        'name'=>$region->name,
                        'code'=>$region->code,
                        'status'=>$region->status,
                       ]);
                    }
                }
            }
            DB::commit();
            GenerateSystemLog(['model'=>'regions','message'=>now().' Regions successfully sync to MIS']);
            $this->line('Regions successfully sync to MIS');
        } catch (Exception $e) {
            DB::rollback();

            GenerateSystemLog(['model'=>'regions','message'=>now().' Something went wrong while regions sync to MIS with this error: '.$e->getMessage()]);

            $this->line('Something went wrong while regions sync to MIS with this error: '.$e->getMessage());
        } catch (Throwable $e){
            DB::rollback();
            GenerateSystemLog(['model'=>'regions','message'=>now().' Something went wrong while regions sync to MIS with this error: '.$e->getMessage()]);
            
            $this->line('Something went wrong while regions sync to MIS with this error: '.$e->getMessage());

        }

    }
}
