<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdvanceDeduction extends Model
{
    protected $table = 'advance_deduction';
    protected $primaryKey = 'advance_deduction_id';

    protected $fillable = [
        'advance_deduction_id','branch_id', 'employee_id','advance_amount','date_of_advance_given','deduction_amouth_per_month', 'no_of_month_to_be_deducted', 'status'
    ];
}
