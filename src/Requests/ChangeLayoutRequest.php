<?php

namespace Just\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Just\Models\User;
use Just\Structure\Panel\Block\Contracts\ValidateRequest;

class ChangeLayoutRequest extends FormRequest implements ValidateRequest
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
        $rules = [
            'layout_id' => 'nullable|integer|min:2',
            'name' => "required",
            "class" => "required",
            'width' => "required|integer|min:980|max:1920"
        ];
        
        if(empty($this->layout_id)){
            $rules['class'] = [
                "required",
                Rule::unique('layouts')->where(function($query){
                    return $query->where('name', $this->name)
                            ->where('class', $this->class);
                })
            ];
        }
        
        return $rules;
    }
    
    public function messages() {
        return parent::messages() + 
                [
                    'class.unique' => __('layout.messages.error.classInUse', ['class'=>$this->class, 'layout' => $this->name]),
                    'layout_id.min' => __('layout.messages.error.protectedLayout')
                ];
    }
}
