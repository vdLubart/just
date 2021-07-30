<?php

namespace Just\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Just\Contracts\Requests\ValidateRequest;
use Just\Models\User;
use stdClass;

/**
 * Class SaveAddOnCategoryOptionRequest
 * @package Just\Requests
 *
 * @property int $add_on_id
 * @property array|stdClass $option
 */
class SaveAddOnOptionRequest extends FormRequest implements ValidateRequest
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
            'id' => 'nullable|integer|exists:addonOptions',
            'add_on_id' => "required|integer|exists:addons,id",
            "option" => "required|json"
        ];
    }
}
