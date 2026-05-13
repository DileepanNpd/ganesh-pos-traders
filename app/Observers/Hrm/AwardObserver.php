<?php

namespace App\Observers\Hrm;

use App\Models\Hrm\Award;

class AwardObserver
{
    public function saving(Award $award)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $award->company_id = $company->id;
        }
    }
}
