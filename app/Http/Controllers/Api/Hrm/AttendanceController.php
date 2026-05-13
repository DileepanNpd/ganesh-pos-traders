<?php

namespace App\Http\Controllers\Api\Hrm;

use App\Http\Controllers\ApiBaseController;
use App\Classes\Common;
use App\Models\StaffMember;
use App\Models\User;
use Carbon\Carbon;
use Examyou\RestAPI\ApiResponse;
use App\Models\Hrm\Holiday;
use App\Http\Requests\Api\Hrm\Attendance\IndexRequest;
use App\Http\Requests\Api\Hrm\Attendance\StoreRequest;
use App\Http\Requests\Api\Hrm\Attendance\UpdateRequest;
use App\Http\Requests\Api\Hrm\Attendance\DeleteRequest;
use App\Models\Hrm\Attendance;
use App\Classes\CommonHrm;

class AttendanceController extends ApiBaseController
{
    protected $model = Attendance::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;


    protected function modifyIndex($query)
    {
        $loggedUser = user();
        $request = request();

        if ($request->has('date') && $request->date != '') {
            $date = explode(',', $request->date);
            $startDate = $date[0];
            $endDate = $date[1];

            $query = $query->whereRaw('attendances.date >= ?', [$startDate])
                ->whereRaw('attendances.date <= ?', [$endDate]);
        }

        if ($loggedUser->ability('admin', 'attendances_view')) {
            if ($request->has('user_id')) {
                $userId = $this->getIdFromHash($request->user_id);
                $query = $query->where('attendances.user_id', $userId);
            }
        } else {
            $query = $query->where('attendances.user_id', $loggedUser->id);
        }

        if ($request->has('status') && $request->status != "all") {
            $attendance = $request->status;
            $query = $query->where('attendances.status', $attendance);
        };

        return  $query;
    }

    public function storing($attendance)
    {
        return $this->updateAddEditing($attendance);
    }

    public function updating($attendance)
    {
        return $this->updateAddEditing($attendance, 'edit');
    }

    public function updateAddEditing($attendance, $addEditType = 'add')
    {
        $company = company();
        $request = request();

        $userId = $this->getIdFromHash($request->user_id);
        $date = $request->date;

        // Throw exception if attendance already exists
        if ($addEditType == 'add' || ($attendance->isDirty('date') && $addEditType == 'edit')) {
            CommonHrm::checkIfAttendanceAlreadyExists($userId, $date);
        }

        $clockIn = $request->clock_in_time;
        $clockOut = $request->clock_out_time;

        $officeShiftTiming = CommonHrm::getUserClockingTime($userId);
        $attendance->office_clock_in_time = $officeShiftTiming['clock_in_time'];
        $attendance->office_clock_out_time = $officeShiftTiming['clock_out_time'];


        // For inserting the clocking date time
        $clockInDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $clockIn, $company->timezone)
            ->setTimezone('UTC');

        $totalMinutes = CommonHrm::getMinutesFromTimes($clockIn, $clockOut);
        $clockoutDateTime = $clockInDateTime->copy()->addMinutes($totalMinutes);
        $attendance->clock_in_date_time = $clockInDateTime->copy()->format('Y-m-d H:i:s');
        $attendance->clock_out_date_time = $clockoutDateTime->format('Y-m-d H:i:s');
        $attendance->total_duration = $totalMinutes;

        // No need of timezone because we need the date object

        if ($request->has('is_half_day') && $request->is_half_day == 1 && $request->has('leave_type_id') && $request->leave_type_id != '') {
            $leaveTypeId = $this->getIdFromHash($request->leave_type_id);
            $attendance->is_leave = 1;
            $attendance->is_half_day = 1;
            $attendance->leave_type_id = $leaveTypeId;

            // Check and update leave type is paid or unpaid
            $isPaidOrNot = CommonHrm::isPaidLeaveOrNot($date, $userId, $leaveTypeId);
            $attendance->is_paid = $isPaidOrNot['isPaid'];
        } else {

            $attendance->is_leave = 0;
            $attendance->is_half_day = 0;
            $attendance->leave_type_id = null;
            $attendance->is_paid = 1;
        }

        // For changing the status
        if ($attendance->is_half_day) {
            $attendance->status = "half_day";
        } else if ($attendance->leave_type_id) {
            $attendance->status = "on_leave";
        } else {
            $attendance->status = "present";
        }

