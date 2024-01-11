<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EmployeeFoodAndTelephoneDeduction extends Model
{
    protected $_table      = 'employee_food_and_telephone_deductions';
    protected $_primaryKey = 'employee_food_and_telephone_deduction_id';

    protected $_fillable = [
        'employee_food_and_telephone_deduction_id', 'employee_id','branch_id', 'month_of_deduction', 'finger_print_id', 'food_allowance_deduction_rule_id', 'telephone_allowance_deduction_rule_id', 'breakfast_count', 'lunch_count', 'dinner_count', 'call_consumed_per_month', 'remarks', 'status',
    ];
}
