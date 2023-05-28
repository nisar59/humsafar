<?php

namespace Modules\Deposits\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Modules\Desks\Entities\Desk;
use Modules\Banks\Entities\Bank;
use App\Traits\Loggable;
use Modules\Logs\Entities\Logs;
use App\Models\ClientSubscriptions;

class Deposits extends Model
{
    use HasFactory,SoftDeletes,Loggable;

    protected $table='deposits';
    protected $fillable = ['client_subscription_ids','user_id','desk_id','amount','desposit_date','deposit_slip','deposit_slip_no','bank_id','verified'];
    
    protected $casts = [
        'client_subscription_ids' => 'json'
    ];


    protected static function newFactory()
    {
        return \Modules\Deposits\Database\factories\DepositsFactory::new();
    }


    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function desk()
    {
        return $this->hasOne(Desk::class, 'id', 'desk_id');
    }

    public function clientsubscriptions()
    {
         $related = $this->hasMany(ClientSubscriptions::class);
         $sub_ids=json_decode($this->client_subscription_ids);
    $related->setQuery(
        ClientSubscriptions::whereIn('id', $sub_ids)->getQuery()
    );

    return $related;
    }



    public function logs()
    {
        return $this->hasMany(Logs::class, 'model_id','id')->where('model',$this->getTable());
    }


    public function bank()
    {
        return $this->hasOne(Bank::class, 'id', 'bank_id')->select('id','name','account_title','account_no','status');
    }


}
