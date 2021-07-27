<?php

namespace Just\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Just\Contracts\Requests\ValidateRequest;
use Just\Models\User;

class SaveUserRequest extends FormRequest implements ValidateRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool {
        return User::authAsMaster();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array {
        if(!$this->user_id){
            return [
                "name" => "required|string|unique:users,name",
                "email" => "required|string|unique:users,email",
                "role" => "required|string|exists:roles,role",
                "password" => "required|string|confirmed"
            ];
        }
        else{
            return [
                "user_id" => "required|integer|min:1|nullable",
                "name" => [
                    "required",
                    "string",
                    Rule::unique('users')->ignore($this->name, 'name')
                ],
                "email" => [
                    "required",
                    "string",
                    Rule::unique('users')->ignore($this->email, 'email')
                ],
                "role" => "required|string|exists:roles,role"
            ];
        }
    }
}
