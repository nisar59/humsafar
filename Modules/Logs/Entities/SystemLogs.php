<?php

namespace Modules\Logs\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemLogs extends Model
{
    use HasFactory;

    protected $table="system_logs";
    protected $fillable = ['model','message'];
    
    protected static function newFactory()
    {
        return \Modules\Logs\Database\factories\SystemLogsFactory::new();
    }
}
