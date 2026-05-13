<?php

namespace App\Http\Requests\Api\Hrm\PrePayment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'amount' => 'required',
            'notes' => 'required',
            'date_time' => 'required',
            'payment_mode_id' => 'required',
            'deduct_from_payroll' => 'required',
        ];

        if ($this->deduct_from_payroll == "0") {
            $rules['payroll_month'] = 'required';
            $rules['payroll_year'] = 'required';
        }
        return $rules;
    }
}
