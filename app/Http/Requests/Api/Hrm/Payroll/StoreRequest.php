<?php

namespace App\Http\Requests\Api\Hrm\Payroll;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
			'user_id' => 'required',
			'month' => 'required',
			'year' => 'required',
			'net_salary' => 'required',
			'payment_date' => 'required',
			'status' => 'required',
		];

		return $rules;
	}
}
