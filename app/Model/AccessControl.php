<?php

namespace App\Model;

use App\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Model;

class AccessControl extends Model{
    // use BranchTrait;
    protected $table = 'employee_access_control';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'branch_id','employee', 'department', 'device', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'
    ];

}
