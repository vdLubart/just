<?php

namespace Lubart\Just\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddFeedbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "username" => "required|max:100",
            "email" => "required|email|max:255",
            "message" => "required|max:1024",
            'g-recaptcha-response'=>'required|recaptcha'
        ];
    }
}
