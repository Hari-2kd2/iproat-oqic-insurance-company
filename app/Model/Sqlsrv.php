<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Sqlsrv extends Model
{
    protected $connection = 'sqlsrv';
    protected $fillable   = [
        'name' .
        'place',
    ];
}
