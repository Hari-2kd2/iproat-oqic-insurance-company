<?php

namespace App\Model;

use App\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Model;

class EarnLeaveRule extends Model
{
    // use BranchTrait;

    protected $table = 'earn_leave_rule';
    protected $primaryKey = 'earn_leave_rule_id';

    protected $fillable = [
        'earn_leave_rule_id','branch_id', 'for_month','day_of_earn_leave'
    ];
}
