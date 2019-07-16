<?php

namespace Lubart\Just\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeEventRequest extends FormRequest
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
            "id" => "integer|min:1|nullable",
            "image" => "image|nullable",
            "subject" => "required",
            "start_date" => "required|date|date_format:Y-m-d",
            "end_date" => "nullable|date|date_format:Y-m-d|after_or_equal:start_date",
            "start_time" => "nullable|regex:/\d{2}\:\d{2}/",
            "end_time" => "nullable|regex:/\d{2}\:\d{2}/",
            "location" => "string",
            "summary" => "string",
            "text" => "string"
        ];
    }
}
