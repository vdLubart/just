<?php

namespace Just\Requests\Block\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Just\Models\User;
use Just\Contracts\Requests\ValidateRequest;

class SaveArticleRequest extends FormRequest implements ValidateRequest
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
            'id' => 'nullable|integer|exists:articles',
            'block_id' => 'required|integer|exists:blocks,id',
            "image" => "image|nullable",
            "subject" => "required|max:255",
            "summary" => "nullable|max:1000",
            "text" => "required"
        ];
    }
}
