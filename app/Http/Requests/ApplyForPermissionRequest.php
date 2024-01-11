<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyForPermissionRequest extends FormRequest
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
            'employee_id'               => 'required',
            'permission_date'           => 'required', 
            'purpose'                   => 'required',
            'permission_duration'       => 'required',

        ];
    }

    public function messages()
    {
        return [
            'employee_id.required'                => 'The Employee field is required.',
            'permission_date.required'            => 'The date field is required.',
            'purpose.required'                    => 'The Purpose field is required.',
            'permission_duration.required'        => 'The Duration field is required.',
            
        ];
    }
}
