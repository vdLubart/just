<?php

namespace Just\Requests\Panel\Block\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Just\Structure\Panel\Block\Contracts\ValidateRequest;

class ChangeArticleRequest extends FormRequest implements ValidateRequest
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
            "image" => "image|nullable",
            "subject" => "required|string|max:255",
            "summary" => "string|nullable|max:1000",
            "text" => "required|string"
        ];
    }
}
