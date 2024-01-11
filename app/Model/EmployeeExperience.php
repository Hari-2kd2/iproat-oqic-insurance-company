<?php

namespace App\Model;

use App\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Model;

class EmployeeExperience extends Model
{
    // use BranchTrait;

    protected $table = 'employee_experience';
    protected $primaryKey = 'employee_experience_id';
    protected $fillable = [
        'employee_experience_id', 'employee_id', 'branch_id', 'organization_name', 'designation', 'from_date', 'to_date', 'skill', 'responsibility'
    ];
}
