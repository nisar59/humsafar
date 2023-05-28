<?php

namespace Modules\Logs\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Throwable;
use App\Models\User;
use Carbon\Carbon;
class Logs extends Model
{
    use HasFactory;

    protected $table='logs';
    protected $fillable = ['log_type','user_id','model','model_id','message','data'];
    
    protected static function newFactory()
    {
        return \Modules\Logs\Database\factories\LogsFactory::new();
    }
    
    public static function boot(){
        parent::boot();

        try {
    
        $settings=Settings();
        $duration=$settings->logs_duration!=null ? $settings->logs_duration : 7 ;
        $duration_type=$settings->logs_duration_type!=null ? $settings->logs_duration_type : 'days' ;
        $exact_date=Carbon::parse('Now -'.$duration.' '.$duration_type);
        $logs=self::whereDate('created_at', '<=', $exact_date);
            
            if($logs->count()>0){
                $logs->delete();
                GenerateSystemLog(['model'=>'logs','message'=>'Logs Successfull deleted before '.$exact_date]);
            }

        } catch (Exception $e) {
        GenerateSystemLog(['model'=>'logs','message'=>'Something went wrong while deleting old logs with this error: '.$e->getMessage()]);
        } catch(Throwable $e){
        GenerateSystemLog(['model'=>'logs','message'=>'Something went wrong while deleting old logs with this error: '.$e->getMessage()]);
        }

    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }


}
