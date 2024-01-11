<?php

namespace App\Model;

use App\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Model;

class CompOff extends Model
{
    // use BranchTrait;

    protected $table = 'comp_off';
    protected $primaryKey = 'comp_off_details_id';

    protected $fillable = [
        'comp_off_details_id', 'employee_attendance_id', 'finger_print_id', 'employee_id', 'branch_id', 'off_date', 'working_date', 'off_timing', 'comment','balance_hour'
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class, 'finger_id', 'finger_print_id');
    }
}
