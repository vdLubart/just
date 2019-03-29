<?php

namespace Lubart\Just\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteLayoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::user()->role == "master";
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return ['layout_id'=>"integer|min:1"];
    }
}
