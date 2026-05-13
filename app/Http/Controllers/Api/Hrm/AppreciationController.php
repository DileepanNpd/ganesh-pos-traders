<?php

namespace App\Http\Controllers\Api\Hrm;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Hrm\Appreciation\IndexRequest;
use App\Http\Requests\Api\Hrm\Appreciation\StoreRequest;
use App\Http\Requests\Api\Hrm\Appreciation\UpdateRequest;
use App\Http\Requests\Api\Hrm\Appreciation\DeleteRequest;
use App\Models\Hrm\Appreciation;

class AppreciationController extends ApiBaseController
{
    protected $model = Appreciation::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function modifyIndex($query)
    {
        $loggedUser = user();
        $request = request();

        if ($request->has('date') && $request->date != '') {
            $date = explode(',', $request->date);
            $startDate = $date[0];
            $endDate = $date[1];

            $query = $query->whereRaw('appreciations.date >= ?', [$startDate])
                ->whereRaw('appreciations.date <= ?', [$endDate]);
        }

        if ($loggedUser->ability('admin', 'appreciations_view')) {
            if ($request->has('user_id')) {
                $query->user_id = $query->where('appreciations.user_id', $this->getIdFromHash($request->user_id));
            }
        } else {
            $query->user_id = $query->where('appreciations.user_id', $loggedUser->id);
        }

        return $query;
    }

    public function storing($appreciation)
    {
        $loggedUser = user();

        $appreciation->created_by = $loggedUser->id;

        return $appreciation;
    }
}
