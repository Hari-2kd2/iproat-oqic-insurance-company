<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TelephoneAllowanceDeductionRule extends Model
{
    protected $_table      = 'telephone_allowance_deduction_rules';
    protected $_primaryKey = 'telephone_allowance_deduction_rule_id';

    protected $_fillable = [
        'telephone_allowance_deduction_rule_id', 'cost_per_call', 'limit_per_month', 'remarks', 'status',
    ];
}
