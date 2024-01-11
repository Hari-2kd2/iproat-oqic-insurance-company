<?php

namespace App\Model;

use App\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    // use BranchTrait;
    protected $table = 'leave_type';
    protected $primaryKey = 'leave_type_id';

    protected $fillable = [
        'leave_type_id', 'branch_id', 'leave_type_name', 'num_of_day'
    ];
}
