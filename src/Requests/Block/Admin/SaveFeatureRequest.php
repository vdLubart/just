<?php

namespace Just\Requests\Block\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Just\Models\User;
use Just\Contracts\Requests\ValidateRequest;

class SaveFeatureRequest extends FormRequest implements ValidateRequest
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
            'id' => 'nullable|integer|exists:features',
            'block_id' => 'required|integer|exists:blocks,id',
            "icon" => "required|integer|min:1",
            "title" => "required",
            "description" => "nullable",
            "link" => "nullable|string"
        ];
    }
}
