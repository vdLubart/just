<?php

namespace Lubart\Just\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SlideTitleChangeRequest extends FormRequest
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
            "id" => "integer|min:1",
            "subid" => "integer|min:0",
            "type" => "required|in:title,note,button",
            "title" => "required"
        ];
    }
}
