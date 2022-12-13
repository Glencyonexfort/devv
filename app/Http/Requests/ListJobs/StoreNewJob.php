<?php

namespace App\Http\Requests\ListJobs;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreNewJob extends CoreRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $rules = [];

        if (FormRequest::input('price_structure') == 'Fixed') {
            $rules['first_name'] = 'required';
            $rules['job_date'] = 'required';
            $rules['fixed_other_rate'] = 'required';
            $rules['goods_value'] = 'required';
            $rules['delivery_suburb'] = 'required';
            $rules['pickup_suburb'] = 'required';
            $rules['job_template'] = 'required';
        }
        elseif (FormRequest::input('price_structure') == 'Hourly') {
            $rules['first_name'] = 'required';
            $rules['job_date'] = 'required';
            $rules['hourly_rate'] = 'required';
            $rules['goods_value'] = 'required';
            $rules['delivery_suburb'] = 'required';
            $rules['pickup_suburb'] = 'required';
            $rules['job_template'] = 'required';
        }
        else{
            $rules['first_name'] = 'required';
            $rules['job_date'] = 'required';
            $rules['goods_value'] = 'required';
            $rules['delivery_suburb'] = 'required';
            $rules['pickup_suburb'] = 'required';
            $rules['job_template'] = 'required';
        }

        return $rules;
    }

}
