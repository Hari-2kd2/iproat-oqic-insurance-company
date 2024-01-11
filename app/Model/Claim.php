<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'remarks', 'attachments', 'status', 'approved_by', 'rejected_by'
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class, 'employee_id', 'employee_id')->select('employee_id', 'finger_id', 'first_name', 'last_name');
    }
}
