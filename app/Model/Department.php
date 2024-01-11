<?php

namespace App\Model;

use App\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{

    use BranchTrait;
    
    protected $table = 'department';
    protected $primaryKey = 'department_id';

    protected $fillable = [
        'department_id','branch_id', 'department_name'
    ];

    public static function listData($department_id = "")
    {
        if ($department_id)
            return Department::where('department_id', $department_id)->pluck('department_name', 'department_id')->toArray();
        else
            return Department::pluck('department_name', 'department_id')->toArray();
    }
}
