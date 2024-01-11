<?php

namespace App\Model;

use App\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Model;

class EmployeeShift extends Model
{
    // use BranchTrait;
    protected $table = 'employee_shift';
    protected $primaryKey = 'employee_shift_id';

    protected $fillable = [
        'employee_shift_id', 'employee_id', 'finger_print_id', 'branch_id', 'month', 'remarks', 'created_by', 'updated_by',
        'd_1', 'd_2', 'd_3', 'd_4', 'd_5', 'd_6', 'd_7', 'd_8', 'd_9', 'd_10', 'd_11', 'd_12', 'd_13', 'd_14', 'd_15', 'd_16', 'd_17', 'd_18', 'd_19', 'd_20', 'd_21',
        'd_22', 'd_23', 'd_24', 'd_25', 'd_26', 'd_27', 'd_28', 'd_29', 'd_30', 'd_31',
    ];

    public function updated_user()
    {
        return $this->belongsTo(Employee::class, 'updated_by')->without('branch', 'department', 'designation', 'costcenter', 'subdepartment');
    }

    public function shift_request()
    {
        return $this->hasMany(ShiftChangeRequest::class, 'employee_id', 'employee_id');
    }
}
