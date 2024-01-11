<?php

namespace App\Model;

use App\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Model;

class AttendanceReport extends Model
{
    // use BranchTrait;
    protected $table = "attendance_report";
    protected $fillable = [
        'branch_id',
        'finger_print_id',
        'in_time',
        'working_time',
        'status',
        'created_at',
        'updated_at',
    ];
}
