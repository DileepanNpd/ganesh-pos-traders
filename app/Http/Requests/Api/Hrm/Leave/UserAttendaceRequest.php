<?php

namespace App\Http\Requests\Api\Hrm\Leave;

use Illuminate\Foundation\Http\FormRequest;

class UserAttendaceRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$rules = [
			'department_id' => 'required',
			'employee_id' => 'required',
			'clock_in' => 'required',
			'clock_out' => 'required',
		];

		if ($this->half_day == 1) {
			$rules['leave_type'] = 'required';
		}

		if ($this->mark_attendance == 'month') {
			$rules['attendance_month'] = 'required';
		} else {
			$rules['attendance_date'] = 'required';
		}

		return $rules;
	}
}
