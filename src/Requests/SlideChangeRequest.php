<?php

namespace Lubart\Just\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SlideChangeRequest extends FormRequest
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
            "slide" => "required|image",
            "block_id" => "required|integer|min:1",
            "id" => "integer|min:1|nullable",
        ];
    }
}
