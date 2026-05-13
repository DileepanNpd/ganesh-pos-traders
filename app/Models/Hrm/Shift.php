<?php

namespace App\Models\Hrm;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class Shift extends BaseModel
{
    protected $table = 'shifts';

    protected $default = ['xid', 'name', 'clock_in_time', 'clock_out_time', 'late_mark_after', 'early_clock_in_time', 'allow_clock_out_till', 'self_clocking', 'allowed_ip_address'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id',];

    protected $appends = ['xid',];

    protected $filterable = ['name'];

    protected $casts = [
        'is_deletable' => 'integer',
        'self_clocking' => 'integer',
        'allowed_ip_address' => 'json'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }
}
