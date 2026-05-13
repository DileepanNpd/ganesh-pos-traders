<?php

namespace App\Models\Hrm;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Models\StaffMember;
use App\Models\Hrm\Award;
use App\Scopes\CompanyScope;

class appreciation extends BaseModel
{
    protected $table = 'appreciations';

    protected $default = ['xid', 'date', 'description', 'price_amount', 'price_given'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'company_id', 'award_id', 'user_id', 'created_by'];

    protected $appends = ['xid', 'x_company_id', 'x_award_id', 'x_user_id', 'x_created_by'];

    protected $filterable = ['award_id'];

    protected $hashableGetterFunctions = [
        'getXCompanyIdAttribute' => 'company_id',
        'getXAwardIdAttribute' => 'award_id',
        'getXUserIdAttribute' => 'user_id',
        'getXCreatedByAttribute' => 'created_by',
    ];

    protected $casts = [
        'date' => 'datetime',
        'user_id' => Hash::class . ':hash',
        'award_id' => Hash::class . ':hash',
        'price_amount' => 'double',
        'price_given' => 'json'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function user()
    {
        return $this->hasOne(StaffMember::class, 'id', 'user_id');
    }

    public function award()
    {
        return $this->hasOne(Award::class, 'id', 'award_id');
    }
}
