<?php

namespace App\Model;

use App\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    // use BranchTrait;

    protected $table = 'holiday';
    protected $primaryKey = 'holiday_id';

    protected $fillable = [
        'holiday_id', 'holiday_name', 'branch_id'
    ];
}
