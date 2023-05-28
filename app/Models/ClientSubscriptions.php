<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;
use Modules\Logs\Entities\Logs;
use Modules\Packages\Entities\Packages;

class ClientSubscriptions extends Model
{
    use HasFactory,SoftDeletes,Loggable;

    protected $table='clients_subscriptions';
    protected $fillable=['client_id','user_id','package_id','desk_id','subscription_date','expire_date','amount','transaction_no','status','services','deposit_id',];



    public function logs()
    {
        return $this->hasMany(Logs::class, 'model_id','id')->where('model',$this->getTable());
    }

    public function package()
    {
        return $this->hasOne(Packages::class,'id','package_id')->select('id','title','amount');
    }

}
