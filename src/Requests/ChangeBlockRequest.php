<?php

namespace Lubart\Just\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeBlockRequest extends FormRequest
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
            'type' => 'nullable|exists:blockList,block',
            'name' => 'nullable|unique:blocks,name',
            'panelLocation' => 'nullable|exists:panels,location',
            'page_id' => 'nullable|exists:pages,id',
            'width' => 'required|integer|min:0|max:12'
        ];
    }
}
