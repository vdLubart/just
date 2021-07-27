<?php

namespace Just\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Just\Models\Block;
use Just\Contracts\Requests\ValidateRequest;

class ChangeBlockRequest extends FormRequest implements ValidateRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array {
        $block = Block::find($this->block_id);

        $blockId = is_null($block) ? 0 : $block->id;

        return [
            'block_id' => 'nullable|integer|min:1',
            'type' => 'required_without:block_id|exists:blockList,block',
            'name' => [
                'nullable',
                'string',
                'regex:/[a-z_0-9]+/',
                Rule::unique('blocks')->ignore($blockId)
            ],
            'panelLocation' => 'nullable|exists:panels,location',
            'page_id' => 'nullable|exists:pages,id',
            'width' => 'integer|min:0|max:12'
        ];
    }
}
