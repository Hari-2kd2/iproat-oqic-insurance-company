<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table = 'device';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'name', 'ip', 'protocol', 'model', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'port', 'username', 'password','devIndex','devResponse','verification_status','type','device_employee_id'
    ];
}
