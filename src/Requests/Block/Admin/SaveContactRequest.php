<?php

namespace Just\Requests\Block\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Just\Models\Block;
use Just\Models\User;
use Just\Contracts\Requests\ValidateRequest;

class SaveContactRequest extends FormRequest implements ValidateRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool {
        return User::authAsAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \Exception
     */
    public function rules(): array {
        $contact = Block::find($this->block_id)->specify()->item();
        $channels = $contact->allChannels();

        $rules = [
            'id' => 'nullable|integer|exists:contacts',
            'block_id' => 'required|integer|exists:blocks,id',
        ];

        foreach ($channels as $channel=>$label){
            $rules[$channel] = 'string';
        }

        $rules['at'] = ($rules['at']??"string") . "|email";
        $rules['facebook'] = ($rules['facebook']??"string") . "|url";
        $rules['youtube'] = ($rules['youtube']??"string") . "|url";
        $rules['linkedin'] = ($rules['linkedin']??"string") . "|url";
        $rules['github'] = ($rules['github']??"string") . "|url";
        $rules['instagram'] = ($rules['instagram']??"string") . "|url";
        $rules['pinterest'] = ($rules['pinterest']??"string") . "|url";
        $rules['reddit'] = ($rules['reddit']??"string") . "|url";
        $rules['soundcloud'] = ($rules['soundcloud']??"string") . "|url";

        return $rules;
    }
}
