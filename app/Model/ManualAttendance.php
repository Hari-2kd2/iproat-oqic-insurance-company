<?php

namespace App\Model;

use App\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Model;

class ManualAttendance extends Model
{
    // use BranchTrait;

    protected $table = 'manual_attendance';
    protected $primaryKey = 'primary_id';
    protected $fillable = [
        'primary_id',
        'branch_id',
        'ID',
        'type',
        'datetime',
        'status',
        'remarks',
        'device_name',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class, 'finger_id', 'ID')->with('department');
    }
}
