<?php

namespace App\Model;

use App\Model\Branch;
use App\Model\Employee;
use App\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Model;

class ApproveOverTime extends Model
{
    // use BranchTrait;

    protected $table = "approve_over_time";
    protected $primaryKey = "approve_over_time_id";
    protected $fillable = [
        'approve_over_time_id', 'finger_print_id', 'branch_id', 'date', 'actual_overtime', 'approved_overtime', 'remark', 'status', 'updated_by', 'created_by', 'created_at', 'updated_at',
    ];
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function employee()
    {
        return $this->hasOne(Employee::class, 'finger_id', 'finger_print_id');
    }
}
