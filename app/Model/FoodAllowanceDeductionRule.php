<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FoodAllowanceDeductionRule extends Model
{

    protected $table      = 'food_allowance_deduction_rules';
    protected $primaryKey = 'food_allowance_deduction_rule_id';

    protected $fillable = [
        'food_allowance_deduction_rule_id', 'breakfast_cost', 'lunch_cost', 'dinner_cost', 'remarks', 'status',
    ];
}
