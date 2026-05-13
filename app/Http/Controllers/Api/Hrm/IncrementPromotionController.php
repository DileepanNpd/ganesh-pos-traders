<?php

namespace App\Http\Controllers\Api\Hrm;

use App\Http\Controllers\ApiBaseController;
use App\Models\StaffMember;
use App\Models\Hrm\BasicSalary;
use App\Http\Requests\Api\Hrm\IncrementPromotion\IndexRequest;
use App\Http\Requests\Api\Hrm\IncrementPromotion\StoreRequest;
use App\Http\Requests\Api\Hrm\IncrementPromotion\UpdateRequest;
use App\Http\Requests\Api\Hrm\IncrementPromotion\DeleteRequest;
use App\Models\Hrm\IncrementPromotion;

class IncrementPromotionController extends ApiBaseController
{
    protected $model = IncrementPromotion::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {
        $loggedUser = user();
        $request = request();

        // Dates Filters
        if ($request->has('dates') && $request->dates != "") {
            $dates = explode(',', $request->dates);
            $startDate = $dates[0];
            $endDate = $dates[1];

            $query = $query->whereRaw('increments_promotions.date >= ?', [$startDate])
                ->whereRaw('increments_promotions.date <= ?', [$endDate]);
        }

        if ($loggedUser->ability('admin', 'increments_promotions_view')) {
            if ($request->has('user_id')) {
                $userId = $this->getIdFromHash($request->user_id);
                $query = $query->where('increments_promotions.user_id', $userId);
            }
        } else {
            $query = $query->where('increments_promotions.user_id', $loggedUser->id);
        }

        // status Filters
        if ($request->has('type') && $request->type != "all") {
            $incrementPromotionType = $request->type;
            $query = $query->where('increments_promotions.type', $incrementPromotionType);
        }
        return  $query;
    }

    public function stored(IncrementPromotion $incrementPromotion)
    {
        $loggedUser = user();
        $request = request();
        $userId = $incrementPromotion->user_id;

        if ($request->has('update_basic_salary') && $request->update_basic_salary  && $loggedUser->ability('admin', 'basic_salaries_edit')) {
            $basicSalary = BasicSalary::where('basic_salaries.user_id', $userId)
                ->first();

            if ($basicSalary) {
                $basicSalary->basic_salary = $incrementPromotion->net_salary;
                $basicSalary->save();
            }
        }

        if ($request->has('update_designation') && $request->update_designation  && $loggedUser->ability('admin', 'designations_edit')) {
            $staffMember = StaffMember::find($userId);

            $staffMember->designation_id = $incrementPromotion->promoted_designation_id;
            $staffMember->save();
        }

        return $incrementPromotion;
    }
}
