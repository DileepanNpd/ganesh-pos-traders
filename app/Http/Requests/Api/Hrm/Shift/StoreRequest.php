<?php

namespace App\Http\Requests\Api\Hrm\Shift;

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
            'name' => 'required',
            'clock_in_time' => 'required',
            'clock_out_time' => 'required',
            'self_clocking' => 'required',
        ];

        return $rules;
    }
}
