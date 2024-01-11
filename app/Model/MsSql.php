<?php

namespace App\Model;

use App\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Model;

class MsSql extends Model
{
    // use BranchTrait;

    protected $table = "ms_sql";
    protected $primaryKey = 'primary_id';
    // protected $primaryKey = null;
    // public $incrementing = false;
    protected $fillable = [
        'primary_id',
        'branch_id',
        'ID',
        'datetime',
        'punching_time',
        'device_employee_id',
        'sms_log',
        'devuid',
        'device',
        'device_name',
        'live_status',
        'employee',
        'status',
        'type',
        'created_at',
        'updated_at',
        'local_primary_id',
        'inout_status',
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class, 'finger_id', 'ID')->with('department');
    }
}
