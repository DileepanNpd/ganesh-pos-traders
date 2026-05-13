<?php

namespace App\Models\Hrm;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Models\StaffMember;
use App\Scopes\CompanyScope;
use App\Models\Hrm\PrePayment;

class PayrollComponent extends BaseModel
{
    protected $table = 'payroll_components';

    protected $default = ['xid'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'pre_payment_id', 'payroll_id', 'user_id'];

    protected $appends = ['xid', 'x_pre_payment_id', 'x_payroll_id', 'x_user_id'];

    protected $hashableGetterFunctions = [
        'getXPayrollIdAttribute' => 'payroll_id',
        'getXPrePaymentIdAttribute' => 'pre_payment_id',
        'getXUserIdAttribute' => 'user_id',
    ];

    protected $casts = [
        'payroll_id' => Hash::class . ':hash',
        'pre_payment_id' => Hash::class . ':hash',
        'user_id' => Hash::class . ':hash',
        'is_earning' => 'boolean',
        'amount' => 'double',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function prePayment()
    {
        return $this->hasOne(PrePayment::class, 'id', 'pre_payment_id');
    }

    public function payroll()
    {
        return $this->hasOne(Payroll::class, 'id', 'payroll_id');
    }

    public function user()
    {
        return $this->hasOne(StaffMember::class, 'id', 'user_id');
    }
}
