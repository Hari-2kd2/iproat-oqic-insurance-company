<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class DeviceConfigRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ip'       => Route::getFacadeRoot()->current()->uri() == "deviceConfigure/create" ? 'required|unique:device,ip' : 'required',
            'name'     => 'required',
            'protocol' => 'required',
            'port'     => 'required',
            'username' => 'required|min:3',
            'password' => 'required|min:6',
            //'model' => 'required',
            //'status' => 'required',
            // 'created_by' => 'required',
            // 'updated_by' => 'required',
        ];
    }
}
