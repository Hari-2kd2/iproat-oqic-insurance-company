<?php

namespace App\Model;

use App\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Model;

class EmployeeInOutData extends Model
{
    // use BranchTrait;
    protected $table = 'view_employee_in_out_data';
    protected $primaryKey = 'employee_attendance_id';
    protected $fillable = [
        "employee_attendance_id",
        "finger_print_id",
        'branch_id',
        "date",
        "in_time_from",
        "in_time",
        "out_time",
        "out_time_upto",
        "working_time",
        "working_hour",
        "in_out_time",
        "status",
        "created_at",
        "updated_at",
        "over_time",
        "early_by",
        "late_by",
        "early_out",
        "late_out",
        "shift_name",
        "device_name",
        "live_status",
        'created_by',
        'updated_by',
        'attendance_status',
        'over_time_status',
        'work_shift_id',
        'remarks',
        'earlylate_out_status',
        'earlylate_in_status'
        // 'approve_over_time_id',
        // 'incentive_details_id',
        // 'comp_off_details_id'
    ];

    protected $with = [
        'employee:finger_id,first_name,last_name,branch_id',
        'updatedBy:updated_by,employee_id,first_name,last_name',
        'compoff', 'incentive', 'workShift'
    ];

    public function compoff()
    {
        return $this->hasMany(CompOff::class, 'employee_attendance_id', 'employee_attendance_id');
    }

    public function incentive()
    {
        return $this->hasMany(Incentive::class, 'employee_attendance_id', 'employee_attendance_id');
    }

    public function workShift()
    {
        return $this->belongsTo(WorkShift::class, 'work_shift_id', 'work_shift_id');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'finger_print_id', 'finger_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Employee::class, 'updated_by');
    }

    public function scopeFilter($query, $status)
    {
        $query->whereHas('employee', function ($q) use ($status) {
            return $q->where('status', $status);
        });

        $query->select(
            "finger_print_id",
            "date",
            "in_time",
            "out_time",
            "working_time",
            "in_out_time",
            "status",
            "created_at",
            "updated_at",
            "over_time",
            "early_by",
            "late_by",
            "shift_name",
            'created_by',
            'updated_by',
            'attendance_status',
            'work_shift_id',
            'remarks',
        );

        return $query;
    }

    public function scopeBranch($query, $branch)
    {
        return $query->whereHas('employee.branch', function ($q) use ($branch) {
            return $q->where('employee.branch_id', $branch);
        });
    }
}
