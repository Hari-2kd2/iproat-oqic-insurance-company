<?php

namespace App\Model;

use App\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Model;

class HolidayDetails extends Model
{
    // use BranchTrait;

    protected $table = 'holiday_details';
    protected $primaryKey = 'holiday_details_id';

    protected $fillable = [
        'holiday_details_id', 'holiday_id', 'branch_id', 'from_date', 'to_date', 'leave_timing', 'comment'
    ];

    public function holiday()
    {
        return $this->belongsTo(Holiday::class, 'holiday_id', 'holiday_id');
    }
}
