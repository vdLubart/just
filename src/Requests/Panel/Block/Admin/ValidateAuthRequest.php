<?php

namespace Lubart\Just\Requests\Panel\Block\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Lubart\Just\Structure\Panel\Block\Contracts\ValidateRequest;

abstract class ValidateAuthRequest extends FormRequest implements ValidateRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
