<?php

namespace App\Observers\Hrm;

use App\Models\Hrm\Attendance;

class AttendanceObserver
{
    public function saving(Attendance $attendance)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $attendance->company_id = $company->id;
        }
    }
}
