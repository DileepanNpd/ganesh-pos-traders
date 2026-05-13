<?php

namespace App\Http\Controllers\Api\Hrm;

use App\Http\Controllers\ApiBaseController;
use App\Models\Company;
use App\Models\StaffMember;
use App\Models\User;
use Carbon\Carbon;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use App\Http\Requests\Api\Hrm\Appreciation\IndexRequest;
use App\Http\Requests\Api\Hrm\Appreciation\StoreRequest;
use App\Http\Requests\Api\Hrm\Appreciation\UpdateRequest;
use App\Http\Requests\Api\Hrm\Appreciation\DeleteRequest;
use App\Models\Hrm\Appreciation;
use GuzzleHttp\Client;
use App\Classes\CommonHrm;
use App\Models\Hrm\Holiday;
use App\Models\Hrm\Leave;
use App\Models\Hrm\Attendance;
use App\Http\Requests\Api\Hrm\HrmSettings\UpdateHrmSettingsRequest;

class HrmDashboardController extends ApiBaseController
{

    public function getTodayAttendanceDetails()
    {
        $ipAddress = CommonHrm::getIpAddress();
        $details = CommonHrm::getTodayAttendanceDetails();
        $details['ip_address'] = $ipAddress;

        return ApiResponse::make('Details Fetched Successfully', $details);
    }

    public function markTodayAttendance()
    {
        $request = request();
        $ipAddress = CommonHrm::getIpAddress();
        $details = CommonHrm::getTodayAttendanceDetails();
        $attendanceType = $request->type;
        $user = user();
        $company = company();

        $shiftClockInTime = CommonHrm::getUserClockingTime($user->id);

        if ($shiftClockInTime['allowed_ip_address'] && count($shiftClockInTime['allowed_ip_address']) > 0) {
            if (in_array($ipAddress, $shiftClockInTime['allowed_ip_address']) == false) {
                throw new ApiException("You can not mark attendance from this IP address");
            }
        }

        if ($attendanceType == 'clock-in') {
            if ($details['is_on_full_day_leave']) {
                throw new ApiException("You are on leave.");
            } else if ($details['is_clocked_in']) {
                throw new ApiException("Already Clocked In");
            } else if ($details['office_hours_expired']) {
                throw new ApiException("Office hours passed");
            } else {

                $isLate = 0;
                $shiftClockInTime = CommonHrm::getUserClockingTime($user->id);
                $currentDateTimeObject = Carbon::now($company->timezone);

                // Check if is late or not
                if ($shiftClockInTime && $shiftClockInTime['late_mark_after']) {
                    $clockInDateTimeFormat = Carbon::createFromFormat('Y-m-d H:i:s', $currentDateTimeObject->copy()->format('Y-m-d') . ' ' . $shiftClockInTime['clock_in_time'], $company->timezone)
                        ->addMinutes($shiftClockInTime['late_mark_after']);

                    $isLate = CommonHrm::isLateClockedIn($clockInDateTimeFormat->format('H:i:s'), $currentDateTimeObject->copy()->format('H:i:s'));
                }

                // Mark the clockIn
                $attendance = new Attendance();
                $attendance->date = $details['date'];
                $attendance->user_id = $user->id;
                $attendance->clock_in_date_time = $currentDateTimeObject->copy()->setTimezone('UTC')->format('Y-m-d H:i:s');
                $attendance->clock_in_ip_address = $ipAddress;
                $attendance->clock_in_time = $details['time'];
                $attendance->office_clock_in_time = $details['office_clock_in_time'];
                $attendance->office_clock_out_time = $details['office_clock_out_time'];
                $attendance->is_paid = 1;
                $attendance->is_late = $isLate;
                $attendance->save();

                $newDetails = CommonHrm::getTodayAttendanceDetails();
                $newDetails['ip_address'] = $ipAddress;

                return ApiResponse::make('Clocked In Successfully', $newDetails);
            }
        } else if ($attendanceType == 'clock-out') {
            if ($details['is_clocked_in'] == false) {
                throw new ApiException("You have not clocked in");
            } else if ($details['is_clocked_out']) {
                throw new ApiException("Already Clocked Out");
            } else if ($details['office_hours_expired']) {
                throw new ApiException("Office Hours Expired");
            } else {
                $currentDateTimeObject = Carbon::now();

                $attendance = Attendance::where('date', $details['date'])
                    ->where('user_id', $user->id)
                    ->first();

                if ($attendance) {
                    $attendance->clock_out_date_time = $currentDateTimeObject->format('Y-m-d H:i:s');
                    $attendance->clock_out_ip_address = $ipAddress;
                    $attendance->clock_out_time = $details['time'];

                    if ($attendance->clock_in_time && $attendance->clock_out_time) {
                        $totalMinutes = CommonHrm::getMinutesFromTimes($attendance->clock_in_time, $attendance->clock_out_time);
                        $attendance->total_duration = $totalMinutes;
                    }

                    $attendance->save();
                }

                $newDetails = CommonHrm::getTodayAttendanceDetails();
                $newDetails['ip_address'] = $ipAddress;

                return ApiResponse::make('Clocked Out Successfully', $newDetails);
            }
        }
    }

