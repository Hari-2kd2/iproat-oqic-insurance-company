<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdvanceDeductionRequest extends FormRequest
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
        if (isset($this->employee)) {
            $result = Employee::where('employee_id', $this->employee)->first();
            return [
                'employee_id' => 'required',
            ];
        }
        return [
            'advance_amount'             => 'required',
            'date_of_advance_given'      => 'required',
            'deduction_amouth_per_month' => 'required',
            'no_of_month_to_be_deducted' => 'required',
        ];

    }

    public function messages()
    {
        return [
            'advance_amount*.required'             => 'The advance amount field is required.',
            'date_of_advance_given*.required'      => 'The date field is required.',
            'deduction_amouth_per_month*.required' => 'The deduction amount field is required.',
            'no_of_month_to_be_deducted*.required' => 'The no of month field is required.',

        ];
    }
}
