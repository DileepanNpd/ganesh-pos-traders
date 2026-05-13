<?php

namespace App\Observers\Hrm;

use App\Models\Hrm\IncrementPromotion;

class IncrementPromotionObserver
{
    public function saving(IncrementPromotion $incrementPromotion)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $incrementPromotion->company_id = $company->id;
        }
    }
}
