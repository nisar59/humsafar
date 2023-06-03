<?php

namespace Modules\Clients\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ClientSubscriptions;
use Modules\Desks\Entities\Desk;
use App\Traits\Loggable;
use Modules\Logs\Entities\Logs;

class Client extends Model
{
    use HasFactory,SoftDeletes,Loggable;

    protected $table='clients';
    protected $fillable = ['desk_id','name','parentage','dob','education','gender','marital_status','phone_primary','phone_secondary','cnic','email','monthly_income','address','province','district','medical_expense','phone_verified','status'];
    
    protected static function newFactory()
    {
        return \Modules\Clients\Database\factories\ClientFactory::new();
    }

    public function clientdesk()
    {
        return $this->hasOne(Desk::class,'id','desk_id');
    }

    public function activesubscription()
    {
        return $this->hasOne(ClientSubscriptions::class,'client_id','id')->where('clients_subscriptions.expire_date','>',now())->where('clients_subscriptions.status',1)->select('id','client_id','package_id','expire_date');
    }

    public function clientsubscriptions()
    {
        return $this->hasMany(ClientSubscriptions::class,'client_id','id');
    }


    public function logs()
    {
        return $this->hasMany(Logs::class, 'model_id','id')->where('model',$this->getTable());
    }


}
