<?php

namespace Modules\Logs\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ImportExportLogs extends Model
{
    use HasFactory;

    protected $table='import_export_logs';
    protected $fillable = ['file_name','success','failed'];
    
    protected static function newFactory()
    {
        return \Modules\Logs\Database\factories\ImportExportLogsFactory::new();
    }
}
