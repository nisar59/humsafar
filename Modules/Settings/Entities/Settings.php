<?php

namespace Modules\Settings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Loggable;

class Settings extends Model
{
    use HasFactory,Loggable;

    protected $fillable = ['id'];
    protected $table='settings';
    protected static function newFactory()
    {
        return \Modules\Settings\Database\factories\SettingsFactory::new();
    }
}
