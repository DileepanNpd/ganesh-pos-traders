<?php

namespace App\Http\Requests\Api\Hrm\Appreciation;

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
            'date' => 'required',
            'price_amount' => 'required',
            'user_id' => 'required',
            'award_id' => 'required',
            'description' => 'required',
        ];

        return $rules;
    }
}
