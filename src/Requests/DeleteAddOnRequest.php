<?php

namespace Just\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Just\Models\User;
use Just\Contracts\Requests\ValidateRequest;

class DeleteAddOnRequest extends FormRequest implements ValidateRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool {
        return User::authAsAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array {
        return [
            'id' => 'required|integer|min:1|exists:addons'
        ];
    }
}