    public function dashboard()
    {
    }

    public function pendingLeaves()
    {
        $loggedUser = user();

        if (!$loggedUser->ability('admin', 'leaves_approve_reject')) {
            throw new ApiException("Not have valid permission");
        }

        $allPendingLeaves = Leave::select('id', 'user_id', 'status', 'reason')->with(['user:id,name,profile_image'])->where('status', 'pending')->get();

        return ApiResponse::make('Details Fetched Successfully', [
            'pending_leaves' => $allPendingLeaves
        ]);
    }

    public function todayAttendanceUsers()
    {
        $company = company();
        $allUsersLists = [];
        $allUsers = StaffMember::select('id', 'name')->get();

        $currentDateTimeObject = Carbon::now($company->timezone);
        $currentTime = $currentDateTimeObject->copy()->format('H:i:s');
        $currentDate = $currentDateTimeObject->copy()->format('Y-m-d');

        $todayHoliday = Holiday::whereDate('date', $currentDate)->first();
        $isHoliday = $todayHoliday ? true : false;

        $allAttendances = Attendance::whereDate('attendances.date', $currentDate)
            ->get();

        foreach ($allUsers as $allUser) {
            $isAttendanceDataFound = $allAttendances->filter(function ($allAttendance) use ($currentDate, $allUser) {
                return $allAttendance->date->format('Y-m-d') == $currentDate && $allAttendance->user_id == $allUser->id;
            })->first();

            if ($isAttendanceDataFound) {
                if ($isAttendanceDataFound->is_leave) {
                    $status = 'absent';
                } else {
                    $status = 'present';
                }
            } else {
                $status = 'not_marked';
            }

            $allUsersLists[] = [
                'name' => $allUser->name,
                'status' => $status,
                'profile_image_url' => $allUser->profile_image_url,
            ];
        }

        return ApiResponse::make('Users Fetched Successfully', [
            'users' => $allUsersLists,
            'is_holiday' => $isHoliday,
            'holiday' => $todayHoliday,
        ]);
    }

    public function updateHrmSettings(UpdateHrmSettingsRequest $request)
    {
        $loggedUser = user();

        if (!$loggedUser->ability('admin', 'hrm_settings')) {
            throw new ApiException("Not have valid permission");
        }

        $company = Company::find($loggedUser->company_id);
        $company->leave_start_month = $request->leave_start_month;
        $company->clock_in_time = $request->clock_in_time;
        $company->clock_out_time = $request->clock_out_time;
        $company->late_mark_after = $request->late_mark_after;
        $company->self_clocking = $request->self_clocking;
        $company->early_clock_in_time = $request->early_clock_in_time;
        $company->allow_clock_out_till = $request->allow_clock_out_till;
        $company->allowed_ip_address = $request->allowed_ip_address;
        $company->save();

        // Reseting the company settings
        company(true);

        return ApiResponse::make('Success', []);
    }
}
