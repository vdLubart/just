<?php

namespace Lubart\Just\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeLinkRequest extends FormRequest
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
        return [
            'block_id' => 'required|integer|min:1',
            'linkedBlock_id' => 'required|integer|min:1',
        ];
    }
}
