<?php

namespace App\Http\Controllers\Api\Hrm;

use App\Http\Controllers\ApiBaseController;
use App\Models\Expense;
use App\Http\Requests\Api\Hrm\Payroll\IndexRequest;
use App\Http\Requests\Api\Hrm\Payroll\StoreRequest;
use App\Http\Requests\Api\Hrm\Payroll\UpdateRequest;
use App\Http\Requests\Api\Hrm\Payroll\DeleteRequest;
use App\Http\Requests\Api\Hrm\Payroll\PayrollGenerateRequest;
use App\Http\Requests\Api\Hrm\Payroll\UpdateStatusRequest;
use App\Http\Requests\Api\Hrm\Payroll\PayrollRegenerateRequest;
use App\Models\Hrm\Payroll;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use App\Classes\CommonHrm;
use App\Models\Hrm\BasicSalary;
use App\Models\Hrm\PayrollComponent;
use App\Models\Hrm\PrePayment;

class PayrollController extends ApiBaseController
{
    protected $model = Payroll::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;
    protected $payrollGenerateRequest = PayrollGenerateRequest::class;
    protected $payrollRegenerateRequest = PayrollRegenerateRequest::class;
    protected $updateStatusRequest = UpdateStatusRequest::class;

    protected function modifyIndex($query)
    {
        $loggedUser = user();
        $request = request();

        //year Filters
        if ($request->has('year') && $request->year != "") {
            $payrollYear = $request->year;
            $query = $query->where('payrolls.year', $payrollYear);
        }

        //month Filters
        if ($request->has('month') && $request->month != "") {
            $payrollMonth = $request->month;
            $query = $query->where('payrolls.month', $payrollMonth);
        }

        if ($loggedUser->ability('admin', 'payrolls_view')) {
            if ($request->has('user_id')) {
                $userId = $this->getIdFromHash($request->user_id);
                $query = $query->where('payrolls.user_id', $userId);
            }
        } else {
            $query = $query->where('payrolls.user_id', $loggedUser->id);
        }

        return  $query;
    }

    public function storing($payroll)
    {
        $loggedUser = user();

        $payroll->created_by = $loggedUser->id;

        return $payroll;
    }

