<?php

namespace Just\Requests\Block\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Just\Contracts\Requests\ValidateRequest;
use Just\Models\User;

class SaveFeedbackRequest extends FormRequest implements ValidateRequest
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
            'id' => 'nullable|integer|exists:feedbacks',
            'block_id' => 'required|integer|exists:blocks,id',
            "username" => "required",
            "email" => "required|email",
            "message" => "required|max:1024",
            "created" => "date"
        ];
    }
}
