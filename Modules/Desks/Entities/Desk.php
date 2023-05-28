<?php

namespace Modules\Desks\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Traits\Loggable;
use Modules\Logs\Entities\Logs;

class Desk extends Model
{
    use HasFactory,SoftDeletes,Loggable;

    protected $table='desks';
    protected $fillable=['desk_code','user_id','branch_id','area_id','region_id','status'];

    protected static function newFactory()
    {
        return \Modules\Desks\Database\factories\DeskFactory::new();
    }

    public function deskuser()
    {
       return $this->hasOne(User::class,'id','user_id')->where('status',1)->where('branch_id',$this->branch_id);
    }


    public function logs()
    {
        return $this->hasMany(Logs::class, 'model_id','id')->where('model',$this->getTable());
    }

}