    public function payrollGenerate(PayrollGenerateRequest $payrollGenerateRequest)
    {
        if ($payrollGenerateRequest->has('year') && $payrollGenerateRequest->has('month') && $payrollGenerateRequest->month > 0) {
            $year = (int) $payrollGenerateRequest->year;
            $month = (int) $payrollGenerateRequest->month;

            $allUsers = BasicSalary::select('id', 'user_id', 'basic_salary');

            if ($payrollGenerateRequest->has('payrolls')) {
                $payrollsIds = $this->getIdArrayFromHash($payrollGenerateRequest->payrolls);

                $userIds = Payroll::whereIn('id', $payrollsIds)->pluck('user_id');

                $allUsers = $allUsers->whereIn('user_id', $userIds);
            }

            $allUsers = $allUsers->get();

            foreach ($allUsers as $allUser) {
                $payroll = Payroll::where('month', $month)
                    ->where('year', $year)
                    ->where('user_id', $allUser->user_id)
                    ->first();

                if (!$payroll) {
                    $payroll = new Payroll();
                    $payroll->created_by = user()->id;
                }

                $basicSalary = $allUser->basic_salary ? $allUser->basic_salary : 0;

                $resultData = CommonHrm::getMonthYearAttendanceDetails($allUser->user_id, $month, $year);
                $holidayCount = $resultData['holiday_count'];
                $paidLeaveCount = $resultData['total_paid_leaves'];
                $totalDaysInMonth = $resultData['total_days'];

                $totalPresentDays = $holidayCount + $paidLeaveCount;
                $netSalary = ($basicSalary / $totalDaysInMonth) * $totalPresentDays;

                $payroll->user_id = $allUser->user_id;
                $payroll->year = $year;
                $payroll->month = $month;
                $payroll->total_days = $totalDaysInMonth;
                $payroll->basic_salary = $basicSalary;
                $payroll->working_days = $resultData['working_days'];
                $payroll->present_days = $resultData['present_days'];
                $payroll->total_office_time = $resultData['total_office_time'];
                $payroll->total_worked_time = $resultData['clock_in_duration'];
                $payroll->total_worked_time = $resultData['clock_in_duration'];
                $payroll->half_days = $resultData['half_days'];
                $payroll->late_days = $resultData['total_late_days'];
                $payroll->paid_leaves = $resultData['paid_leaves'];
                $payroll->unpaid_leaves = $resultData['total_unpaid_leaves'];
                $payroll->holiday_count = $holidayCount;
                $payroll->salary_amount = $netSalary;
                $payroll->net_salary = $netSalary;
                $payroll->status = "generated";
                $payroll->updated_by = user()->id;
                $payroll->save();

                $totalPrePaymentAmount = 0;
                $totalExpenseAmount = 0;

                // PrePayment Payroll Component
                PayrollComponent::where('payroll_id', $payroll->id)
                    ->where('type', 'pre_payments')
                    ->delete();
                // Getting all Pre payments
                $allPrepayments = PrePayment::where('user_id', $allUser->user_id)
                    ->where('payroll_month', $month)
                    ->where('payroll_year', $year)
                    ->get();

                foreach ($allPrepayments as $allPrepayment) {
                    $newPrePaymentComponent = new PayrollComponent();
                    $newPrePaymentComponent->payroll_id = $payroll->id;
                    $newPrePaymentComponent->pre_payment_id = $allPrepayment->id;
                    $newPrePaymentComponent->user_id = $allUser->user_id;
                    $newPrePaymentComponent->name = "Pre Payments";
                    $newPrePaymentComponent->amount = $allPrepayment->amount;
                    $newPrePaymentComponent->is_earning = 0;
                    $newPrePaymentComponent->type = 'pre_payments';
                    $newPrePaymentComponent->save();

                    $totalPrePaymentAmount += $allPrepayment->amount;
                }

                // Expense Payroll Component
                PayrollComponent::where('payroll_id', $payroll->id)
                    ->where('type', 'expenses')
                    ->delete();
                // Getting all Pre payments
                $allExpenses = Expense::where('user_id', $allUser->user_id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->get();

                foreach ($allExpenses as $allExpense) {
                    $newExpenseComponent = new PayrollComponent();
                    $newExpenseComponent->payroll_id = $payroll->id;
                    $newExpenseComponent->expense_id = $allExpense->id;
                    $newExpenseComponent->user_id = $allUser->user_id;
                    $newExpenseComponent->name = "Expense Claim";
                    $newExpenseComponent->amount = $allExpense->amount;
                    $newExpenseComponent->is_earning = 1;
                    $newExpenseComponent->type = 'expenses';
                    $newExpenseComponent->save();

                    $totalExpenseAmount += $allExpense->amount;
                }

                $payroll->pre_payment_amount = $totalPrePaymentAmount;
                $payroll->expense_amount = $totalExpenseAmount;
                $payroll->net_salary = $netSalary - $totalPrePaymentAmount + $totalExpenseAmount;
                $payroll->save();
            }
        }


        return ApiResponse::make('Generate Successfully', []);
    }

    public function updateStatus(UpdateStatusRequest $updateStatusRequest)
    {
        $loggedUser = user();

        if (!$loggedUser->ability('admin', 'payrolls_edit')) {
            throw new ApiException("Not have valid permission");
        }

        if ($updateStatusRequest->has('payrolls')) {
            $paymentStatus = $updateStatusRequest->payroll_status;
            $payrollIds = $this->getIdArrayFromHash($updateStatusRequest->payrolls);

            foreach ($payrollIds as $payrollId) {
                $payroll = Payroll::find($payrollId);

                if ($payroll) {
                    $payroll->status = $paymentStatus;

                    if ($paymentStatus == 'paid') {
                        $payroll->payment_date = $updateStatusRequest->date;
                        $payroll->payment_mode_id = $this->getIdFromHash($updateStatusRequest->payment_mode_id);
                    } else {
                        $payroll->payment_date = null;
                        $payroll->payment_mode_id = null;
                    }

                    $payroll->save();
                }
            }
        }

        return ApiResponse::make('Status Updated Successfully', []);
    }
}
