<?php

namespace App\Http\Controllers\Api\Hrm;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Hrm\Department\IndexRequest;
use App\Http\Requests\Api\Hrm\Department\StoreRequest;
use App\Http\Requests\Api\Hrm\Department\UpdateRequest;
use App\Http\Requests\Api\Hrm\Department\DeleteRequest;
use App\Models\Hrm\Department;

class DepartmentController extends ApiBaseController
{
    protected $model = Department::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function storing($departments)
    {
        $loggedUser = user();

        $departments->created_by = $loggedUser->id;

        return $departments;
    }
}
