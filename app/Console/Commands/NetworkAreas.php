<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Areas\Entities\Areas;
use Throwable;
use DB;
class NetworkAreas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'network:areas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all areas from MIS';

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
            $data=['key'=>'areas'];
            $type="post";
            $res=ApiCall($type,$url,$data);
            if($res->success){
                $data=json_decode($res->data);
                if(isset($data->data)){
                    foreach ($data->data as $key => $area) {
                       Areas::updateOrCreate(['mis_sync_id'=>$area->id],[
                        'mis_sync_id'=>$area->id,
                        'name'=>$area->name,
                        'code'=>$area->code,
                        'region_id'=>$area->region_id,
                        'status'=>$area->status,
                       ]);
                    }
                }
            }
            DB::commit();
            GenerateSystemLog(['model'=>'areas','message'=>now().' Areas successfully sync to MIS']);
            $this->line('Areas successfully sync to MIS');
        } catch (Exception $e) {
            DB::rollback();
            GenerateSystemLog(['model'=>'areas','message'=>now().' Something went wrong while areas sync to MIS with this error: '.$e->getMessage()]);
            $this->line('Something went wrong while regions sync to MIS with this error: '.$e->getMessage());
        } catch (Throwable $e){
            DB::rollback();
            GenerateSystemLog(['model'=>'areas','message'=>now().' Something went wrong while areas sync to MIS with this error: '.$e->getMessage()]);
            $this->line('Something went wrong while regions sync to MIS with this error: '.$e->getMessage());

        }


    }
}
