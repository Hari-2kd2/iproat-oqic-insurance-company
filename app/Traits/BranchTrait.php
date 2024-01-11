<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BranchTrait
{
    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->branch_id = (session('logged_session_data.branch_id'));
        });

        if (null != session('logged_session_data') && (session('logged_session_data.role_id')) != 1) {
            self::addGlobalScope(function (Builder $builder) {
                $builder->where('branch_id', (session('logged_session_data.branch_id')));
            });
        }
    }
}
