<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LeavePermission extends Model
{
    protected $table = 'leave_permission';
    protected $primaryKey = 'leave_permission_id';

    protected $fillable = [
        'leave_permission_id','employee_id','permission_duration','leave_permission_date','leave_permission_purpose','status','department_head',
        'department_approval_status','from_time','to_time','head_remarks'
    ];
    public function employee(){
        return $this->belongsTo(Employee::class,'employee_id')->withDefault(
          [
            'employee_id' => 0,
            'user_id' => 0,
            'department_id' => 0,
            'email'=>'unknown email',
            'first_name'=>'unknown',
            'last_name'=>'unknown last name'

          ]
        );
    }

}
