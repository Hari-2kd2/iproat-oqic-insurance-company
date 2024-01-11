<?php

namespace App\Model;

use App\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Model;

class CompanyAddressSetting extends Model
{
    // use BranchTrait;
    protected $table = 'company_address_settings';
    protected $primaryKey = 'company_address_setting_id';

    protected $fillable = [
        'company_address_setting_id', 'branch_id', 'address'
    ];
}
