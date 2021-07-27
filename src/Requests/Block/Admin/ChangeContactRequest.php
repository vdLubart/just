<?php

namespace Just\Requests\Block\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Just\Models\Block;
use Just\Models\User;
use Just\Contracts\Requests\ValidateRequest;

class ChangeContactRequest extends FormRequest implements ValidateRequest
{
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
        $contact = Block::find($this->block_id)->specify()->model();
        $channels = $contact->allChannels();

        $rules = [];

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
