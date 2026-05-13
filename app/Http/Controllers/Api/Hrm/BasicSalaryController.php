<?php

namespace App\Http\Controllers\Api\Hrm;

use App\Http\Controllers\ApiBaseController;
use Examyou\RestAPI\Exceptions\ApiException;
use App\Http\Requests\Api\Hrm\BasicSalary\IndexRequest;
use App\Http\Requests\Api\Hrm\BasicSalary\StoreRequest;
use App\Http\Requests\Api\Hrm\BasicSalary\UpdateRequest;
use App\Http\Requests\Api\Hrm\BasicSalary\DeleteRequest;
use App\Models\Hrm\BasicSalary;

class BasicSalaryController extends ApiBaseController
{
    protected $model = BasicSalary::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {
        $request = request();

        if ($request->has('user_id')) {
            $query = $query->where('basic_salaries.user_id', $this->getIdFromHash($request->user_id));
        }
        return  $query;
    }

    public function storing(BasicSalary $basicSalary)
    {
        $request = request();
        $userId = $this->getIdFromHash($request->user_id);
        $isBasicSalaryExists = BasicSalary::where('user_id', $userId)->first();

        if ($isBasicSalaryExists) {
            $basicSalary = $isBasicSalaryExists;

            $basicSalary->basic_salary = $request->basic_salary;
        }

        return $basicSalary;
    }

    public function updating(BasicSalary $basicSalary)
    {
        if ($basicSalary->isDirty('user_id')) {
            throw new ApiException('User cannot be changed');
        }

        return $basicSalary;
    }
}
