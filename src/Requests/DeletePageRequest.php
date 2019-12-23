<?php

namespace Just\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Just\Models\User;
use Just\Structure\Panel\Block\Contracts\ValidateRequest;

class DeletePageRequest extends FormRequest implements ValidateRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return User::canAccessAdminPanel();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|integer|min:1|exists:pages'
        ];
    }
}