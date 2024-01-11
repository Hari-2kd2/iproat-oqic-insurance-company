<?php

namespace App\Model;

use App\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // use BranchTrait;

    protected $table = 'role';
    protected $primaryKey = 'role_id';

    protected $fillable = [
        'role_id', 'role_name', 'branch_id'
    ];
}
