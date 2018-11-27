<?php

namespace Lubart\Just\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeatureChangeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "id" => "integer|min:1|nullable",
            "icon" => "required|integer|min:1",
            "title" => "required",
            "description" => "nullable",
            "link" => "nullable"
        ];
    }
}
