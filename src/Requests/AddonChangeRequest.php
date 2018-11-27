<?php

namespace Lubart\Just\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lubart\Just\Structure\Panel\Block\Addon;
use Lubart\Just\Structure\Panel\Block;

class AddonChangeRequest extends FormRequest
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
        if(empty($this->addon_id)){
            $block = Block::find($this->block_id)->specify();
            
            $rules = [
                "type" => "required|string",
                "block_id" => "required|integer|min:1",
            ];
        }
        else{
            $block = Addon::find($this->addon_id)->block->specify();
            $rules = [];
        }
        
        $addonNames = $block->addons()->where('id', '<>', $this->addon_id ?? 0)->pluck('name')->toArray();
        // cannot just combine arrays because they have the same keys
        $usedNames = array_keys($block->getAttributes());
        foreach($addonNames as $name){
            $usedNames[] = $name;
        }
        
        $rules += [
            "addon_id" => "integer|min:1|nullable",
            "name" => [
                "required",
                "string",
                Rule::notIn( $usedNames )
                ],
            "title" => "required|string",
            "description" => "nullable"
        ];
        
        return $rules;
    }
    
    public function messages() {
        return [
            'name.not_in' => 'Name is already in use'
        ];
    }
}