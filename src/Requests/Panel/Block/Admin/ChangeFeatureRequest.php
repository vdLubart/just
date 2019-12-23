<?php

namespace Just\Requests\Panel\Block\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Just\Structure\Panel\Block\Contracts\ValidateRequest;

class ChangeFeatureRequest extends FormRequest implements ValidateRequest
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
            "icon" => "required|integer|min:1",
            "title" => "required",
            "description" => "nullable|string",
            "link" => "nullable|string"
        ];
    }
}
