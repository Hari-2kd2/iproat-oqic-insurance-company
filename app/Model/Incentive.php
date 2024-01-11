<?php

namespace App\Model;

use App\Model\Employee;
use App\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Model;

class Incentive extends Model
{
    // use BranchTrait;

    protected $table = 'incentives';
    protected $primaryKey = 'incentive_details_id';

    protected $fillable = [
        'incentive_details_id', 'employee_attendance_id', 'finger_print_id', 'employee_id', 'branch_id', 'incentive_date', 'working_date',  'comment',
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class, 'finger_id', 'finger_print_id');
    }
}
