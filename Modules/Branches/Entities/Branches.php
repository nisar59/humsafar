<?php

namespace Modules\Branches\Entities;

use Modules\Areas\Entities\Areas;
use Modules\Regions\Entities\Regions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;
use Modules\Logs\Entities\Logs;

class Branches extends Model
{
    use HasFactory,Loggable,SoftDeletes;

    protected $fillable = [
            'mis_sync_id',            
            'region_id',
            'area_id',
            'name',
            'code',
            'status'
    ];
    
    protected static function newFactory()
    {
        return \Modules\Branches\Database\factories\BranchesFactory::new();
    }

    public function areas()
    {
        return $this->belongsTo(Areas::class,'area_id','mis_sync_id');
    }
    
    public function regions()
    {
        return $this->belongsTo(Regions::class,'region_id','mis_sync_id');
    }

    public function logs()
    {
        return $this->hasMany(Logs::class, 'model_id','id')->where('model',$this->getTable());
    }

    
}
