<?php

namespace Just\Requests\Block\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Just\Models\User;
use Just\Models\Blocks\Contracts\ValidateRequest;

class ChangeArticleRequest extends FormRequest implements ValidateRequest
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
            "image" => "image|nullable",
            "subject" => "required|max:255",
            "summary" => "nullable|max:1000",
            "text" => "required"
        ];
    }
}
