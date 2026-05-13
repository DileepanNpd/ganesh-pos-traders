<?php

namespace App\Http\Controllers\Api\Hrm;

use App\Classes\Common;
use App\Http\Controllers\ApiBaseController;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Company;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use App\Classes\CommonHrm;
use App\Http\Requests\Api\Hrm\Leave\IndexRequest;
use App\Http\Requests\Api\Hrm\Leave\StoreRequest;
use App\Http\Requests\Api\Hrm\Leave\UpdateRequest;
use App\Http\Requests\Api\Hrm\Leave\DeleteRequest;
use App\Http\Requests\Api\Hrm\Leave\UpdateStatusRequest;
use App\Http\Requests\Api\Hrm\Leave\UserAttendaceRequest;
use App\Models\Hrm\Leave;
use App\Models\Hrm\LeaveType;
use App\Models\Hrm\Attendance;

class LeaveController extends ApiBaseController
{
    protected $model = Leave::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {
        $loggedUser = user();
        $request = request();

        // status Filters
        if ($request->has('status') && $request->status != "all") {
            $leaveStatus = $request->status;
            $query = $query->where('leaves.status', $leaveStatus);
        }

        if ($loggedUser->ability('admin', 'leaves_assign_to_all')) {
            if ($request->has('user_id')) {
                $query->user_id = $query->where('leaves.user_id', $this->getIdFromHash($request->user_id));
            }
        } else {
            $query->user_id = $query->where('leaves.user_id', $loggedUser->id);
        }

        // leaveType Filters
        if ($request->has('leave_type_id') && $request->leave_type_id != "") {
            $leaveTypeId = $this->getIdFromHash($request->leave_type_id);
            $query = $query->where('leaves.leave_type_id', $leaveTypeId);
        }

        return  $query;
    }

    public function storing(Leave $leave)
    {
        $loggedUser = user();
        $request = request();

        if ($request->has('status') && $loggedUser->ability('admin', 'leaves_approve_reject')) {
            $leave->status = $request->status;
        }

        if ($request->has('user_id') && $loggedUser->ability('admin', 'leaves_assign_to_all')) {
            $leave->user_id = $this->getIdFromHash($request->user_id);
        } else {
            $leave->user_id = $loggedUser->id;
        }

        // Throw exception if attendance already exists
        CommonHrm::checkIfAttendanceAlreadyExists($leave->user_id, $leave->start_date, $leave->end_date);

        return $leave;
    }

    public function stored(Leave $leave)
    {
        $loggedUser = user();

        if ($leave->status == 'approved' && $loggedUser->ability('admin', 'leaves_approve_reject')) {
            $this->updateLeaveStatus($leave->id);
        }

        return $leave;
    }

    public function updating(Leave $leave)
    {
        $loggedUser = user();
        $request = request();

        // Can not update the leave if it is already approved or rejcted
        if ($leave->getOriginal('status') == 'approved' || $leave->getOriginal('status') == 'rejected') {
            throw new ApiException("Leave already apporved or rejected");
        }

        // If not admin or not having persmission of leaves_approve_reject
        // Then cannot edit other user
        if (!$loggedUser->ability('admin', 'leaves_edit_all') && $leave->user_id != $loggedUser->id) {
            throw new ApiException("Not have valid permission");
        }

        if (($request->status == 'approved' || $request->status == 'rejected')  && $loggedUser->ability('admin', 'leaves_approve_reject')) {
            if ($request->status == 'approved') {
                $this->updateLeaveStatus($leave->id);
            }

            $leave->status = $request->status;
        }

        return $leave;
    }

    public function destroying(Leave $leave)
    {
        $loggedUser = user();

        // If user don't have permssion to leaves_delete_all permission
        if (!$loggedUser->ability('admin', 'leaves_delete_all') && $leave->user_id == $loggedUser->id && ($leave->status == 'approved' || $leave->status == 'rejected')) {
            throw new ApiException("Not have valid permission");
        }

        // Cannot delete other user leave if not have permission
        if (!$loggedUser->ability('admin', 'leaves_delete_all') && $leave->user_id != $loggedUser->id && $leave->status == 'pending') {
            throw new ApiException("Not have valid permission");
        }

        return $leave;
    }

    // TODO - on creating leave save number of days
    // TODO - also calculate using half day

