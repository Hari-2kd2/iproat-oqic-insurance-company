<?php

namespace App\Model;

use App\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Model;

class EmployeeOverTime extends Model
{
    // use BranchTrait;
    protected $table = 'employee_over_times';
    protected $primaryKey = 'employee_over_time_id';

    protected $fillable = [
        'employee_over_time_id', 'date', 'employee_id', 'work_shift_id',
         'Overtime_duration','status'
    ];

    public function employee(){
        return $this->belongsTo(Employee::class,'employee_id');
    }
}
