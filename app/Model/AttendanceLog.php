<?php

namespace App\Model;

use App\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{

    use BranchTrait;
    protected $table = "attendance_log";
    protected $primaryKey = 'attendance_log_id';
    
    protected $fillable = [
        'branch_id',
        'employeeId',
        'date',
        'time',
        'deviceSerial',
        'deviceId',
        'locationName',
        'locationId',
        'mode',
        'type',
        'deviceName',
        'lateEntry',
        'companyDisplayId',
        'companyName',
        'lastEvaluatedKey',
        'size',
        'status',
        'created_at',
        'updated_at',
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class, 'finger_id', 'employeeId')->with('department');
    }
}
