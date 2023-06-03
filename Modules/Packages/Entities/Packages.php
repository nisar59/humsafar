<?php

namespace Modules\Packages\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;
use Modules\Logs\Entities\Logs;
use App\Models\ClientSubscriptions;
class Packages extends Model
{
    use HasFactory,SoftDeletes,Loggable;
    
    protected $fillable = ['title','amount','compensation','subscription_type','subscription_duration','status'];

    protected static function newFactory()
    {
        return \Modules\Packages\Database\factories\PackagesFactory::new();
    }

    public function subscriptions()
    {
       return $this->hasMany(ClientSubscriptions::class, 'package_id', 'id');
    }

    public function logs()
    {
        return $this->hasMany(Logs::class, 'model_id','id')->where('model',$this->getTable());
    }


}
