<?php

namespace App\Classes;

use App\Models\StaffMember;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Examyou\RestAPI\Exceptions\ApiException;
use App\Models\Hrm\Holiday;
use App\Models\Hrm\LeaveType;
use App\Models\Hrm\Attendance;
use GuzzleHttp\Client;

class CommonHrm
{
    public static function getMinutesFromTimes($startTime, $endTime)
    {
        $timeArray = self::getDetailsArrayFromTimes($startTime, $endTime);
        $isNextDayForTime = self::isTimeForNextDate($startTime, $endTime);

        if ($isNextDayForTime) {
            $totalMinutes = ((24 - $timeArray['start_hours'] - 1) * 60) + (60 - $timeArray['start_minutes']) +  ($timeArray['end_hours'] * 60) +  $timeArray['end_minutes'];
        } else {
            $totalMinutes =  (($timeArray['end_hours'] - $timeArray['start_hours'] - 1) * 60) + (60 - $timeArray['start_minutes']) +  $timeArray['end_minutes'];
        }

        return $totalMinutes;
    }

    public static function getDetailsArrayFromTimes($startTime, $endTime)
    {
        $startTimeArray = explode(':', $startTime);
        $endTimeArray = explode(':', $endTime);

        $startTimeHour = $startTimeArray[0];
        $startTimeMinute = $startTimeArray[1];

        $endTimeHour = $endTimeArray[0];
        $endTimeMinute = $endTimeArray[1];

        return [
            'start_hours' => (int) $startTimeHour,
            'start_minutes' => (int) $startTimeMinute,
            'end_hours' => (int) $endTimeHour,
            'end_minutes' => (int) $endTimeMinute,
        ];
    }

    public static function isTimeForNextDate($startTime, $endTime)
    {
        $timeArray = self::getDetailsArrayFromTimes($startTime, $endTime);

        return $timeArray['start_hours'] > $timeArray['end_hours'] ? true : false;
    }

    public static function isLateClockedIn($officeStartTime, $clockInTime)
    {
        $isLate = false;
        $timeArray = self::getDetailsArrayFromTimes($officeStartTime, $clockInTime);

        if ($timeArray['end_hours'] > $timeArray['start_hours']) {
            $isLate = true;
        } else if ($timeArray['end_hours'] == $timeArray['start_hours']) {
            $isLate =  $timeArray['end_minutes'] <= $timeArray['start_minutes'] ? false : true;
        }

        return $isLate;
    }

    public static function getUserClockingTime($userId)
    {
        $company = company();

        $user = StaffMember::select('id', 'name', 'shift_id')->with(['shift'])
            ->where('company_id', $company->id)
            ->find($userId);

        $allIps = [];
        $allowedIpAddress = $user && $user->shift ? $user->shift->allowed_ip_address : $company->allowed_ip_address;
        if ($allowedIpAddress) {
            foreach ($allowedIpAddress as $allIpAddress) {
                $allIps[] = $allIpAddress['allowed_ip_address'];
            }
        }

        $clockInTime = $user && $user->shift ? $user->shift->clock_in_time : $company->clock_in_time;
        $clockOutTime = $user && $user->shift ? $user->shift->clock_out_time : $company->clock_out_time;
        if (!$clockInTime) {
            $clockInTime = "09:30:00";
        }
        if (!$clockOutTime) {
            $clockInTime = "18:00:00";
        }

        // If user have shift then shift time will be return
        // Else company time will be return
        return [
            'clock_in_time' => $clockInTime,
            'clock_out_time' => $clockInTime,
            'late_mark_after' => $user && $user->shift ? $user->shift->late_mark_after : $company->late_mark_after,
            'self_clocking' => $user && $user->shift ? $user->shift->self_clocking : $company->self_clocking,
            'allowed_ip_address' => $allIps,
        ];
    }

