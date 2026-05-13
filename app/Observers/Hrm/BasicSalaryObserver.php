<?php

namespace App\Observers\Hrm;

use App\Models\Hrm\BasicSalary;

class BasicSalaryObserver
{
    public function saving(BasicSalary $basicSalary)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $basicSalary->company_id = $company->id;
        }
    }
}
