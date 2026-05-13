<?php

namespace App\Models\Hrm;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Models\StaffMember;
use App\Models\PaymentMode;
use App\Scopes\CompanyScope;

class PrePayment extends BaseModel
{
    protected $table = 'pre_payments';

    protected $default = ['xid'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'user_id', 'payment_mode_id'];

    protected $appends = ['xid', 'x_user_id', 'x_payment_mode_id'];

    protected $filterable = ['id', 'payment_mode_id'];

    protected $hashableGetterFunctions = [
        'getXUserIdAttribute' => 'user_id',
        'getXPaymentModeIdAttribute' => 'payment_mode_id',
    ];

    protected $casts = [
        'user_id' => Hash::class . ':hash',
        'payment_mode_id' => Hash::class . ':hash',
        'amount' => 'double',
        'payroll_month' => 'integer',
        'payroll_year' => 'integer',
        'deduct_from_payroll' => 'integer',
        'date_time' => 'datetime'
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

    public function paymentMode()
    {
        return $this->hasOne(PaymentMode::class, 'id', 'payment_mode_id');
    }
}
