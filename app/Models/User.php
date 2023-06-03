<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Modules\Branches\Entities\Branches;
use App\Models\ClientSubscriptions;
use Modules\Desks\Entities\Desk;
use App\Traits\Loggable;
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, Loggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name','email','father_name','cnic','phone','otp','password','pin','lock_screen_token','emp_code','role_name','status','is_block','access_level','branch_id','area_id','region_id', 'bank_name','bank_account_title','bank_account_no', 'bank_account_verified'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function getJWTIdentifier() {
        return $this->getkey();
    }

    public function getJWTCustomClaims() {
        return [];
    }


    public function desk()
    {
       return $this->hasOne(Desk::class,'user_id','id')->where('status',1)->where('branch_id',$this->branch_id);
    }


    public function cash_in_hand()
    {
       return $this->hasMany(ClientSubscriptions::class, 'user_id', 'id')->where('deposit_id',null);
    }

    public function branch()
    {
        return $this->hasOne(Branches::class, 'id', 'branch_id');
    }

}
