<?php

namespace Just\Requests\Block\Visitor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Just\Contracts\Requests\ValidateRequest;

class PublicEventRequest extends FormRequest implements ValidateRequest
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
            'event_id' => 'required|min:1|exists:events,id',
            'name' => 'required',
            'email' => [
                'required',
                Rule::unique('registrations')->where(function($query) {
                    return $query->where('event_id', $this->event_id);
                })
            ],
            'comment' => 'nullable|max:1000',
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
            'email.unique' => __('events.registerExistingEmail'),
            'g-recaptcha-response.required' => __('feedback.validation.recaptchaFailed'),
            'g-recaptcha-response.recaptcha' => __('feedback.validation.recaptchaFailed')
        ];
    }
}
