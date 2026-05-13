<?php

namespace App\Http\Controllers\Api\Hrm;

use App\Http\Controllers\ApiBaseController;
use App\Models\StaffMember;
use Examyou\RestAPI\Exceptions\ApiException;
use App\Http\Requests\Api\Hrm\Shift\IndexRequest;
use App\Http\Requests\Api\Hrm\Shift\StoreRequest;
use App\Http\Requests\Api\Hrm\Shift\UpdateRequest;
use App\Http\Requests\Api\Hrm\Shift\DeleteRequest;
use App\Models\Hrm\Shift;

class ShiftController extends ApiBaseController
{
    protected $model = Shift::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function destroying($shift)
    {
        $assignedShiftCount = StaffMember::where("users.shift_id", $shift->id)->count();

        if ($assignedShiftCount > 0) {
            throw new ApiException("Shift cann't be deleted its assigned to the staffMembers");
        }
        return $shift;
    }
}
