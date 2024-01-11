<?php

namespace App\Model;

use App\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    // use BranchTrait;
    
    protected $table = 'designation';
    protected $primaryKey = 'designation_id';

    protected $fillable = [
        'designation_id','branch_id', 'designation_name'
    ];


}
