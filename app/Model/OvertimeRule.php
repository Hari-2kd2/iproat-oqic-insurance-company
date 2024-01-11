<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OvertimeRule extends Model
{
    protected $table = 'overtime_rules';
    protected $primaryKey = 'overtime_rule_id';

    protected $fillable = [
        'overtime_rule_id', 'per_min','amount_of_deduction','status','created_by','updated_by'
    ];
}
