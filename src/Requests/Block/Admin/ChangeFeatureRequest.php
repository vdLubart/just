<?php

namespace Just\Requests\Block\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Just\Models\User;
use Just\Models\Blocks\Contracts\ValidateRequest;

class ChangeFeatureRequest extends FormRequest implements ValidateRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return User::authAsAdmin();
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
            "description" => "nullable",
            "link" => "nullable|string"
        ];
    }
}
