<?php

namespace Just\Requests\Block\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Just\Models\User;
use Just\Contracts\Requests\ValidateRequest;

class SaveEventRequest extends FormRequest implements ValidateRequest
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
     */
    public function rules(): array {
        return [
            'id' => 'nullable|integer|exists:events',
            'block_id' => 'required|integer|exists:blocks,id',
            "image" => "image|nullable",
            "subject" => "required",
            "start_date" => "required|date|date_format:Y-m-d",
            "end_date" => "nullable|date|date_format:Y-m-d|after_or_equal:start_date",
            "start_time" => "nullable|regex:/\d{2}\:\d{2}/",
            "end_time" => "nullable|regex:/\d{2}\:\d{2}/",
            "location" => "nullable",
            "summary" => "nullable",
            "text" => "required"
        ];
    }
}
