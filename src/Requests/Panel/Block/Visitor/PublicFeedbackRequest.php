<?php

namespace Lubart\Just\Requests\Panel\Block\Visitor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lubart\Just\Structure\Panel\Block\Contracts\ValidateRequest;

class PublicFeedbackRequest extends FormRequest implements ValidateRequest
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

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages() {
        return [
            'g-recaptcha-response.required' => __('feedback.validation.recaptchaFailed'),
            'g-recaptcha-response.recaptcha' => __('feedback.validation.recaptchaFailed')
        ];
    }
}