        return $attendance;
    }

    public function attendaceSummary()
    {
        $request = request();
        $company = company();

        $month = (int) $request->month;
        $year = (int) $request->year;
        $loggedUser = user();

        $today = Carbon::now($company->timezone);
        $startDate = Carbon::createFromDate($year, $month, 1, $company->timezone)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth();

        $dateRanges = [];
        $columns = [];
        $attendanceData = [];

        $offset = $request->offset;
        $limit = $request->limit;
        $totalRecords = 1;

        $allCompanyUsers = StaffMember::select('id', 'name');

        $userId = null;
        if ($loggedUser->ability('admin', 'attendances_view')) {
            if ($request->has('user_id')) {
                $userId = $this->getIdFromHash($request->user_id);
                $allCompanyUser = $allCompanyUsers->where('id', $userId);

                $totalRecords = StaffMember::count();
            } else {
                $totalRecords = 1;
            }
        } else {
            $userId = $loggedUser->id;
            $allCompanyUser = $allCompanyUsers->where('id', $userId);

            $totalRecords = 1;
        }

        $allCompanyUsers = $allCompanyUsers->skip($offset)->take($limit)->get();

        $allAttendances = Attendance::with(['leave:id,leave_type_id,reason', 'leave.leaveType:id,name', 'holiday']);

        if ($userId != null) {
            $allAttendances = $allAttendances->where('attendances.user_id', $userId);
        }
        $allAttendances = $allAttendances->whereBetween('attendances.date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();
        $allHolidays = Holiday::whereBetween('holidays.date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();


        while ($startDate->lte($endDate)) {
            $dateRanges[] = $startDate->copy();
            $columns[] = $startDate->format('d');

            $startDate->addDay();
        }

        foreach ($allCompanyUsers as $allCompanyUser) {
            $userAttendanceData = [
                'name' => $allCompanyUser->name
            ];

            $totalWorkingDays = 0;
            $totalPresentDays = 0;
            foreach ($dateRanges as $dateRange) {
                $isAttendanceDataFound = $allAttendances->filter(function ($allAttendance) use ($dateRange, $allCompanyUser) {
                    return $allAttendance->date->format('Y-m-d') == $dateRange->format('Y-m-d') && $allCompanyUser->id == $allAttendance->user_id;
                })->first();
                $isHolidayDataFound = $allHolidays->filter(function ($allHoliday) use ($dateRange) {
                    return $allHoliday->date->format('Y-m-d') == $dateRange->format('Y-m-d');
                })->first();

                $isHoliday = false;
                $isLeave = false;
                $holidayName = '';
                $leaveName = '';
                $leaveReason = '';
                if ($dateRange->gt($today)) {
                    $status = 'upcoming';
                } else if ($isHolidayDataFound) {
                    $status = 'holiday';
                    $isHoliday = true;
                    $holidayName = $isHolidayDataFound->name;
                } else if ($isAttendanceDataFound) {

                    if ($isAttendanceDataFound->is_leave && $isAttendanceDataFound->is_half_day) {
                        $status = 'half_day';
                        $isLeave = true;
                        $leaveName = $isAttendanceDataFound->leave && $isAttendanceDataFound->leave->leaveType ? $isAttendanceDataFound->leave->leaveType->name : '';
                        $leaveReason = $isAttendanceDataFound->leave ? $isAttendanceDataFound->leave->reason : '';

                        $totalPresentDays += 0.5;
                    } else if ($isAttendanceDataFound->is_leave) {
                        $status = 'absent';
                        $isLeave = true;
                        $leaveName = $isAttendanceDataFound->leave && $isAttendanceDataFound->leave->leaveType ? $isAttendanceDataFound->leave->leaveType->name : '';
                        $leaveReason = $isAttendanceDataFound->leave ? $isAttendanceDataFound->leave->reason : '';
                    } else {
                        $status = 'present';

                        $totalPresentDays += 1;
                    }

                    $totalWorkingDays += 1;
                } else {
                    $status = 'absent';
                    $totalWorkingDays += 1;
                }
                $userAttendanceData[$dateRange->format('j')] = [
                    'status' => $status,
                    'is_holiday' => $isHoliday,
                    'holiday_name' => $holidayName,
                    'is_leave' => $isLeave,
                    'leave_name' => $leaveName,
                    'clock_in'  => $isAttendanceDataFound ? $isAttendanceDataFound->clock_in_date_time : '',
                    'clock_out'  => $isAttendanceDataFound ? $isAttendanceDataFound->clock_out_date_time : '',
                    'leave_reason'  => $leaveReason,
                ];
            }

            $userAttendanceData['working_days'] = $totalWorkingDays;
            $userAttendanceData['present_days'] = $totalPresentDays;
            $attendanceData[] = $userAttendanceData;
        }

        return ApiResponse::make('Data fetched', [
            'columns' => $columns,
            'dateRange' => $dateRange,
            'data' => $attendanceData,
            'meta'  => [
                'paging' => [
                    'total' => $totalRecords
                ]
            ]
        ]);
    }

    public function attendaceSummaryByMonth()
    {
        $request = request();

        $month = (int) $request->month;
        $year = (int) $request->year;
        $loggedUser = user();
        $company = company();

        if ($request->has('user_id') && $loggedUser->ability('admin', 'attendances_view')) {
            $userId = $this->getIdFromHash($request->user_id);
        } else {
            $userId = $loggedUser->id;
            // Todo check if have mark attendance permission
        }

        $resultData = CommonHrm::getMonthYearAttendanceDetails($userId, $month, $year);

        return ApiResponse::make('Data fetched', $resultData);
    }
}
