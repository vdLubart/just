<?php

namespace Just\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Just\Models\User;
use Just\Contracts\Requests\ValidateRequest;

class InitializeLayoutRequest extends FormRequest implements ValidateRequest
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
        return [
            'layout_id'=>"integer|min:1|exists:layouts,id"
        ];
    }
}
