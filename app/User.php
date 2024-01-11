<?php

namespace App;

use App\Model\Role;
use App\Model\Employee;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use ESolution\DBEncryption\Traits\EncryptedAttribute;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{

    use Notifiable;
    // use EncryptedAttribute;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'user';
    protected $primaryKey = 'user_id';

    protected $fillable = ['user_id', 'role_id', 'branch_id', 'user_name', 'email', 'password', 'status', 'created_by', 'updated_by', 'device_employee_id', 'google2fa_secret'];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // protected $encryptable = [
    //     'user_name'
    // ];

    public static function scopeUserRole($query, $role)
    {
        return $query->where('role_id', $role);
    }

    public function role()
    {
        return $this->hasOne(Role::class, 'role_id', 'role_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    public function getIsAdminAttribute()
    {
        return $this->role()->where('role_id', 1)->exists();
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
