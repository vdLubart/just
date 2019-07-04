<?php

namespace Lubart\Just\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterInEventRequest extends FormRequest
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
                Rule::unique('registrations')->where(function($query){
                    return $query->where('event_id', $this->event_id);
                })
            ],
            'comment' => 'nullable|max:1000'
        ];
    }

    public function messages(){
        return [
            'email.unique' => 'This email is already registered in this event'
        ];
    }
}
