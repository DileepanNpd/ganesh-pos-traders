<?php

namespace App\Observers\Hrm;

use App\Models\Hrm\LeaveType;

class LeaveTypeObserver
{
    public function saving(LeaveType $leaveType)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $leaveType->company_id = $company->id;
        }
    }
}
