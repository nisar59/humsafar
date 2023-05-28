<?php

namespace Modules\Areas\Entities;

use Modules\Regions\Entities\Regions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Branches\Entities\Branches;
use App\Traits\Loggable;
use Modules\Logs\Entities\Logs;

class Areas extends Model
{
    use HasFactory,Loggable,SoftDeletes;

    protected $fillable = [
        'mis_sync_id',
        'name',
        'code',
        'region_id',
        'status'
    ];
    
    protected static function newFactory()
    {
        return \Modules\Areas\Database\factories\AreasFactory::new();
    }

    public function regions()
    {
        return $this->belongsTo(Regions::class,'region_id','mis_sync_id');
    }

    public function branches()
    {
        return $this->hasMany(Branches::class,'area_id','mis_sync_id');
    }

    public function logs()
    {
        return $this->hasMany(Logs::class, 'model_id','id')->where('model',$this->getTable());
    }


}
