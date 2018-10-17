<?php

namespace Lubart\Just\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeLayoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => "required",
            "class" => "required",
            'width' => "required|integer|min:980|max:1920"
        ];
    }
}
