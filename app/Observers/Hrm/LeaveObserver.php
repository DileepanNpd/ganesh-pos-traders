<?php

namespace App\Observers\Hrm;

use App\Models\Hrm\Leave;

class LeaveObserver
{
    public function saving(Leave $leave)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $leave->company_id = $company->id;
        }
    }
}
