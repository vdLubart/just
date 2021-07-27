<?php

namespace Just\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;
use Just\Contracts\Requests\ValidateRequest;

class SavePasswordRequest extends FormRequest implements ValidateRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array {
        return [
            "current_password" => "required",
            "new_password" => "required|string|confirmed"
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param Validator $validator
     */
    public function withValidator(Validator $validator)
    {
        // checks user current password
        // before making changes
        $validator->after(function ($validator) {
            if (!Hash::check($this->current_password, \Auth::user()->password) ) {
                $validator->errors()->add('current_password', 'Current password is incorrect.');
            }
        });
    }
}
