<?php

namespace Modules\Regions\Entities;


use Modules\Areas\Entities\Areas;
use Illuminate\Database\Eloquent\Model;
use Modules\Branches\Entities\Branches;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;
use Modules\Logs\Entities\Logs;

class Regions extends Model
{
    use HasFactory,Loggable,SoftDeletes;

    protected $fillable = [
        'mis_sync_id',
        'name',
        'code',
        'status'
        
    ];
    
    protected static function newFactory()
    {
        return \Modules\Regions\Database\factories\RegionsFactory::new();
    }

    public function areas()
    {
        return $this->hasMany(Areas::class,'region_id','mis_sync_id');
    }

    public function branches()
    {
        return $this->hasMany(Branches::class,'region_id','mis_sync_id');
    }

    public function logs()
    {
        return $this->hasMany(Logs::class, 'model_id','id')->where('model',$this->getTable());
    }


    
}
