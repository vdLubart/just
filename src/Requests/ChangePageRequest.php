<?php

namespace Lubart\Just\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Lubart\Just\Structure\Panel\Block\Contracts\ValidateRequest;

class ChangePageRequest extends FormRequest implements ValidateRequest
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
        $rules = [
            'layout_id' => 'required|integer|min:1'
        ];
        
        if(empty($this->page_id)){
            $rules['route'] = 'required|unique:routes,route';
        }
        
        return $rules;
    }
}
