<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LastRecordID extends Model{
    protected $table = 'live_record_id';
    protected $primaryKey = 'live_id';

}
