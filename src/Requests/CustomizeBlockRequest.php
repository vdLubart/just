<?php
/**
 * @author Viacheslav Dymarchuk
 */

namespace Just\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Just\Models\Block;
use Just\Models\Blocks\Contracts\ValidateRequest;
use Just\Models\User;

class CustomizeBlockRequest extends FormRequest implements ValidateRequest{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return User::authAsAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $block = Block::find($this->block_id);

        $blockId = is_null($block) ? 0 : $block->id;

        return [
            'block_id' => 'nullable|integer|min:1',
            'type' => 'required_without:block_id|exists:blockList,block',
            'name' => [
                'nullable',
                Rule::unique('blocks')->ignore($blockId)
            ],
            'panelLocation' => 'nullable|exists:panels,location',
            'page_id' => 'nullable|exists:pages,id',
            // todo: remove nullable after removing layout type
            'width' => 'nullable|integer|min:0|max:12', // field is nullable because it does not used in grid layouts
        ];
    }
}