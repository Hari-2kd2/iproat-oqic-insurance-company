<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveOverTimeRequest extends FormRequest
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
            'finger_print_id' => 'required',
            'date' => 'required',
            'actual_overtime' => 'required',
            'approved_overtime' => 'required',
            'remark' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'finger_print_id.required' => 'Employee Finger Id field is required.',
            'date.required' => 'The  date field is required.',
            'actual_overtime.required' => 'The Actual OverTime field is required.',
            'approved_overtime.required' => 'The Approved OverTime field is required.',
            'remark.required' => 'The Remark field is required.',
        ];
    }
}
