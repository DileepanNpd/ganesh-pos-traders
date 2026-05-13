<?php

namespace App\Http\Controllers\Api\Hrm;

use App\Http\Controllers\ApiBaseController;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Carbon;
use App\Http\Requests\Api\Hrm\PrePayment\IndexRequest;
use App\Http\Requests\Api\Hrm\PrePayment\StoreRequest;
use App\Http\Requests\Api\Hrm\PrePayment\UpdateRequest;
use App\Http\Requests\Api\Hrm\PrePayment\DeleteRequest;
use App\Models\Hrm\PrePayment;
use App\Models\Hrm\Payroll;

class PrePaymentController extends ApiBaseController
{
    protected $model = PrePayment::class;

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

            $query = $query->whereRaw('DATE(pre_payments.date_time) >= ?', [$startDate])
                ->whereRaw('DATE(pre_payments.date_time) <= ?', [$endDate]);
        }

        if ($loggedUser->ability('admin', 'pre_payments_view')) {
            if ($request->has('user_id')) {
                $userId = $this->getIdFromHash($request->user_id);
                $query = $query->where('pre_payments.user_id', $userId);
            }
        } else {
            $query = $query->where('pre_payments.user_id', $loggedUser->id);
        }

        return  $query;
    }

    public function storing($prePayment)
    {
        return $this->storeAndUpdate($prePayment);
    }

    public function updating($prePayment)
    {
        return $this->storeAndUpdate($prePayment);
    }

    public function storeAndUpdate($prePayment)
    {
        $request = request();

        if ($request->deduct_from_payroll == 1) {
            $month = Carbon::createFromFormat('Y-m-d\TH:i:sP', $request->date_time)->format('m');
            $year = Carbon::createFromFormat('Y-m-d\TH:i:sP', $request->date_time)->format('Y');
        } else {
            $month = $request->payroll_month;
            $year = $request->payroll_year;
        }

        $userId = $this->getIdFromHash($request->user_id);
        $payroll = Payroll::where('payrolls.user_id', $userId)
            ->where('payrolls.month', $month)
            ->where('payrolls.year', $year)
            ->count();

        if ($payroll > 0) {
            throw new ApiException("Pre Payment already generated for this employee for month " . $month . '-' . $year);
        }

        $prePayment->payroll_month = $month;
        $prePayment->payroll_year = $year;

        return $prePayment;
    }
}
