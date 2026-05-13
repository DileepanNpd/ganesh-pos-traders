<?php

namespace App\Observers\Hrm;

use App\Models\Hrm\Appreciation;

class AppreciationObserver
{
    public function saving(Appreciation $appreciation)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $appreciation->company_id = $company->id;
        }
    }
}
