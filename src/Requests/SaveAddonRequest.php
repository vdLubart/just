<?php

namespace Just\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Just\Models\AddOn;
use Just\Models\Block;
use Just\Contracts\Requests\ValidateRequest;
use Just\Models\User;

class SaveAddonRequest extends FormRequest implements ValidateRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool {
        return User::authAsMaster();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array {
        if(empty($this->addon_id)){
            $block = Block::find($this->block_id)->specify();

            $rules = [
                "type" => "required|string",
                "block_id" => "required|integer|min:1",
            ];
        }
        else{
            $addon = Addon::find($this->addon_id);
            $block = $addon->block->specify();
            $rules = [];
        }

        $addonNames = $block->addons()->where('id', '<>', $this->addon_id ?? 0)->pluck('name')->toArray();
        // cannot just combine arrays because they have the same keys
        $usedNames = array_keys($block->getAttributes());

        if(isset($addon)){
            if(($key = array_search($addon->name, $usedNames)) !== false) {
                unset($usedNames[$key]);
            }
        }

        foreach($addonNames as $name){
            $usedNames[] = $name;
        }

        $rules += [
            "addon_id" => "integer|min:1|nullable",
            "name" => [
                "required",
                "string",
                "regex:/^[a-z]+[a-zA-Z0-9]*$/",
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
