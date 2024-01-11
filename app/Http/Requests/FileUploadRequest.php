<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
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
            'select_file' => 'required|max:1000|mimes:xlsx',
        ];
    }

    public function messages()
    {
        return [
            'select_file.required' => 'Please choose the file first.',
            'select_file.mimes' => 'The File Format is Invalid (ie: xlsx).',
        ];
    }
}
