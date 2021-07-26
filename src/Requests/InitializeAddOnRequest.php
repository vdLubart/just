<?php
/**
 * @author Viacheslav Dymarchuk
 */

namespace Just\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Just\Models\Blocks\Contracts\ValidateRequest;
use Just\Models\User;

class InitializeAddOnRequest extends FormRequest implements ValidateRequest{

    public function authorize(): bool {
        return User::authAsAdmin();
    }

    public function rules(): array {
        return [
            'id' => 'required|integer|exists:addons',
        ];
    }
}