    public function statusUpdate(UpdateStatusRequest $request, $id)
    {
        $loggedUser = user();

        $id = $this->getIdFromHash($id);
        $leave = Leave::find($id);

        if ($leave->status == 'approved' || $leave->status == 'rejected') {
            throw new ApiException("Leave already apporved or rejected");
        }

        if (!$loggedUser->ability('admin', 'leaves_approve_reject')) {
            throw new ApiException("Not have valid permission");
        }

        if ($request->status == 'approved') {
            $data = $this->updateLeaveStatus($id);
        }

        // Updating Status
        $leave->status = $request->status;
        $leave->save();

        return ApiResponse::make('Success', $data);
    }

    public function updateLeaveStatus($id)
    {
        $leave = Leave::find($id);

        $leaveDates = CarbonPeriod::create($leave->start_date, $leave->end_date);
        $data = [];

        foreach ($leaveDates as $leaveDate) {
            $isPaidLeaveOrNot = CommonHrm::isPaidLeaveOrNot($leaveDate->format('Y-m-d'), $leave->user_id, $leave->leave_type_id);

            if ($isPaidLeaveOrNot['isHoliday'] == false) {
                $isPaid = $isPaidLeaveOrNot['isPaid'];

                $data[] = [
                    'date' => $leaveDate,
                    'totalLeaveTakenThisYear' => $isPaidLeaveOrNot['totalLeaveTakenThisYear'],
                    'paidLeaveTakenThisMonth' => $isPaidLeaveOrNot['paidLeaveTakenThisMonth'],
                    'total_leaves' => $isPaidLeaveOrNot['totalLeaves'],
                    'maxLeavePerMonth' => $isPaidLeaveOrNot['maxLeavePerMonth'],
                    'isPaid' => $isPaid,
                ];

                $attendance = new Attendance();
                $attendance->is_leave = 1;
                $attendance->date = $leaveDate->format('Y-m-d');
                $attendance->user_id = $leave->user_id;
                $attendance->leave_id = $leave->id;
                $attendance->leave_type_id = $leave->leave_type_id;
                $attendance->is_half_day = $leave->is_half_day;
                $attendance->is_paid = $isPaid;
                $attendance->status = $leave->is_half_day ? 'half_day' : 'on_leave';
                $attendance->reason = $leave->reason;
                $attendance->save();
            }
        }

        return $data;
    }

    public function leaveStartMonthUpdate(UpdateLeaveSettingsRequest $request)
    {

        $user = user();
        $company = Company::find($user->company_id);
        $company->leave_start_month = $request->leave_start_month;
        $company->save();

        return ApiResponse::make('Success', []);
    }


    public function remainingLeaves()
    {
        $request = request();

        // Check if user have permssion to view all leaves
        $loggedUser = user();
        $userId = $loggedUser->ability('admin', 'leaves_assign_to_all') && $request->has('user_id') ? $this->getIdFromHash($request->user_id) : $loggedUser->id;

        $allLeaveTypes = LeaveType::select('id', 'name', 'total_leaves', 'is_paid')->get();
        $year = $request->year;

        // If request year is same as current year
        $fincialDates = CommonHrm::getFincialYearStartEndDate($year);
        $startDate = $fincialDates['startDate'];
        $endDate = $fincialDates['endDate'];

        foreach ($allLeaveTypes as $allLeaveType) {
            $totalFullDayLeavesCount = Attendance::where('attendances.is_leave', 1)
                ->where('is_holiday', 0)
                ->whereBetween('attendances.date', [$startDate, $endDate])
                ->where('attendances.leave_type_id', $allLeaveType->id)
                ->where('attendances.user_id', $userId)
                ->where('attendances.is_half_day', 0);

            if ($allLeaveType->is_paid == 1) {
                $totalFullDayLeavesCount = $totalFullDayLeavesCount->where('attendances.is_paid', 1);
            }
            $totalFullDayLeavesCount = $totalFullDayLeavesCount->count();

            $totalHalfDayLeavesCount = Attendance::where('attendances.is_leave', 1)
                ->where('is_holiday', 0)
                ->whereBetween('attendances.date', [$startDate, $endDate])
                ->where('attendances.leave_type_id', $allLeaveType->id)
                ->where('attendances.user_id', $userId)
                ->where('attendances.is_half_day', 1);

            if ($allLeaveType->is_paid == 1) {
                $totalHalfDayLeavesCount = $totalHalfDayLeavesCount->where('attendances.is_paid', 1);
            }
            $totalHalfDayLeavesCount = $totalHalfDayLeavesCount->count();

            $totalLeaves = ($totalHalfDayLeavesCount / 2) + $totalFullDayLeavesCount;
            $allLeaveType->remaining_leaves = $allLeaveType->total_leaves - $totalLeaves;
        }

        return ApiResponse::make('Data fetched', [
            'data' => $allLeaveTypes
        ]);
    }

