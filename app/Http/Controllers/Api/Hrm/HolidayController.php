<?php

namespace App\Http\Controllers\Api\Hrm;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Hrm\Holiday\IndexRequest;
use App\Http\Requests\Api\Hrm\Holiday\StoreRequest;
use App\Http\Requests\Api\Hrm\Holiday\UpdateRequest;
use App\Http\Requests\Api\Hrm\Holiday\DeleteRequest;
use App\Http\Requests\Api\Hrm\Holiday\MarkHolidayRequest;

use App\Models\Hrm\Holiday;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use App\Models\Hrm\Leave;
use App\Models\Hrm\Attendance;

class HolidayController extends ApiBaseController
{
    protected $model = Holiday::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;
    protected $markHolidayRequest = MarkHolidayRequest::class;


    protected function modifyIndex($query)
    {
        $request = request();
        $user = user();

        // isWeekend Filters
        if ($request->has('holiday_type') && $request->holiday_type != "all") {
            $holidayType = $request->holiday_type == 'holiday' ? 0 : 1;
            $query = $query->where('holidays.is_weekend', $holidayType);
        };

        if ($request->has('month')) {
            $query = $query->whereMonth('date', $request->month);
        }

        if ($request->has('year')) {
            $query = $query->whereYear('date', $request->year);
        }

        return  $query;
    }

    public function storing($holiday)
    {
        $loggedUser = user();

        $holiday->created_by = $loggedUser->id;
        $holiday = $this->storingAndUpdating($holiday);

        return $holiday;
    }

    public function updating($holiday)
    {
        $holiday = $this->storingAndUpdating($holiday);

        return $holiday;
    }

    public function storingAndUpdating($holiday)
    {
        $request = request();

        $holidayFound = Holiday::whereDate('date', $request->date)->first();

        if ($holidayFound) {
            $holiday = $holidayFound;
        }

        $holiday->name = $request->name;
        $holiday->year = Carbon::createFromFormat('Y-m-d', $request->date)->year;
        $holiday->month = Carbon::createFromFormat('Y-m-d', $request->date)->month;

        return $holiday;
    }

    public function markHoliday(MarkHolidayRequest $markHolidayRequest)
    {
        $loggedUser = user();

        if (!$loggedUser->ability('admin', 'mark_weekend_holiday')) {
            throw new ApiException("Not have valid permission");
        }

        $request = request();

        $startDate = $request->date_from;
        $endDate = $request->date_to;
        $markDayName = $request->name;

        $periods = CarbonPeriod::create($startDate, $endDate);
        $dates = [];

        // Iterate over the period
        foreach ($periods as $period) {
            $date = $period->format('Y-m-d');
            $dayName = $period->format('l');
            $dates[] = $dayName;

            if (in_array($dayName, $markDayName)) {

                // Check if holiday exists
                $newHoliday = Holiday::whereDate('date', $date)->first();

                if (!$newHoliday) {
                    $newHoliday = new Holiday();
                }
                $newHoliday->date = $date;
                $newHoliday->name = $request->ocassion_name;
                $newHoliday->year = $period->format('Y');
                $newHoliday->month = $period->format('m');
                $newHoliday->is_weekend = 1;
                $newHoliday->save();
            }
        }

        return ApiResponse::make('Holiday Successfully', []);
    }
}