    public static function getShiftTimeFromDate($date, $userId)
    {
        $company = company();
        $clockTiming = self::getUserClockingTime($userId);

        $clockInDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $clockTiming['clock_in_time'], $company->timezone)
            ->setTimezone('UTC');
        $clockOutDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $clockTiming['clock_out_time'], $company->timezone)
            ->setTimezone('UTC');

        return [
            'clock_in_date_time' => $clockInDateTime,
            'clock_out_date_time' => $clockOutDateTime,
        ];
    }

    public static function getFincialYearStartEndDate($year)
    {
        $company = company();
        $startMonth = (int)'01';

        $startDate = Carbon::create($year, $startMonth, 1, 0, 0, 0, $company->timezone)->setTimezone('UTC')->startOfDay();
        $endDate = $startDate->copy()->addYear()->subDay();

        return [
            'startDate' => $startDate,
            'endDate'   => $endDate
        ];
    }

    public static function isPaidLeaveOrNot($date, $userId, $leaveTypeId)
    {
        // TODO - Check if attendance already exists or not

        $isPaid = true;
        $isHoliday = Holiday::whereDate('date', $date)->count();
        $leaveType = LeaveType::find($leaveTypeId);
        $maxLeavePerMonth = $leaveType->max_leaves_per_month;

        // Getting Fincial Year
        $fincialYear = self::getFincialYearFromDate($date);

        $dateDetails = self::getDateDetails($date);
        // Total Leave This Month
        $leaveDateMonth = $dateDetails['month'];
        $paidLeaveTakenThisMonth = self::totalPaidLeavesByYearMonth($leaveType->id, $userId, $fincialYear, $leaveDateMonth);
        $totalLeaveTakenThisYear = self::totalPaidLeavesByYear($leaveType->id, $userId, $fincialYear);

        if ($isHoliday == 0) {
            // Total leaves taken in this year (finical year)
            if ($totalLeaveTakenThisYear >= $leaveType->total_leaves || ($maxLeavePerMonth != null && $paidLeaveTakenThisMonth >= $maxLeavePerMonth)) {
                $isPaid = false;
            } else {
                $isPaid = true;
            }
        }

        // If leave is unpaid then directly set to unpaid
        // Otherwise according to above condition
        $isPaid = $leaveType->is_paid == 0 ? 0 : $isPaid;

        return [
            'isHoliday' => $isHoliday > 0 ? true : false,
            'isPaid'   => $isPaid,
            'paidLeaveTakenThisMonth' => $paidLeaveTakenThisMonth,
            'totalLeaveTakenThisYear' => $totalLeaveTakenThisYear,
            'totalLeaves' => $leaveType->total_leaves,
            'maxLeavePerMonth' => $maxLeavePerMonth,
        ];
    }

    public static function getDateDetails($date)
    {
        $dateArray = explode('-', $date);

        return [
            'year' => $dateArray[0],
            'month' => $dateArray[1],
            'day' => $dateArray[2],
        ];
    }

    // Get Fincial Year from a date
    public static function getFincialYearFromDate($date)
    {
        $dateDetails = self::getDateDetails($date);
        $company = company();
        $dateYear = $dateDetails['year'];
        $startMonth = (int)'01';

        // TODO - cross  check it is correct
        // Set Date as Date Object
        $dateObject = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' 00:00:00', $company->timezone);
        $startDate = Carbon::create($dateYear, $startMonth, 1)->setTimezone($company->timezone)->startOfDay();
        $endDate = $startDate->copy()->addYear()->subDay();

        // If current
        if (!$dateObject->between($startDate, $endDate)) {
            $dateYear -= 1;
        }

        return $dateYear;
    }

    public static function totalPaidLeavesByYearMonth($leaveTypeId, $userId, $year, $month)
    {
        $totalFullDayLeavesCount = Attendance::join('leaves', 'leaves.id', '=', 'attendances.leave_id')
            ->whereNotNull('attendances.leave_id')
            ->whereYear('attendances.date', $year)
            ->whereMonth('attendances.date', $month)
            ->where('leaves.leave_type_id', $leaveTypeId)
            // ->where('leaves.status', 'approved')
            ->where('attendances.user_id', $userId)
            ->where('attendances.is_half_day', 0)
            ->where('attendances.is_paid', 1)
            ->count();

        $totalHalfDayLeavesCount = Attendance::join('leaves', 'leaves.id', '=', 'attendances.leave_id')
            ->whereNotNull('attendances.leave_id')
            ->whereYear('attendances.date', $year)
            ->whereMonth('attendances.date', $month)
            ->where('leaves.leave_type_id', $leaveTypeId)
            // ->where('leaves.status', 'approved')
            ->where('attendances.user_id', $userId)
            ->where('attendances.is_half_day', 1)
            ->where('attendances.is_paid', 1)
            ->count();

        $totalLeaves = ($totalHalfDayLeavesCount / 2) + $totalFullDayLeavesCount;

        return $totalLeaves;
    }

    public static function totalPaidLeavesByYear($leaveTypeId, $userId, $year)
    {
        $fincialDates = self::getFincialYearStartEndDate($year);
        $startDate = $fincialDates['startDate'];
        $endDate = $fincialDates['endDate'];

        $totalFullDayLeavesCount = Attendance::join('leaves', 'leaves.id', '=', 'attendances.leave_id')
            ->whereNotNull('attendances.leave_id')
            ->whereBetween('attendances.date', [$startDate, $endDate])
            ->where('leaves.leave_type_id', $leaveTypeId)
            // ->where('leaves.status', 'approved')
            ->where('attendances.user_id', $userId)
            ->where('attendances.is_half_day', 0)
            ->where('attendances.is_paid', 1)
            ->count();

        $totalHalfDayLeavesCount = Attendance::join('leaves', 'leaves.id', '=', 'attendances.leave_id')
            ->whereNotNull('attendances.leave_id')
            ->whereBetween('attendances.date', [$startDate, $endDate])
            ->where('leaves.leave_type_id', $leaveTypeId)
            // ->where('leaves.status', 'approved')
            ->where('attendances.user_id', $userId)
            ->where('attendances.is_half_day', 1)
            ->where('attendances.is_paid', 1)
            ->count();

        $totalLeaves = ($totalHalfDayLeavesCount / 2) + $totalFullDayLeavesCount;

        return $totalLeaves;
    }

    public static function checkIfAttendanceAlreadyExists($userId, $startDate, $endDate = null)
    {
        if ($endDate != null) {
            $allDates = CarbonPeriod::create($startDate, $endDate);

            foreach ($allDates as $allDate) {
                $attendaceCount = Attendance::where('user_id', $userId)->whereDate('date', $allDate->format("Y-m-d"))->count();

                if ($attendaceCount > 0) {
                    throw new ApiException("Attendance already exists for date " . $allDate->format("Y-m-d"));
                }
            }
        } else {
            $attendaceCount = Attendance::where('user_id', $userId)->whereDate('date', $startDate)->count();

            if ($attendaceCount > 0) {
                throw new ApiException("Attendance already exists for date " . $startDate);
            }
        }
    }

    public static function getIpAddress()
    {
        $ipaddress = '';

        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;

        // $client = new Client();
        // $response = $client->get('https://api.ipify.org?format=json');
        // $responseData = json_decode($response->getBody(), true);
        // $ipAddress = $responseData['ip'];

        // return $ipAddress;
    }

    public static function getTodayAttendanceDetails()
    {
        $earlyClockInMinutes = 120;
        $clockOutAfterInMinutes = 120;

        $request = request();
        $company = company();
        $user = user();

        $shiftClockInTime = self::getUserClockingTime($user->id);

        $currentDateTimeObject = Carbon::now($company->timezone);
        $currentTime = $currentDateTimeObject->copy()->format('H:i:s');
        $currentDate = $currentDateTimeObject->copy()->format('Y-m-d');

        $showClockedInButton = false;
        $showClockedOutButton = false;
        $officeHoursExpired = false;

        // Early Office Start Time
        $earlyOfficeStartTime = Carbon::createFromFormat('Y-m-d H:i:s', $currentDate . ' ' . $shiftClockInTime['clock_in_time'], $company->timezone)
            ->subMinutes($earlyClockInMinutes);
        // Office hours passed
        $maxOfficeEndTime = Carbon::createFromFormat('Y-m-d H:i:s', $currentDate . ' ' . $shiftClockInTime['clock_out_time'], $company->timezone)
            ->addMinutes($clockOutAfterInMinutes);

        // If current time is greater than office early time
        // Then show clock in button
        if ($currentDateTimeObject->copy()->gte($earlyOfficeStartTime)) {
            $showClockedInButton = true;
        }

        // If current time is greate than max time of office
        // It mean office hours passed and cannot login and logout
        if ($currentDateTimeObject->copy()->lte($maxOfficeEndTime)) {
            $showClockedInButton = true;
            $showClockedOutButton = true;
        } else {
            $showClockedInButton = false;
            $showClockedOutButton = false;

            $officeHoursExpired = true;
        }

        $isUserAttendanceExists = Attendance::whereDate('attendances.date', $currentDate)
            ->where('attendances.user_id', $user->id)
            ->first();

        $isOnFullDayLeave = false;
        $isClockedIn = false;
        $isClockedOut = false;
        $clockInTime = null;
        $clockInDateTime = null;
        $clockOutTime = null;
        $clockOutDateTime = null;
        if ($isUserAttendanceExists) {

            if ($isUserAttendanceExists->is_leave && $isUserAttendanceExists->is_half_day == 0) {
                $showClockedInButton = false;
                $showClockedOutButton = false;

                $isOnFullDayLeave = true;
            } else {
                // If user clocked in then don't show clock in button
                if ($isUserAttendanceExists->clock_in_time) {
                    $isClockedIn = true;
                    $showClockedInButton = false;
                }

                if ($isUserAttendanceExists->clock_out_time) {
                    $isClockedOut = true;
                    $showClockedOutButton = false;
                }
            }

            $clockInTime = $isUserAttendanceExists->clock_in_time;
            $clockInDateTime = $isUserAttendanceExists->clock_in_date_time;
            $clockOutTime = $isUserAttendanceExists->clock_out_time;
            $clockOutDateTime = $isUserAttendanceExists->clock_out_date_time;
        }

        return [
            'date' => $currentDate,
            'time' => $currentTime,
            'is_on_full_day_leave' => $isOnFullDayLeave,
            'is_clocked_in' => $isClockedIn,
            'is_clocked_out' => $isClockedOut,
            'office_hours_expired' => $officeHoursExpired,
            'clock_in_time' => $clockInTime,
            'clock_in_date_time' => $clockInDateTime,
            'clock_out_time' => $clockOutTime,
            'clock_out_date_time' => $clockOutDateTime,
            'show_clock_in_button' => $showClockedInButton,
            'show_clock_out_button' => $showClockedOutButton,
            'office_clock_in_time' => $shiftClockInTime['clock_in_time'],
            'office_clock_out_time' => $shiftClockInTime['clock_out_time'],
            'self_clocking' => $shiftClockInTime['self_clocking'],
        ];
    }

    public static function getMonthYearAttendanceDetails($userId, $month, $year)
    {
        $company = company();
        $user = StaffMember::select('id', 'name', 'shift_id')->with(['shift'])->find($userId);

        $currentDateTime = Carbon::now($company->timezone);
        $today = Carbon::now($company->timezone)->startOfDay();
        $startDate = Carbon::createFromDate($year, $month, 1, $company->timezone)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->startOfDay();

        $attendanceData = [];
        $lateTime = 0;
        $clockedInDuration = 0;
        $totalLateDays = 0;
        $totalHalfDays = 0;
        $totalPaidLeave = 0;
        $paidLeaveCount = 0;
        $totalUnPaidLeave = 0;
        $totalHolidayCount = 0;
        $totalDays = 0;

        $shiftDetails = self::getUserClockingTime($userId);

        $shiftClockInTime = $shiftDetails['clock_in_time'];
        $shiftClockOutTime = $shiftDetails['clock_out_time'];
        $officeHoursInMinutes = self::getMinutesFromTimes($shiftClockInTime, $shiftClockOutTime);

        $allAttendances = Attendance::with(['leave:id,leave_type_id,reason', 'leave.leaveType:id,name', 'holiday'])
            ->whereBetween('attendances.date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('attendances.user_id', $userId)
            ->get();
        $allHolidays = Holiday::whereBetween('holidays.date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        $totalWorkingDays = 0;
        $totalPresentDays = 0;

        while ($endDate->gte($startDate)) {
            $currentDate = $endDate->copy();

            $isAttendanceDataFound = $allAttendances->filter(function ($allAttendance) use ($currentDate) {
                return $allAttendance->date->format('Y-m-d') == $currentDate->format('Y-m-d');
            })->first();

            $isHolidayDataFound = $allHolidays->filter(function ($allHoliday) use ($currentDate) {
                return $allHoliday->date->format('Y-m-d') == $currentDate->format('Y-m-d');
            })->first();

            $totalWorkDurationInMinutes = 0;
            $isHoliday = false;
            $isLeave = false;
            $holidayName = '';
            $leaveName = '';
            $leaveReason = '';

            if ($currentDate->gt($today)) {
                $status = 'upcoming';
            } else if ($isHolidayDataFound) {
                $status = 'holiday';
                $isHoliday = true;
                $holidayName = $isHolidayDataFound->name;

                $totalHolidayCount += 1;
            } else if ($isAttendanceDataFound) {
                if ($isAttendanceDataFound->leave_type_id && $isAttendanceDataFound->is_half_day) {
                    $status = 'half_day';
                    $isLeave = true;
                    $leaveName = $isAttendanceDataFound->leave && $isAttendanceDataFound->leave->leaveType ? $isAttendanceDataFound->leave->leaveType->name : '';
                    $leaveReason = $isAttendanceDataFound->leave ? $isAttendanceDataFound->leave->reason : '';

                    $totalPresentDays += 0.5;
                    $totalHalfDays += 1;
                } else if ($isAttendanceDataFound->leave_type_id) {
                    $status = 'absent';
                    $isLeave = true;
                    $leaveName = $isAttendanceDataFound->leave && $isAttendanceDataFound->leave->leaveType ? $isAttendanceDataFound->leave->leaveType->name : '';
                    $leaveReason = $isAttendanceDataFound->leave ? $isAttendanceDataFound->leave->reason : '';
                } else {
                    $status = 'present';

                    $totalPresentDays += 1;
                }

                if ($status == 'half_day' || $status == 'present') {

                    // If attendance date is same as today date
                    // And user not clocked out
                    // Then we will calcualte the difference
                    if ($isAttendanceDataFound->clock_in_date_time && $isAttendanceDataFound->clock_in_date_time->format('Y-m-d') == $today->format('Y-m-d') && $isAttendanceDataFound->clock_out_date_time == null) {
                        $totalWorkDurationInMinutes = $currentDateTime->diffInMinutes($isAttendanceDataFound->clock_in_date_time);
                    } else if ($isAttendanceDataFound->clock_in_date_time != null && $isAttendanceDataFound->clock_out_date_time != null) {
                        // User have clock in and clock out time
                        $totalWorkDurationInMinutes = $isAttendanceDataFound->clock_out_date_time->diffInMinutes($isAttendanceDataFound->clock_in_date_time);
                    } else if ($user && $user->shift) {
                        $clockOutTimeDateFormatForAttendance = Carbon::createFromFormat('Y-m-d H:i:s', $isAttendanceDataFound->clock_in_date_time->format('Y-m-d') . '' . $user->shift->clock_out_time);

                        // If user assigned a shift
                        $totalWorkDurationInMinutes = $clockOutTimeDateFormatForAttendance->diffInMinutes($isAttendanceDataFound->clock_in_date_time);
                    } else {
                        // If shift not defined then take setting from company
                    }

                    if ($isAttendanceDataFound->is_late) {
                        $isLateClockedIn = CommonHrm::isLateClockedIn($isAttendanceDataFound->office_clock_in_time, $isAttendanceDataFound->clock_in_time);

                        if ($isLateClockedIn) {
                            $lateTime =  CommonHrm::getMinutesFromTimes($isAttendanceDataFound->office_clock_in_time, $isAttendanceDataFound->clock_in_time);
                        } else {
                            $lateTime = 0;
                        }
                    } else {
                        $lateTime = 0;
                    }
                }


                if ($isAttendanceDataFound->is_paid) {
                    $totalPaidLeave += $isAttendanceDataFound->is_half_day ? 0.5 : 1;

                    if ($isAttendanceDataFound->leave_type_id) {
                        $paidLeaveCount += $isAttendanceDataFound->is_half_day ? 0.5 : 1;
                    }
                } else {
                    $totalUnPaidLeave += $isAttendanceDataFound->is_half_day ? 0.5 : 1;
                }

                $totalWorkingDays += 1;
            } else {
                $status = 'not_marked';
                $totalWorkingDays += 1;
            }

            if ($status != 'upcoming') {
                $isUserLate = $isAttendanceDataFound && $isAttendanceDataFound->is_late ? $isAttendanceDataFound->is_late : 0;

                $attendanceData[] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'status' => $status,
                    'is_holiday' => $isHoliday,
                    'holiday_name' => $holidayName,
                    'is_leave' => $isLeave,
                    'leave_name' => $leaveName,
                    'is_late'   => $isUserLate,
                    'late_time' => $lateTime,
                    'clock_in'  => $isAttendanceDataFound ? $isAttendanceDataFound->clock_in_date_time : '',
                    'clock_out'  => $isAttendanceDataFound ? $isAttendanceDataFound->clock_out_date_time : '',
                    'clock_in_ip'  => $isAttendanceDataFound ? $isAttendanceDataFound->clock_in_ip_address : '',
                    'clock_out_ip'  => $isAttendanceDataFound ? $isAttendanceDataFound->clock_out_ip_address : '',
                    'leave_reason'  => $leaveReason,
                    'worked_time'  => $totalWorkDurationInMinutes
                ];

                $totalLateDays += $isUserLate;
                $clockedInDuration += $totalWorkDurationInMinutes;
            }

            $totalDays += 1;

            $endDate->subDay();
        }

        return [
            'data'  => $attendanceData,
            'total_days'  => $totalDays,
            'working_days'  => $totalWorkingDays,
            'present_days'  => $totalPresentDays,
            'paid_leaves'  => $paidLeaveCount,
            'half_days'  => $totalHalfDays,
            'office_time'  => $officeHoursInMinutes,
            'total_office_time'  => $totalWorkingDays * $officeHoursInMinutes, // TODO - Do it from attendance table
            'clock_in_duration'  => $clockedInDuration,
            'total_late_days'  => $totalLateDays,
            'total_paid_leaves' => $totalPaidLeave,
            'total_unpaid_leaves' => $totalUnPaidLeave,
            'holiday_count' => $totalHolidayCount,
        ];
    }
}
