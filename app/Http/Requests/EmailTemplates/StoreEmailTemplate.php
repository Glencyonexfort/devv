<?php

namespace App\Http\Requests\EmailTemplates;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmailTemplate extends CoreRequest
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
        return [
            "email_template_name" => "required",
//            "email" => "required|email|unique:users",
//            "password" => "required|min:6",
//            'slack_username' => 'nullable|unique:employee_details,slack_username'
        ];
    }
}
