<?php

namespace App\Http\Requests\Api\Hrm\Payroll;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusRequest extends FormRequest
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
            'payroll_status' => 'required'
        ];

        if ($this->payroll_status == "paid") {
            $rules['date'] = 'required';
            $rules['payment_mode_id'] = 'required';
        }

        return $rules;
    }
}
