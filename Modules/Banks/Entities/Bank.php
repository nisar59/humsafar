<?php

namespace Modules\Banks\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;
class Bank extends Model
{
    use HasFactory,SoftDeletes,Loggable;

    protected $fillable = [
        'name',
        'account_title',
        'account_no',
        'code',
        'status'
    ];
    
    protected static function newFactory()
    {
        return \Modules\Banks\Database\factories\BankFactory::new();
    }
}
