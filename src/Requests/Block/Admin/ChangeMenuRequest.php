<?php

namespace Just\Requests\Block\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Just\Contracts\Requests\ValidateRequest;

class ChangeMenuRequest extends FormRequest implements ValidateRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array {
        return [
            "id" => "integer|min:1|nullable",
            "item" => "required",
            "parent" => "integer|min:0",
            "route" => "nullable",
            "url" => "nullable"
        ];
    }
}
