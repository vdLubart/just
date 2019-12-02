<?php

namespace Lubart\Just\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Lubart\Just\Models\User;
use Lubart\Just\Structure\Panel\Block\Contracts\ValidateRequest;

class DeleteLayoutRequest extends FormRequest implements ValidateRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return User::authAsMaster();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'layout_id'=>"integer|min:1|exists:layouts,id"
        ];
    }
}
