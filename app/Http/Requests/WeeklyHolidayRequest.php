<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WeeklyHolidayRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'day_name' => 'required',
            'month' => 'required',
            'employee_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'day_name.required' => 'The weekly holiday name field is required.',
            'employee_id.required' => 'Select any one of the employee name.',
            'day_name.unique' => 'The weekly holiday name has already been taken.',
        ];
    }
}
