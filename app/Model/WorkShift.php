<?php

namespace App\Model;

use App\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Model;

class WorkShift extends Model
{
    use BranchTrait;

    protected $table = 'work_shift';
    protected $primaryKey = 'work_shift_id';

    protected $fillable = [
        'work_shift_id', 'branch_id', 'shift_name', 'start_time', 'end_time', 'late_count_time',
    ];
}
