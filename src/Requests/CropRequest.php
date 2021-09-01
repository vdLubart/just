<?php

namespace Just\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Just\Contracts\Requests\ValidateRequest;

class CropRequest extends FormRequest implements ValidateRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "id" => "required|integer|min:1",
            "x" => "required|integer|min:0",
            "y" => "required|integer|min:0",
            "w" => "required|integer|min:1",
            "h" => "required|integer|min:1",
            "img" => "required|regex:/[a-z0-9\/\.]+/",
        ];
    }
}
