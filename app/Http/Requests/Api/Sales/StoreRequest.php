<?php

namespace App\Http\Requests\Api\Sales;

use Illuminate\Validation\Rule;
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
        $company = company();
        $loggedUser = auth('api')->user();

        $rules = [
            'user_id' => 'required',
            'order_status' => 'required',
            'product_items'    => 'required',
            'order_date'    => 'required',
        ];

        // if ($this->has('pay_object') && count($this->pay_object) > 0) {
        //     dd($this->pay_object);
        //     foreach ($this->pay_object as $object) {
        //         if ($object['payment_mode_id'] == "") {
        //             $rules = [
        //                 'payment_mode_id' => 'required',
        //             ];
        //         }
        //         // dd( $object['payment_mode_id']);
        //     }
        // }

        if ($this->invoice_number && $this->invoice_number != '') {
            $rules['invoice_number'] = [
                'required',
                Rule::unique('orders', 'invoice_number')->where(function ($query) use ($company) {
                    return $query->where('company_id', $company->id);
                })
            ];
        }

        return $rules;
    }
}