    public function unpaidLeaves()
    {
        $startMonth = (int)'01';
        $request = request();

        // Check if user have permssion to view all leaves
        $loggedUser = user();
        $userId = $loggedUser->ability('admin', 'leaves_assign_to_all') && $request->has('user_id') ? $this->getIdFromHash($request->user_id) : $loggedUser->id;
        $year = (int)$request->year;

        $yearMonths = [];

        $monthCounter = 0;
        for ($i = $startMonth; $i <= 12; $i++) {
            $yearMonths[] = [
                'month' => $i,
                'year'  => $year
            ];

            $monthCounter += 1;
        }

        for ($i = 1; $i <= 12 - $monthCounter; $i++) {
            $yearMonths[] = [
                'month' => $i,
                'year'  => $year + 1
            ];
        }

        $unpaidLeaveData = [];
        foreach ($yearMonths as $yearMonth) {
            $totalFullDayLeavesCount = Attendance::where('attendances.is_leave', 1)
                ->whereNotNull('attendances.leave_type_id')
                ->whereMonth('attendances.date', $yearMonth['month'])
                ->whereYear('attendances.date', $yearMonth['year'])
                ->where('attendances.user_id', $userId)
                ->where('attendances.is_half_day', 0)
                ->where('attendances.is_paid', 0)
                ->count();

            $totalHalfDayLeavesCount = Attendance::where('attendances.is_leave', 1)
                ->whereNotNull('attendances.leave_type_id')
                ->whereMonth('attendances.date', $yearMonth['month'])
                ->whereYear('attendances.date', $yearMonth['year'])
                ->where('attendances.user_id', $userId)
                ->where('attendances.is_half_day', 1)
                ->where('attendances.is_paid', 0)
                ->count();

            $totalLeaves = ($totalHalfDayLeavesCount / 2) + $totalFullDayLeavesCount;

            $unpaidLeaveData[] = [
                'month' => $yearMonth['month'] < 10 ? '0' . $yearMonth['month'] : '' . $yearMonth['month'],
                'year'  => $yearMonth['year'],
                'unpaid_leaves' => $totalLeaves
            ];
        }

        return ApiResponse::make('Data fetched', [
            'data' => $unpaidLeaveData
        ]);
    }

    public function addUserAttendance(UserAttendaceRequest $request)
    {
        // $request = request();
        // dd($request);
        $user = isset($request->employee_id);
        $userId = $this->getIdFromHash($request->employee_id);
        if ($user == true) {
            $dates = [];
            $startDate = 0;
            $endDate = 0;
            if ($request->mark_attendance == 'month') {
                $month = substr($request->attendance_month, 5, 6);
                $year = substr($request->attendance_month, 0, 4);
                $month = (int) $month;
                $year = (int) $year;
                // $user = user();

                $startDate = Carbon::createFromDate($year, $month, 1);
                $endDate = $startDate->copy()->endOfMonth();

                $dateRanges = [];
                $columns = [];

                while ($startDate->lte($endDate)) {
                    $dateRanges[] = $startDate->copy();
                    $columns[] = $startDate->format('d');

                    $startDate->addDay();
                }

                foreach ($dateRanges as $dateRange) {
                    $dates[] = $dateRange->format('Y-m-d');
                }
            }
            if ($request->mark_attendance == 'date') {
                $count = 1;
                foreach ($request->attendance_date as $date) {
                    if ($count == 1) {
                        $startDate = Carbon::createFromFormat('Y-m-d', substr($date, 0, 10));
                    } else {
                        $endDate = Carbon::createFromFormat('Y-m-d', substr($date, 0, 10));
                    }
                    $count += 1;
                };
                while ($startDate->lte($endDate)) {

                    $dates[] = $startDate->copy()->format('Y-m-d');

                    $startDate->addDay();
                }
            }


            if ($request->half_day == 1) {
                $leaveId = $this->getIdFromHash($request->leave_type);
            } else {
                $leaveId = 0;
            }
            foreach ($dates as $date) {


                $attendance = new Attendance();
                $attendance->is_leave = $request->half_day;
                $attendance->leave_type_id = $leaveId;
                $attendance->date = $date;
                $attendance->user_id = $userId;
                $attendance->clock_in_date_time = $request->clock_in;
                $attendance->clock_out_date_time = $request->clock_out;
                $attendance->is_half_day = $request->half_day;
                $attendance->is_late = $request->late;
                $attendance->save();
            }
        }
    }
}
